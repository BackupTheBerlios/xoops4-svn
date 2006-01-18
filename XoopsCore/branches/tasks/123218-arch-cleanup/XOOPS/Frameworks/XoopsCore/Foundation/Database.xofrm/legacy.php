<?php
/**
 * Legacy (XOOPS 2.0.x) xoops_db_Database driver class definition
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Kazumi Ono <onokazu@xoops.org>
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

XOS::import( 'xoops_db_Database_mysql' );

/**
 * Legacy xoops_db_Database driver main class
 */
class xoops_db_Database_legacy extends xoops_db_Database_mysql {
	/**
	 * assign a {@link XoopsLogger} object to the database
	 * @deprecated (assign the logger public property)
	 */
	function setLogger(&$logger) {
		$this->logger =& $logger;
	}
	/**
	 * set the prefix for tables in the database
	 * @deprecated (assign the prefix public property)
	 */
	function setPrefix($value) {
		$this->prefix = $value;
	}
	/**
	 * generate an ID for a new row
     * 
     * This is for compatibility only. Will always return 0, because MySQL supports
     * autoincrement for primary keys.
     * 
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because mysql has support for autoincrement
	 */
	function genId($sequence)
	{
		return 0; // will use auto_increment
	}

	/**
	 * Get a result row as an enumerated array
	 * 
     * @param resource $result
     * @return array
	 */
	function fetchRow($result)
	{
		return @mysql_fetch_row($result);
	}

	/**
	 * Fetch a result row as an associative array
	 *
     * @return array
	 */
	function fetchArray($result)
    {
        return @mysql_fetch_assoc( $result );
    }

    /**
     * Fetch a result row as an associative array
     *
     * @return array
     */
    function fetchBoth($result)
    {
        return @mysql_fetch_array( $result, MYSQL_BOTH );
    }

	/**
	 * Get the ID generated from the previous INSERT operation
	 * 
     * @return int
	 */
	function getInsertId()
	{
		return mysql_insert_id($this->conn);
	}

	/**
	 * Get number of rows in result
	 * 
     * @param resource query result
     * @return int
	 */
	function getRowsNum($result)
	{
		return @mysql_num_rows($result);
	}

	/**
	 * Get number of affected rows
	 *
     * @return int
	 */
	function getAffectedRows()
	{
		return mysql_affected_rows($this->conn);
	}

	/**
	 * Close MySQL connection
	 * 
	 */
	function close()
	{
		mysql_close($this->conn);
	}

	/**
	 * will free all memory associated with the result identifier result.
	 * 
     * @param resource query result
     * @return bool TRUE on success or FALSE on failure. 
	 */
	function freeRecordSet($result)
	{
		return mysql_free_result($result);
	}

	/**
	 * Returns the text of the error message from previous MySQL operation
	 * 
     * @return bool Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred. 
	 */
	function error()
	{
		return @mysql_error();
	}

	/**
	 * Returns the numerical value of the error message from previous MySQL operation 
	 * 
     * @return int Returns the error number from the last MySQL function, or 0 (zero) if no error occurred. 
	 */
	function errno()
	{
		return @mysql_errno();
	}

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     * 
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    function quoteString($str)
    {
         //return "'".str_replace('\\"', '"', addslashes($str))."'";
         return $this->quote( $str );
    }

    /**
	 * Get field name
	 *
     * @param resource $result query result
     * @param int numerical field index
     * @return string
	 */
	function getFieldName($result, $offset)
	{
		return mysql_field_name($result, $offset);
	}

	/**
	 * Get field type
	 *
     * @param resource $result query result
     * @param int $offset numerical field index
     * @return string
	 */
    function getFieldType($result, $offset)
	{
		return mysql_field_type($result, $offset);
	}

	/**
	 * Get number of fields in result
	 *
     * @param resource $result query result
     * @return int
	 */
	function getFieldsNum($result)
	{
		return mysql_num_fields($result);
	}

	/**
     * perform a query on the database
     * 
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    function query($sql, $limit=0, $start=0) {
	    $sql = ltrim($sql);
		if ( !$this->allowWebChanges && strtolower( substr($sql, 0, 6) ) != 'select' )  {
			trigger_error( 'Database updates are not allowed during processing of a GET request', E_USER_WARNING );
			return false;
		}
		return $this->queryF( $sql, $limit, $start );
    }
    
    function queryF( $sql, $limit, $start = 0 ) {
    	if ( !empty($limit) ) {
			$sql = $sql . ' LIMIT ' . (int)$start . ', ' . (int)$limit;
		}
		if ( $result = mysql_query($sql, $this->conn) ) {
			$this->logEvent($sql);
        } else {
        	$this->logError();
        }
        return $result;
    }

    /**
	 * perform queries from SQL dump file in a batch
	 * 
     * @param string $file file path to an SQL dump file
     * 
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
	 */
	function queryFromFile($file){
        if (false !== ($fp = fopen($file, 'r'))) {
			include_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';
            $sql_queries = trim(fread($fp, filesize($file)));
            SqlUtility::splitMySqlFile($pieces, $sql_queries);
            foreach ($pieces as $query) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery(trim($query), $this->prefix());
                if ($prefixed_query != false) {
                    $this->query($prefixed_query[0]);
                }
            }
            return true;
        }
        return false;
    }
    
}

?>