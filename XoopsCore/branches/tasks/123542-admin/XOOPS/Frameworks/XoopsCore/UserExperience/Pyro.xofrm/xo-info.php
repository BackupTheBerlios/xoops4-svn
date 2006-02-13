<?php
/**
 * xoops_pyro bundle information file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since       2.3.0
 * @version		$Id$
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @package		xoops_pyro
 */

return array(
	'xoBundleDisplayName' => '"Pyro" user interface wigets set',
	'xoBundleIdentifier' => 'xoops_pyro',
	'xoServices' => array(
		'xoops_pyro_TreeWidget'	=> array (
			'xoBundleRoot' => '/TreeWidget.xoobj'
		),
		// Form widgets
		'xoops_pyro_Form'			=> array( 'xoClassPath' => '/Forms/form.php' ),
		'xoops_pyro_FormElement'	=> array( 'xoClassPath' => '/Forms/element.php' ),
		'xoops_pyro_FormText'		=> array( 'xoClassPath' => '/Forms/text.php' ),
		'xoops_pyro_FormSecret'		=> array( 'xoClassPath' => '/Forms/text.php' ),
		'xoops_pyro_FormTextarea'	=> array( 'xoClassPath' => '/Forms/textarea.php' ),
		'xoops_pyro_FormSelect'		=> array( 'xoClassPath' => '/Forms/select.php' ),
		'xoops_pyro_FormSelect1'	=> array( 'xoClassPath' => '/Forms/select.php' ),
		'xoops_pyro_FormSubmit'		=> array( 'xoClassPath' => '/Forms/submit.php' ),
		'xoops_pyro_FormRange'		=> array( 'xoClassPath' => '/Forms/range.php' ),
	),
);

?>