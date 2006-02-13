<?php
/**
 * xoops_opal bundle information file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since       2.3.0
 * @version		$Id$
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @package 	xoops_opal
 */

return array(
	'xoBundleDisplayName' => 'Opal output rendering framework',
	'xoBundleIdentifier' => 'xoops_opal',
	'xoServices' => array(
		'xoops_opal_Smarty'	=> array (
			'xoBundleRoot' => '/Smarty.xoobj'
		),
		'xoops_opal_Theme'	=> array (
			'xoBundleRoot' => '/Theme.xoobj'
		),
		'xoops_opal_Widget' => array(
			'xoClassPath' => '/widget.php',
		),
	),
);

?>