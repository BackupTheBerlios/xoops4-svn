<?php
/**
* xoops_kernel framework bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since        2.3.0
* @package		xoops_kernel
* @version		$Id$
*/

return array(
	'xoBundleDisplayName' => 'XOOPS kernel framework',
	'xoBundleIdentifier' => 'xoops_kernel',
	
	'xoServices' => array(
		'xoops_kernel_ErrorHandler' => array(
			'xoClassPath' => '/errorhandler.php',
		),
		'xoops_kernel_Module' => array(
			'xoClassPath' => '/module.php',
			'xoFactory' => 'xoops_kernel_ModuleFactory',
		),
		'xoops_kernel_ModuleFactory' => array(
			'xoClassPath' => '/module.php',
			'singleton' => true,
		),
		'xoops_kernel_User' => array(
			'xoClassPath' => '/user.php',
		),
	),
);

?>