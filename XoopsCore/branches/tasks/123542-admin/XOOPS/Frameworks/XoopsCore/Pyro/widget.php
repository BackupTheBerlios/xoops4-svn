<?php
/**
* xoops_pyro_Widget component class file
*
* @copyright	The Xoops project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @package      xoops_pyro
* @subpackage   xoops_pyro_Widget
* @author       Skalpa Keo <skalpa@xoops.org>
* @since        2.3.0
* @version		$Id$
*/

/**
* xoops_pyro_Widget
*
* Widgets are user interface components that can be rendered to HTML
* 
* 
* 
* 
* @author 		Skalpa Keo
* @package		xoops_pyro
* @subpackage	xoops_pyro_Widget
* @since        2.3.0
*/
class xoops_pyro_Widget {
	/**
	 * XMLID given to this widget main output element
	 *
	 * @var string
	 */
	var $elementId = '';
	/**
	 * Classes of this widget output element (when rendered to HTML)
	 *
	 * @var string[]
	 */
	var $elementClasses = array();
	/**
	 * Path to the stylesheet to add to the document when inserting this widget
	 *
	 * @var string
	 */
	var $stylesheet = '';
	/**
	 * Path to the javascript file to add to the document when inserting this widget
	 *
	 * @var string
	 */
	var $javascript = '';
	/**
	 * Template to use to render this widget (use hardcoded rendering if left empty)
	 *
	 * @var string
	 */
	var $template = '';
	/**
	 * Renders this widget
	 */
	function render() {
		return '';
	}
	/**
	 * Returns an XML opening tag with its 'id' and 'class' attributes set
	 * @access protected
	 */
	function renderOpeningTag( $tagName ) {
		$str = "<$tagName";
		if ( $this->elementId ) {
			$str .= ' id="' . $this->elementId . '"';
		}
		if ( !empty( $this->elementClasses ) ) {
			$str .= ' class="' . implode( ' ', $this->elementClasses ) . '"';
		}
		return $str . '>';
	}

	
	
}

