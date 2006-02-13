<?php
/**
 * xoops_db_DatabaseObject and xoops_db_DatabaseFactory classes definition
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
 * xoops_db_Database objects factory
 * 
 * The xoops_db_DatabaseFactory is the factory class responsible for database connections instanciation.
 * It is invocated when you request the creation of a <var>xoops_db_Database</var> instance, and will
 * return an instance of a specicif driver according to the <var>dsn</var> parameter you'll have provided.
 * 
 * In general, a DSN consists of the driver name, followed by a colon, followed by the driver-specific 
 * connection syntax. Further information is available from the driver-specific documentation.
 * 
 * The dsn parameter supports three different methods of specifying the arguments required to create a 
 * database connection: 
 * - <b>Driver invocation</b>: <var>dsn</var> contains the full DSN.
 * - <b>Aliasing</b>: <var>dsn</var> dsn consists of a datastore name that maps to an entry defined in the {@link xoops_db_DatabaseFactory::$availableStores} collection.
 *
 * A special version of the <var>xoops.mysql</var> driver implementing the pre-2.3 database interface
 * called <var>xoops.legacy</var> is provided with XOOPS. Applications developers can use it to switch
 * progressively to the new interface (the old one will become entirely deprecrated in the future, to ensure
 * XOOPS can entirely run on other RDBMS systems).
 * 
 * <b>Create a database connection using driver invocation</b>
 * <code>
 * // Connect to a xoops_db_Database_mysql database
 * $mysqldb =& XOS::create( 'xoops_db_Database', array(
 *     'dsn' => 'mysql:dbname=test;host=localhost;user=dbuser;password=dbpwd',
 * ) );
 * </code>
 * 
 * <b>Create a database instance using an alias</b>
 * 
 * The following example assumes that you have defined a datastore named <i>backupdb</i> in your system
 * (whether by using the corresponding configuration module panel, or by entering it in the
 * <var>xoops_db_DatabaseFactory</var> managed preferences file).
 * 
 * <code>
 * // Connect to a database using an alias
 * $db =& XOS::create( 'xoops_db_Database', array( 'dsn' => 'backupdb' ) );
 * if ( !$db ) {
 *     echo 'Failed to connect to the backupdb database';
 * }
 * </code>
 * 
 * <b>Create a database connection to the system database</b>
 * <code>
 * // Use the default invocation system
 * $db =& XOS::create( 'xoops_db_Database', array( 'dsn' => 'system' ) );
 * 
 * // Specifically request an instance supporting the old db interface
 * $olddb =& $xoops->loadService( 'xoopsdb' );
 * </code>
 *
 * @package		xoops_db
 * @subpackage	xoops_db_Database
 */
class xoops_db_DatabaseFactory {
	/**
	 * Available databases, indexed by name
	 *
	 * @var array
	 */
	var $availableStores = array(
		'system' => array(
			'driverName' => 'xoops.mysql',
			'host' => XOOPS_DB_HOST,
			'user' => XOOPS_DB_USER,
			'password' => XOOPS_DB_PASS,
			'dbname' => XOOPS_DB_NAME,
			'prefix' => XOOPS_DB_PREFIX,
			'persistent' => XOOPS_DB_PCONNECT,
		),
	);
	/**
	 * References to the already created instances (indexed by DSN)
	 *
	 * @var array
	 * @access private
	 */
	var $instances = array();
	
	/**
	 * Creates an instance to represent a connection to the requested database
	 * 
	 * mysql:host=localhost;dbname=mydb
	 * to explicitely specify a xoops driver:
	 * xoops.legacy:host=localhost;dbname=mydb
	 */
	function &createInstance( $options = array(), $initArgs = array() ) {
		if ( !isset( $options['dsn'] ) ) {
			$options['dsn'] = 'system';
		}
		if ( isset( $this->instances[ $options['dsn'] ] ) ) {
			return $this->instances[ $options['dsn'] ];
		}
		$inst = false;
		$parsed = explode( ':', $dsn = $options['dsn'], 2 );
		if ( count( $parsed ) == 1 ) {
			// An alias name was specified
			if ( !isset( $this->availableStores[$parsed[0]] ) ) {
				trigger_error( "The database alias {$parsed[0]} is invalid.", E_USER_WARNING );
				return $inst;
			}
			$options = array_merge( $this->availableStores[$parsed[0]], $options );
		} else {
			// A real DSN was specified
			if ( substr( $parsed[0], 0, 5 ) == 'xoops' ) {
				// Convert the rest of the dsn string to properties
				$parsed = explode( ';', $parsed[1] );
				foreach ( $parsed as $prop ) {
					list( $name, $value ) = explode( '=', $prop, 2 );
					$options[$name] = $value;		
				}
				$options['driverName'] = $parsed[0];
			}
		}
		if ( substr( $options['driverName'], 0, 5 ) != 'xoops' ) {
			trigger_error( "Direct PDO drivers access is not implemented yet.", E_USER_WARNING );
			return $inst;
		} else {
			$options['driverName'] = substr( $options['driverName'], 6 );
			$inst =& XOS::create( "xoops_db_Database_{$options['driverName']}", $options );
			$this->instances[$dsn] =& $inst;
		}
		return $inst;
	}
}


/**
 * Base class for database drivers
 * 
 * All xoops_db database drivers inherit from this class.
 * @package		xoops_db
 * @subpackage	xoops_db_Database
 */
class xoops_db_Database {
	/**
	 * Type of database driver
	 * @var string
	 */
	var $driverName = '';
	/**
	 * Database hostname
	 * @var string
	 */
	var $host = 'localhost';
	/**
	 * Username used to connect
	 * @var string
	 */
  	var $user = '';
  	/**
  	 * Database name
  	 *
  	 * @var string
  	 */
	var $dbname = 'xoops';
	/**
	 * Prefix for tables in the database
	 * @var string
	 */
	var $prefix = '';
	/**
	 * Whether to use persistent connection or not
	 * @var boolean
	 */
	var $persistent = false;
	/**
	 * If true, attempt to connect to the database upon instanciation
	 * @var boolean
	 */
	var $autoConnect = true;
	/**
	 * If statements that modify the database are selected (see forceExec() to override this)
	 * @var boolean
	 */
	var $allowWebChanges = false;
	
	/**
	 * reference to a {@link XoopsLogger} object
     * @see XoopsLogger
	 * @var object XoopsLogger
	 */
	var $logger = false;


	function xoInit( $options = array() ) {
		global $xoops;

		$this->allowWebChanges = ( $_SERVER['REQUEST_METHOD'] != 'GET' );
		if ( $xoops->services['logger'] ) {
			$this->logger =& $xoops->services['logger'];
		}
		if ( $this->autoConnect ) {
			return $this->connect();
		}
		return true;
	}
	
	/**
	 * connect to the database
	 * 
	 * @param bool $selectdb select the database now?
	 * @return bool successful?
	 */
	function connect( $selectdb = true ) {
		return false;
	}
	/**
	 * Send information about an event to the attached logger
	 */
	function logEvent( $msg ) {
		if ( $this->logger ) {
			$this->logger->logEvent( $msg, $this->xoBundleIdentifier );
		}
	}
	/**
	 * Send information about an error to the attached logger
	 */
	function logError() {
		if ( $this->logger ) {
			$error = $this->errorInfo();
			$this->logger->logEvent( "Error {$error[1]} ({$error[0]}): {$error[2]}", $this->xoBundleIdentifier );
		}
	}
	
	/**
	 * Prefix a table name
	 * 
	 * if tablename is empty, only prefix will be returned
	 * 
	 * @param string $table tablename
	 * @return string prefixed tablename, just prefix if tablename is empty
	 */
	function prefix( $table = '' ) {
		if ( empty( $table ) ) {
			return $this->prefix;
		}
		return empty($this->prefix) ? $table : ( $this->prefix . '_' . $table );
	}
	
}






?>