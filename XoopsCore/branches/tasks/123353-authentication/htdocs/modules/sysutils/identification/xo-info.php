<?php
/**
 * mod_xoops_Identification bundle information file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		mod_xoops_Identification
 * @version		$Id$
 */



return array(
	'xoBundleIdentifier' => 'mod_xoops_Identification',
	'xoBundleDisplayName' => 'XOOPS default identification module',
	//'xoClassPath' => '/xoops-module.php',

	'allowFor' => array( XOOPS_GROUP_ANONYMOUS, XOOPS_GROUP_USERS ),
	
	'moduleLocations' => array(
		'login' => array(
			'displayName' => 'User login',
			'scriptFile' => '/login.php',
			'parameters' => array(
				'login' => array( '', XO_TYPE_STRING ),
				'password' => array( '', XO_TYPE_STRING ),
				'xoops_redirect' => array( '/www/', XO_TYPE_STRING ),
			),
		),
		'logout' => array(
			'displayName' => 'Logout',
			'scriptFile' => '/logout.php',
			'allowFor' => array( XOOPS_GROUP_USERS ),
		),
		'lost-password' => array(
			'displayName' => 'Lost your password ?',
			'scriptFile' => '/lostpass.php',
		),
	),
	'xoServices' => array(
		'xoops_identification_LoginForm' => array(
			'xoClassPath' => '/class/loginform.php',
		),
	),
);

?>