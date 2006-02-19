<?php
/**
 * xoops_pyro_FormElement main class file
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


XOS::import( 'xoops_opal_Widget' );

/**
 * Base class for form elements
 * 
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormElement extends xoops_opal_Widget {
	/**
	 * This element label
	 * @var string
	 */
	var $label = '';
	/**
	 * Name of this element when rendered as within an HTML form
	 * @var string
	 */
	var $name = '';
	/**
	 * Pointer to the data this element references (if any)
	 * @var mixed
	 */
	var $value = null;
	/**
	 * Path to the data this element references
	 * @var string
	 */
	var $refPath = '';
	/**
	 * Pointer to the form that owns this element
	 * @var mixed
	 */
	var $form = null;
	/**
	 * If this element should be rendered as an HTML form element or as an XForms element
	 * @var boolean
	 */
	var $renderAsHtml = true;
	
	function xoInit( $options = array(), $attributes = array() ) {
		if ( $this->refPath && !$this->name ) {
			$this->name = $this->refPath;
		}
		return parent::xoInit( $options, $attributes );
	}
	
	/**
	 * Convenience method to attach a data variable to this element
	 * @param mixed $var Pointer to the variable this element should be referencing
	 * @param string $path Path to the attached variable
	 * @param string $name Name to use when rendering this element as HTML (if empty, will be created from $path)
	 */
	function attachVar( &$var, $path, $name = '' ) {
		$this->value =& $var;
		$this->ref = $path;
		$this->name = empty($name) ? $path : $name;
		return $var;
	}
	
	/**
	 * Render this element label
	 * @return string
	 */
	function renderLabel() {
		if ( $this->label ) {
			if ( $this->elementId ) {
				return "<label for=\"$this->elementId\">$this->label</label>";
			}
			return "<label>$this->label</label>";
		}
		return '';
	}

	function renderControl() {
		return '';
	}

	function renderStringProperty( $matches ) {
		if ( $matches[1] == 'label' ) {
			return $this->renderLabel();
		} elseif ( $matches[1] == 'control' ) {
			return $this->renderControl();
		}
		return parent::renderStringProperty( $matches );
	}

	
	
}

?>