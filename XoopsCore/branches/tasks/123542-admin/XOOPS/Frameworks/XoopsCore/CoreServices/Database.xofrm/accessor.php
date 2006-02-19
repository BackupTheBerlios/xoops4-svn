<?php
/**
 * xoops_db_Accessor main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_db
 * @subpackage	xoops_db_Accessor
 * @version		$Id$
 * @devstatus	unstable
 */

XOS::import( 'xoops_db_Database' );

/**
 * Database accessor component
 * 
 * This class provides a standard interface for database accessors (objects meant to perform a
 * defined set of statements).
 * 
 * It may be extended as-is (by low-level components like the tree or preferences management
 * classes), but XOOPS will provide modules developers with higher-level data access mechanisms
 * for retrieving/storing model entities in the future.
 */
class xoops_db_Accessor {
	/**
	 * The database connection that will be used to perform statements
	 * @var xoops_db_Database
	 */
	var $db = false;
	/**
	 * List of tables this object will access
	 * 
	 * This property contains the accessed tables names in a <var>realname</var> => <var>prefixedname</var>
	 * hash. If it originally contains a regular array upon instanciation, the initialization routine will
	 * take care of generating the hash automatically.
	 * <code>
	 * $db =& XOS::create( 'xoops_db_Database', array( 'prefix' => 'tmp' ) );
	 * $accessor = XOS::create( 'xoops_db_Accessor', array(
	 *     'tables' => array( 'stuff', 'thing' ),
	 * ) );
	 * var_export( $accessor->tables );
	 * // Outputs:
	 * // array(
	 * //     'stuff' => 'tmp_stuff',
	 * //     'thing' => 'tmp_thing',
	 * // )
	 * </code>
	 * @var xoops_db_Statement[]
	 */
	var $tables = array();
	/**
	 * List of statements this object can perform (indexed by name)
	 * 
	 * <var>$queries</var> contains this accessor statements. Each entry can be either a string
	 * containing a valid SQL statement, or a {@link xoops_db_Statement} instance. When
	 * this accessor will be asked to execute a string statement for the first time, it will
	 * automatically replace the corresponding entry in this array with a statement object instance.
	 * @var array
	 */
	var $queries = array();
	
	/**#@+ @tasktype 10 Initialization*/
	/**
	 * Initializes the accessor instance
	 */
	function xoInit( $options = array() ) {
		// If no db specified, automatically connect to the system db
		if ( !$this->db ) {
			$this->db =& XOS::create( 'xoops_db_Database' );
		}
		if ( isset( $this->tables[0] ) ) {
			$this->tables = array_combine( $this->tables, array_map( array( &$this->db, 'prefix' ), $this->tables ) );
		}
		return true;
	}
	/**#@-*/
	

	/**
	 * Prepares the specified statement if the $queries entry is not already a statement instance
	 * @param string $name Name of the statement to prepare
	 * @return bool
	 */
	function prepareStatement( $name, $params = array() ) {
		static $patterns = null;

		if ( !@$this->queries[$name] ) {
			trigger_error( "Cannot execute unknown/invalid query $name", E_USER_WARNING );
			return false;
		}
		if ( !is_object( $this->queries[$name] ) ) {
			if ( !isset( $patterns ) ) {
				$patterns = array();
				// Replace the real table names with their prefixed equivalent
				foreach ( $this->tables as $k => $v ) {
					$patterns[] = '{' . $k . '}';
				}
			}
			$this->queries[$name] = $this->db->prepare( str_replace( $patterns, $this->tables, $this->queries[$name] ) );
		}
		if ( !empty( $params ) ) {
			if ( isset( $this->parameters[$name] ) ) {
				foreach ( $params as $k => $v ) {
					$this->queries[$name]->bindValue( is_int($k)?($k+1):$k, $v, $this->parameters[$name][$k] );
				}
			} else {
				foreach ( $params as $k => $v ) {
					$this->queries[$name]->bindValue( is_int($k)?($k+1):$k, $v );
				}
			}
		}
		return true;
	}

	/**
	 * Executes the specified statement and returns the number of affected rows
	 * 
	 * exec() returns the number of rows that were modified or deleted by the 
	 * statement you issued. If no rows were affected, it returns 0.
	 * 
	 * @param string $name The name of the SQL statement to prepare and execute
	 * @param array $params Values to bind to the statement parameters before execution
	 * @see http://fr3.php.net/manual/en/function.PDO-exec.php PDO::exec()
	 */
	function exec( $name, $params = array() ) {
		if ( $this->prepareStatement( $name, $params ) ) {
			if ( $this->queries[$name]->execute() ) {
				$this->queries[$name]->closeCursor();
				return $this->queries[$name]->rowCount();
			}
		}
		return false;
	}
	/**
	 * Executes the specified statement and returns all the rows of the result set
	 * 
	 * fetchAll() is a convenience function that encapsulates the statement preparation,
	 * parameters binding, execution and result retrieval.
	 * 
	 * @param string $name The name of the SQL statement to prepare and execute
	 * @param array $params Values to bind to the statement parameters before execution
	 * @param int $fetchMode Controls the contents of the returned array. Defaults to PDO::FETCH_BOTH. 
	 * @see http://www.php.net/manual/en/function.pdostatement-fetch.php PDOStatement::fetch()
	 */
	function fetchAll( $name, $params = array(), $fetchMode = PDO_FETCH_ASSOC ) {
		if ( $this->prepareStatement( $name, $params ) ) {
			if ( $this->queries[$name]->execute() ) {
				$res = $this->queries[$name]->fetchAll( $fetchMode );
				$this->queries[$name]->closeCursor();
				return $res;
			}
		}
		return false;
	}
	
	
	function execQuery( $name, $params = array(), $fetchMode = PDO_FETCH_ASSOC ) {
		if ( !$this->createStatement( $name ) ) {
			return false;
		}
		$this->bindValues( $name, $params );
		
		$this->queries[$name]->execute();
		if ( $rows = $this->queries[$name]->fetchAll( $fetchMode ) ) {
			$this->queries[$name]->closeCursor();
		}
		return $rows;
	}

	
	
	
	
	
}



























?>