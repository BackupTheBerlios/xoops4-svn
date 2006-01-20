<?php
/**
 * xoops_auth_Database main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_auth
 * @package		xoops_auth_DatabaseDriver
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'xoops_auth_AuthenticationService' );

/**
 * xoops_auth_DatabaseDriver authentication driver
 * 
 * This driver checks if the specified user exists in the database
 */
class xoops_auth_DatabaseDriver extends xoops_auth_AuthenticationDriver {

	var $hashFunctions = array( 'md5' );

	function checkCredentials( $login, $password, $hash = '' ) {
		if ( empty( $hash ) ) {
			$password = md5( $password );
		}
		$db =& XOS::create( 'xoops_db_Database' );
		$table = $db->prefix( 'users' );
		$stmt = $db->prepare( "SELECT COUNT(*) FROM `$table` WHERE `uname`=:login AND `pass`=:password" );
		$stmt->bindValue( ':login', $login, PDO_PARAM_STR );
		$stmt->bindValue( ':password', $password, PDO_PARAM_STR );
		if ( $stmt->execute() ) {
			list( $count ) = $stmt->fetch( PDO_FETCH_NUM );
			$stmt->closeCursor();
		}
		return @$count ? true : false;
	}


}






?>