<?php
/**
* xoops_pyro_Theme bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @since        2.3.0
* @version		$Id$
* @author		Skalpa Keo <skalpa@xoops.org>
*/

return array(
	'xoBundleDisplayName' => 'XOOPS Theme component',
	'xoBundleIdentifier' => 'xoops_pyro_Theme',
	'xoClassPath' => '/theme.php',
	'xoFactory' => 'xoops_pyro_ThemeFactory',
	'xoServices' => array(
		'xoops_pyro_ThemeFactory' => array(
			'xoClassPath' => '/theme.php',
		),
	),
);

?>