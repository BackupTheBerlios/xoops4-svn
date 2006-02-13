<?php
/**
 * xoops_template_Smarty bundle information file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since       2.3.0
 * @package		xoops_opal
 * @subpackage	xoops_opal_Smarty
 * @version		$Id$
 */

return array(
	'xoBundleDisplayName' => 'Smarty template engine (XOOPS version)',
	'xoBundleIdentifier' => 'xoops_opal_Smarty',
	'xoClassPath' => '/smarty.php',
	'xoServices' => array(
		'Smarty' => array(
			'xoClassPath' => '/smarty/Smarty.class.php',
		),
	),
);

?>