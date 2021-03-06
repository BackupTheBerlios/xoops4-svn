<?php
/**
* xoops_template_Smarty bundle information file
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
	'xoBundleDisplayName' => 'Smarty template engine (XOOPS version)',
	'xoBundleIdentifier' => 'xoops_template_Smarty',
	'xoClassPath' => '/smarty.php',
	'xoServices' => array(
		'Smarty' => array(
			'xoClassPath' => '/smarty/Smarty.class.php',
		),
	),
);

?>