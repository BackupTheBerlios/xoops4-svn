<?php
/**
* xoops_pyro bundle information file
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
	'xoBundleDisplayName' => '"Pyro" user interface framework',
	'xoBundleIdentifier' => 'xoops_pyro',
	'xoServices' => array(
		'xoops_pyro_Theme'	=> array (
			'xoBundleRoot' => '/Theme.xoobj'
		),
		'xoops_pyro_Widget' => array(
			'xoClassPath' => '/widget.php',
		),
		'xoops_pyro_TreeWidget'	=> array (
			'xoBundleRoot' => '/TreeWidget.xoobj'
		),
		'xoops_template_Smarty'	=> array (
			'xoBundleRoot' => '/Smarty.xoobj'
		),
	),
);

?>