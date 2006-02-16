<?php
/**
 * xoops_opal_PdfMaker main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_opal
 * @subpackage	xoops_opal_PdfMaker
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'FPDF' );
XOS::import( 'PDML' );

/**
 * Opal PDF generation component (based on {@link http://pdml.sourceforge.net/ PDML}+{@link http://www.fpdf.org/ FPDF})
 *
 * For the moment the PDF maker just provides a dummy implementation of {@link http://pdml.sourceforge.net/ PDML}/
 * {@link http://www.fpdf.org/ FPDF}. It is called by the {@link xoops_opal_Theme theme service} when it is
 * asked to return PDF output (templates are processed normally, and their output sent to this component for
 * post-processing).
 * 
 * Please note that this component is just here for experimentation purposes and that the decision to go for
 * PDML is not yet definitive. If you don't want to take any risk to have to rewrite some of your code,
 * you may prefer to make direct use of the FPDF class.
 * 
 * @package		xoops_opal
 * @subpackage	xoops_opal_PdfMaker
 * @devstatus	unstable
 */
class xoops_opal_PdfMaker extends PDML {
	/**
	 * Format of the output page
	 * 
	 * Configures the page format of the generated document. Valid values are <var>A5</var>,
	 * <var>A4</var>, <var>A3</var>, <var>letter</var>, <var>legal</var>.
	 * @var string
	 */
	var $pageFormat = 'A4';
	/**
	 * Orientation of the output page
	 * 
	 * Configures the page orientation of the generated document. Valid values are
	 * <var>P</var> for Portrait, and <var>L</var> for landscape.
	 * @var string
	 */
	var $pageOrientation = 'P';
	/**
	 * Default measure unit
	 * 
	 * Configures the default user mesure unit. Possible values are:
	 * - <b>pt:</b> point (1/72 of inch, or 0.35mm)
	 * - <b>mm:</b> millimeter
	 * - <b>cm:</b> centimeter
	 * - <b>in:</b> inch
	 * 
	 * NB: It seems that PDML has problems with anything else than pt, so you may want to stick
	 * with this unit ;-).
	 * 
	 * @var string
	 */
	var $unitType = 'pt';
	/**
	 * Initializes the PDF maker and the underlying FPDF class.
	 *
	 * @param array $options
	 * @return bool
	 */	
	function xoInit( $options = array() ) {
		$this->FPDF( $this->pageOrientation, $this->unitType, $this->pageFormat );
		return true;
	}
	/**
	 * Start capturing output for later post-processing
	 * 
	 * This method starts output buffering, ensuring content is sent to the
	 * {@link xoops_opal_PdfMaker::transformPdml()} method.
	 */
	function startCapture() {
		$this->compress = 0;
		ob_start( array( &$this, 'transformPdml' ) );
	}
	/**
	 * Parses the specified PDML string and returns PDF content
	 * @param string PDML string to parse
	 * @return string Content of the generated PDF file
	 */
	function transformPdml( $str ) {
		$this->ParsePDML( $str );
		return $this->Output( '', 'S' );	
	}

}


?>