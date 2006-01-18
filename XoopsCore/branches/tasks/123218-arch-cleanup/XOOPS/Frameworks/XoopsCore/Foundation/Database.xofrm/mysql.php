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
	function errorInfo() {
		return array( 'HY000', @mysql_errno( $this->conn ), @mysql_error( $this->conn ) );
	}	

	function exec( $statement ) {
		if ( $this->allowWebChanges ) {
			return mysql_query( $statement, $this->conn ) ? mysql_affected_rows( $this->conn ) : false;
		} else {
			trigger_error( 'Database updates are not allowed during processing of a GET request', E_USER_WARNING );
			return false;
		}
	}
	
	function lastInsertId( $name = '' ) {
		return mysql_insert_id( $this->conn );
	}
	
	function quote( $string ) {
		return "'" . mysql_real_escape_string( $string, $this->conn ) . "'";
	}

}


class xoops_db_Statement_mysql {

	var $boundColumns = array();	
	var $boundParams = array();
	var $boundValues = array();
	
	
	function bindColumn( $column, &$variable, $type ) {
		$this->boundColumns[$column] = array( &$variable, $type );
	}
	function bindParam( $param, &$variable, $type = 0, $length = 0, $options = array() ) {
		$this->boundParams[$param] = array( &$variable, $type, $length, $options );	
	}
	function bindValue( $param, $value, $type ) {
	}
}


?>