<?php
/**
 * xoops_http_DatabaseSessionHandler main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Kazumi Ono	<onokazu@xoops.org>
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_http
 * @package		xoops_http_Session
 * @version		$Id$
 */

/**
 * Handler for a session
 */
class xoops_http_DatabaseSessionHandler {

	var $tableName = 'session';

     /**
      * Database connection object
      *
      * @var	object
      * @access	protected
      */
    var $db;

    function xoInit( $options = array() ) {
    	global $xoops;
    	//( 'xoops_db_LegacyDatabase' );
    	if ( !$this->db =& $xoops->loadService( 'legacydb' ) ) {
    		return false;
    	}
    	$this->tableName =& $this->db->prefix( $this->tableName );
    	return true;
    }
    /**
     * Read a session from the database
     *
     * @param	string  &sess_id    ID of the session
     *
     * @return	array   Session data
     */
    function read( $sessionId ) {
    	$res = $this->db->query(  "SELECT `sess_data` FROM `$this->tableName` WHERE `sess_id`=" . $this->db->quote( $sessionId ) );
    	if ( $res !== false ) {
            list( $data ) = $db->fetchRow($result);
            return $data;
        }
        return '';
    }
    /**
     * Write a session to the database
     *
     * @param   string  $sess_id
     * @param   string  $sess_data
     *
     * @return  bool
     **/
    function write( $sessionId, $data ) {
    	$sessionId = $this->db->quote( $sessionId );
		$data = $this->db->quote( $data );
		$time = time();
    	
    	list( $count ) = $this->db->fetchRow( $this->db->query( "SELECT COUNT(*) FROM `$this->tableName` WHERE sess_id=$sessionId" ) );
 	
    	if ( $count > 0 ) {
			$sql = "UPDATE `$this->tableName` SET sess_updated=$time,sess_data=$data WHERE sess_id=$sessionId";
    	} else {
    		$ip = $this->db->quote( $_SERVER['REMOTE_ADDR'] );
    		$sql = "INSERT INTO `$this->tableName` (sess_id,sess_updated,sess_ip,sess_data) VALUES ($sessionId,$time,$ip,$data)";
    	}
		return $this->db->queryF( $sql );
    }
    /**
     * Destroy a session
     *
     * @param   string  $sess_id
     *
     * @return  bool
     **/
    function destroy( $sessionId ) {
    	$sessionId = $this->db->quote( $sessionId );
    	return $this->db->queryF( "DELETE FROM `$this->tableName` WHERE sess_id=$sessionId" ) ? true : false;
    }
    /**
     * Garbage Collector
     *
     * @param   int $expire Time in seconds until a session expires
	 * @return  bool
     **/
    function gc( $expire ) {
        $time = time() - intval($expire);
        return $this->db->queryF( "DELETE FROM `$this->tableName` WHERE sess_updated < $time" );
    }
}
?>