<?php
/**
* xoops_auth framework bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright	The XOOPS project http://www.xoops.org/
* @license		http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since		2.3.0
* @category		CoreServices
* @package		xoops_auth
* @version		$Id$
*/

return array(
	'xoBundleDisplayName'			=> 'XOOPS Authentication framework',
	'xoBundleIdentifier'			=> 'xoops_auth',

	'xoServices' => array(
		'xoops_auth_AuthenticationService' => array (
			'xoBundleDisplayName'			=> 'XOOPS default authentication service',
			'xoClassPath' => '/auth.php',
		),
		'xoops_auth_DatabaseDriver' => array (
			'xoBundleDisplayName'			=> 'XOOPS database authentication driver',
			'xoClassPath' => '/xoopsdb.php',
		),
		'xoops_auth_LdapDriver' => array (
			'xoBundleDisplayName'			=> 'XOOPS LDAP authentication driver',
			'xoClassPath' => '/ldap.php',
		),
		'xoops_auth_ActiveDirectoryDriver' => array (
			'xoBundleDisplayName'			=> 'XOOPS ActiveDirectory Service (tm) authentication driver',
			'xoClassPath' => '/ads.php',
		),
	),
);

?>