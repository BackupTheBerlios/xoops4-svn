<?php
/**
 * Mysql xoops_db_Database driver class definition
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_db
 * @subpackage	xoops_db_Database
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'xoops_db_Database' );

if ( !defined( 'PDO_PARAM_INT' ) ) {
	define( 'PDO_PARAM_NULL', 0 );
	define( 'PDO_PARAM_INT', 1 );
	define( 'PDO_PARAM_STR', 2 );
	define( 'PDO_PARAM_LOB', 3 );
	define( 'PDO_PARAM_STMT', 4 );
	define( 'PDO_PARAM_BOOL', 5 );

	define( 'PDO_FETCH_LAZY', 1);
	define( 'PDO_FETCH_ASSOC', 2);
	define( 'PDO_FETCH_NUM', 3);
	define( 'PDO_FETCH_BOTH', 4);
	define( 'PDO_FETCH_OBJ', 5);
	define( 'PDO_FETCH_BOUND', 6);
	define( 'PDO_FETCH_COLUMN', 7);
	define( 'PDO_FETCH_CLASS', 8);
	define( 'PDO_FETCH_INTO', 9);
	define( 'PDO_FETCH_FUNC', 10);
	define( 'PDO_FETCH_NAMED', 11);
	define( 'PDO_FETCH_GROUP', 0x10000 );
	define( 'PDO_FETCH_UNIQUE', 0x30000 );
	define( 'PDO_FETCH_CLASSTYPE', 0x40000 );
	define( 'PDO_FETCH_SERIALIZE', 0x80000 );
	
	// PDO class attributes
	define( 'PDO_ATTR_PERSISTENT', 12 );
	define( 'PDO_ATTR_STATEMENT_CLASS', 13 );
	define( 'PDO_MYSQL_ATTR_USE_BUFFERED_QUERY', 1000 );
}



/**
 * Legacy xoops_db_Database driver main class
 */
class xoops_db_Database_mysql extends xoops_db_Database {
	/**
	 * Link identifier to the connected database
	 * @var resource
	 */
	var $conn = false;

	/**
	 * This instance attributes
	 * @var array
	 * @access protected
	 */
	var $attributes = array(
		PDO_ATTR_PERSISTENT => 0,
		PDO_ATTR_STATEMENT_CLASS => array( 'xoops_db_Statement_mysql', array() ),
		PDO_MYSQL_ATTR_USE_BUFFERED_QUERY => false,
	);
	
	/**
	 * connect to the database
	 * 
     * @param bool $selectdb select the database now ?
     * @return bool successful?
	 */
	function connect( $selectdb = true ) {
		if ( $this->persistent ) {
			$this->conn = @mysql_pconnect( $this->host, $this->user, $this->password );
		} else {
			$this->conn = @mysql_connect( $this->host, $this->user, $this->password );
		}
		if ( !$this->conn ) {
			$this->logError();
			return false;
		}
		if( $selectdb && !mysql_select_db( $this->dbname ) ) {
			$this->logError();
			return false;
		}
		return true;
	}
	// --------- PDO implementation ---------
	/**
	 * Initiates a transaction
	 *
	 * NB: This driver doesn't support transactions and always stays in autocommit mode
	 * @return boolean
	 */	
	function beginTransation() {
		return true;
	}
	/**
	 * Commits a transaction
	 *
	 * NB: This driver doesn't support transactions and always stays in autocommit mode
	 * @return boolean
	 */	
	function commit() {
		return true;
	}
	/**
	 * Fetch extended error information associated with the last operation on the database handle 
	 * 
	 * <p>errorInfo() returns an array of error information about the last operation performed 
	 * by this database handle. The array consists of the following fields:<br />
	 * 0: SQLSTATE error code (a five-character alphanumeric identifier defined in the ANSI SQL standard).<br />
	 * 1: Driver-specific error code
	 * 2: Driver-specific error message</p>
	 * 
	 * <p class="note">This implementation doesn't support SQLSTATE (it will always be HY000)</p>
	 */
	function errorInfo() {
		return array( 'HY000', @mysql_errno( $this->conn ), @mysql_error( $this->conn ) );
	}	
	/**
	 * Fetch the SQLSTATE associated with the last operation on the database handle 
	 */
	function errorCode() {
		return 'HY000';
	}
	/**
	 * Execute an SQL statement and return the number of affected rows
	 * 
	 * PDO::exec() does not return results from a SELECT statement. For a SELECT statement that 
	 * you only need to issue once during your program, consider issuing PDO::query().
	 * For a statement that you need to issue multiple times, prepare a PDOStatement object 
	 * with PDO::prepare() and issue the statement with PDOStatement::execute().
	 * 
	 * @param string $statement The SQL statement to prepare and execute
	 */
	function exec( $statement ) {
		if ( $this->allowWebChanges ) {
			return mysql_query( $statement, $this->conn ) ? mysql_affected_rows( $this->conn ) : false;
		} else {
			trigger_error( 'Database updates are not allowed during processing of a GET request', E_USER_WARNING );
			return false;
		}
	}
	/**
	 * Returns the ID of the last inserted row
	 */
	function lastInsertId( $name = '' ) {
		return mysql_insert_id( $this->conn );
	}
	/**
	 * Prepares a statement for execution and returns a statement object
	 * 
	 * <p>
	 * Prepares an SQL statement to be executed by the PDOStatement::execute() method.
	 * The SQL statement can contain zero or more named (:name) or question mark (?) 
	 * parameter markers for which real values will be substituted when the statement 
	 * is executed. You cannot use both named and question mark parameter markers within 
	 * the same SQL statement; pick one or the other parameter style.
	 * </p>
	 * @param string $statement This must be a valid SQL statement for the target database server.
	 * @param array $driverOptions This array holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns.
	 * @return object A PDOStatement object, if the database server successfully prepares the statement.
	 */
	function prepare( $statement, $driverOptions = false ) {
		return XOS::create( 'xoops_db_Statement_mysql', array(
			'db' => &$this,
			'query' => $statement,
			'driverOptions' => $driverOptions
		) );	
	}
	/**
	 * Executes an SQL statement, returning a result set as a PDOStatement object
	 * 
	 *<p> PDO::query() executes an SQL statement in a single function call, returning 
	 * the result set (if any) returned by the statement as a PDOStatement object.</p>
	 * <p>For a query that you need to issue multiple times, you will realize better 
	 * performance if you prepare a PDOStatement object using PDO::prepare() and issue 
	 * the statement with multiple calls to PDOStatement::execute().</p>
	 * <p>If you do not fetch all of the data in a result set before issuing your next call
	 * to PDO::query(), your call may fail. Call PDOStatement::closeCursor() to release the 
	 * database resources associated with the PDOStatement object before issuing your next 
	 * call to PDO::query().</p>
	 * @param string $statement The valid SQL statement to prepare and execute.
	 * @return object
	 */
	function query( $statement ) {
		$stmt = XOS::create( 'xoops_db_Statement_mysql', array(
			'db' => &$this,
			'query' => $statement,
			'driverOptions' => $driverOptions
		) );
		if ( $stmt ) {
			$stmt->execute();
			return $stmt;
		}
		return false;
	}
	/**
	 * Quotes a string for use in a query.
	 * 
	 * PDO::quote() places quotes around the input string (if required) and escapes special 
	 * characters within the input string, using a quoting style appropriate to the underlying driver. 
	 */
	function quote( $string ) {
		return "'" . mysql_real_escape_string( $string, $this->conn ) . "'";
	}
	/**
	 * Rolls back a transaction
	 */
	function rollBack() {
		return true;
	}

	
}


class xoops_db_Statement_mysql {

	var $query = '';
	var $preparedQuery = '';

	var $db = false;
	var $result = false;
	
	/**
	 * Internal lookup table for parameters bindings
	 * @var array
	 * @access protected
	 */
	var $paramBindings = array();
	/**
	 * Internal lookup table for columns bindings
	 * @var array
	 * @access protected
	 */
	var $columnBindings = array();
	/**
	 * Current fetch mode
	 *
	 * @var int
	 * @access protected
	 */
	var $fetchMode;
	/**
	 * Class / instance for PDO_FETCH_CLASS / PDO_FETCH_INTO
	 * @access protected
	 */
	var $fetchTarget = false;
	
	function xoInit( $options = array() ) {
		if ( strpos( $this->query, '?' ) !== false ) {
			$this->preparedQuery = preg_replace( '/(\\?)/', '{__p$1}', $this->query );
		} else {
			$this->preparedQuery = preg_replace( '/:([a-zA-Z_]*)/', '{$1}', $this->query );
		}
		return true;
	}
	
	/**
	 * Bind a column to a PHP variable
	 *
	 * @param mixed $column
	 * @param mixed $variable
	 * @param int $type
	 */
	function bindColumn( $column, &$variable, $type ) {
		$this->columnBindings[ intval($column) ? (int)$column : substr($column, 1) ] = array( &$variable, $type );
	}
	/**
	 * Binds a parameter to the specified variable name
	 * @param mixed $param
	 * @param mixed $variable
	 * @param int $type
	 * @param int $length
	 * @param mixed $options
	 */
	function bindParam( $param, &$variable, $type = 0, $length = 0, $options = array() ) {
		$param = intval($param) ? "__p$param" : substr($param,1);
		$this->paramBindings[$param] = array( &$variable, $type, $length, $options );
	}
	/**
	 * Bind a value to a parameter
	 * @param mixed $param
	 * @param mixed $value
	 * @param int $type
	 */
	function bindValue( $param, $value, $type ) {
		$param = intval($param) ? "__p$param" : substr($param,1);
		$this->paramBindings[$param] = array( $value, $type );
	}
	/**
	 * Closes the cursor, enabling the statement to be executed again
	 */	
	function closeCursor() {
		if ( $this->result ) {
			mysql_free_result( $this->result );
			$this->result = false;
		}
		return true;
	}	
	/**
	 * Returns the number of columns in the result set
	 * @return integer
	 */
	function columnCount() {
		return $this->result ? mysql_num_fields( $this->result ) : 0;
	}
	/**
	 * Executes a prepared statement
	 */
	function execute( $params = array() ) {
		$search = array();
		$replace = array();
		foreach ( $this->paramBindings as $k => $v ) {
			$search[] = '{' . $k . '}';
			$replace[] = $this->castValue( $v[0], $v[1] );
		}
		$sql = str_replace( $search, $replace, $this->preparedQuery );
		echo $sql;
		$this->result = mysql_unbuffered_query( $sql, $this->db->conn );
		return (bool)$this->result;
	}
	
	function fetch( $mode = null, $orientation = null, $offset = null ) {
		if ( !$this->result ) {
			return false;
		}
		if ( !$mode ) {
			$mode = $this->fetchMode;
		}
		switch ( $mode ) {
		case PDO_FETCH_ASSOC:
			return mysql_fetch_array( $this->result, MYSQL_ASSOC );
		case PDO_FETCH_BOTH:
			return mysql_fetch_array( $this->result, MYSQL_BOTH );
		case PDO_FETCH_NUM:
			return mysql_fetch_array( $this->result, MYSQL_NUM );
		case PDO_FETCH_LAZY:
		case PDO_FETCH_OBJ:
			if ( $props = mysql_fetch_array( $this->result, MYSQL_ASSOC ) ) {
				$target = new stdClass();
				XOS::apply( $target, $props );
				return $target;
			}
			return false;
		case PDO_FETCH_INTO:
			if ( $props = mysql_fetch_array( $this->result, MYSQL_ASSOC ) ) {
				XOS::apply( $this->fetchTarget, $props );
				return $this->fetchTarget;
			}
			return false;
		case PDO_FETCH_CLASS:
			if ( $props = mysql_fetch_array( $this->result, MYSQL_ASSOC ) ) {
				return XOS::create( $this->fetchTarget, $props );
			}
			return false;
		case PDO_FETCH_CLASS | PDO_FETCH_CLASSTYPE:
			if ( $props = mysql_fetch_array( $this->result, MYSQL_ASSOC ) ) {
				$classType = array_shift( $props );
				return XOS::create( $classType, $props );
			}
			return false;
		case PDO_FETCH_BOUND:
			if ( $row = mysql_fetch_array( $this->result, MYSQL_BOTH ) ) {
				foreach ( $this->columnBindings as $k => $col ) {
					$col[0] = $this->castValue(  is_int($k) ? $row[$k-1] : $row[$k], $col[1] );
				}
				return true;
			}
			return false;
		}
	}

	function setFetchMode( $mode, $param = null ) {
		$this->fetchMode = $mode;
		if ( $mode == PDO_FETCH_CLASS || $mode == PDO_FETCH_INTO ) {
			$this->fetchTarget = $param;
		}
	}

	/**
	 * Cast a variable according to a PDO_PARAM_* type
	 *
	 * @param mixed $val The variable whom type must be set
	 * @param int $type A PDO_PARAM_* data type
	 * @return mixed
	 * @access protected
	 */
	function castValue( $val, $type = PDO_PARAM_INT ) {
		switch ( $type ) {
		case PDO_PARAM_BOOL:
			return $val ? 1 : 0;
		case PDO_PARAM_STR:
			return $this->db->quote( $val );
		case PDO_PARAM_INT:
		default:
			return intval( $val );
		}
	}

	
	
	
}


?>