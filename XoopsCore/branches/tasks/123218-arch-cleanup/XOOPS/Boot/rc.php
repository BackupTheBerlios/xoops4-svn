<?php
/**
* XOOPS default startup-sequence file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @package		xoops
* @since        2.3.0
* @author		Skalpa Keo <skalpa@xoops.org>
* @version		$Id$
*/

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();


if ( $this->xoRunMode ) {
	// If the server is not in production mode, instanciate the logger and error handling services	
	$this->loadService( 'logger' );
	$this->loadService( 'error' );
}

if ( isset( $_SERVER['SERVER_NAME'] ) ) {
	$this->loadService( 'session' );
	$this->loadService( 'http' );
}

?>