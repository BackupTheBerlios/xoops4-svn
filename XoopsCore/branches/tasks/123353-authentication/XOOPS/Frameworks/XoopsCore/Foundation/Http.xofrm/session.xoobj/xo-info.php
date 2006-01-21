<?php
/**
* xoops_http session component bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright	The XOOPS project http://www.xoops.org/
* @license		http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since		2.3.0
* @package		xoops_http
* @package		xoops_http_Session
* @version		$Id$
*/

return array(
	'xoBundleDisplayName' => 'XOOPS Session management component',
	'xoBundleIdentifier' => 'xoops_http_Session',
	'xoClassPath' => '/session.php',
	'xoFactory' => 'xoops_http_SessionFactory',
	'xoServices' => array(
		'xoops_http_SessionService' => array(
			'xoBundleDisplayName' => 'XOOPS default session service (using php.ini settings)',
			'xoClassPath' => '/session.php',
		),
		'xoops_http_CustomSessionService' => array(
			'xoBundleDisplayName' => 'XOOPS custom session service (using custom settings)',
			'xoClassPath' => '/session-custom.php',
		),
		'xoops_http_DatabaseSessionHandler' => array(
			'xoBundleDisplayName' => 'XOOPS database session handler',
			'xoClassPath' => '/handler-db.php',
		),
	),
	
	
	
);

?>