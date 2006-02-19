<?php
/**
 * xoops_pyro_FormRange main class file
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
 * @subpackage 	xoops_pyro_Forms
 */


XOS::import( 'xoops_pyro_FormElement' );

/**
 * Form element widget for the XForms 'range' element
 * 
 * When rendered as an XForms element, this widget will display a range control and
 * degrade gracefully to a select element when using HTML.
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormRange extends xoops_pyro_FormElement {

	var $tagName = 'range';
	
	/**
	 * Minimal value that can be chosen with this control
	 * @var integer
	 */
	var $start = 0;
	/**
	 * Maximal value that can be chosen with this control
	 * @var integer
	 */
	var $end = 100;
	/**
	 * Increment between two choices
	 * @var integer
	 */
	var $step = 10;
	
	var $attributes = array();

	function renderControl() {
		$attributes = $this->attributes;
		if ( $this->renderAsHtml ) {
			if ( $this->name ) {
				$attributes['name'] = $this->name;
			}
			$attributes['size'] = 1;
			$str = $this->renderTag( 'select', $attributes );
			$val = $this->start;
			if ( !$this->step ) {
				$this->step = 1;
			}
			while ( $val <= $this->end ) {
				$str.= '<option value="' . $val . '"';
				if ( isset($this->value) && $this->value == $val ) {
					$str .= ' selected="selected"';
				}
				$str .= ">$val</option>";
				$val += $this->step;
			}
			return $str . '</select>';
		} else {
			if ( $this->refPath ) {
				$attributes['ref'] = $this->refPath;
			}
			return $this->renderTag( $this->tagName, $attributes ) . $this->renderLabel() . "</$this->tagName>";
		}
	}
}

?>