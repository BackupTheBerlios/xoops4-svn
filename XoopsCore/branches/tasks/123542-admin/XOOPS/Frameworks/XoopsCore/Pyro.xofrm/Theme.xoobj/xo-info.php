<?php
/**
* xoops_opal_Theme bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @since        2.3.0
* @version		$Id$
* @author		Skalpa Keo <skalpa@xoops.org>
* @package 		xoops_opal
* @subpackage 	xoops_opal_Theme
*/

return array(
	'xoBundleDisplayName' => 'XOOPS Theme component',
	'xoBundleIdentifier' => 'xoops_opal_Theme',
	'xoClassPath' => '/theme.php',
	'xoFactory' => 'xoops_opal_ThemeFactory',
	'xoServices' => array(
		'xoops_opal_ThemeFactory' => array(
			'xoClassPath' => '/theme.php',
		),
	),
);

?>