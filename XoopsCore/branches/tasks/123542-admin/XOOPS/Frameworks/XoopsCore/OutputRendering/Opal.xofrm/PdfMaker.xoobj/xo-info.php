<?php
/**
 * xoops_opal_PdfMaker bundle information file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since       2.3.0
 * @package		xoops_opal
 * @subpackage	xoops_opal_PdfMaker
 * @version		$Id$
 */

return array(
	'xoBundleDisplayName' => 'Opal PDF rendering component',
	'xoBundleIdentifier' => 'xoops_opal_PdfMaker',
	'xoClassPath' => '/pdfmaker.php',
	'xoServices' => array(
		'FPDF' => array(
			'xoClassPath' => '/fpdf/fpdf.php',
		),
		'PDML' => array(
			'xoClassPath' => '/pdml.php',
		),
	),
);

?>