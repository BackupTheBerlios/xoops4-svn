<?php
/**
 * xoops_opal_Widget component class file
 *
 * @copyright	The Xoops project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @since       2.3.0
 * @version		$Id$
 * @package     xoops_opal
 * @subpackage  xoops_opal_Widget
 */

/**
 * Base class for User Interface widgets
 *
 * Widgets are configurable objects intended to be inserted inside the output.
 * They provide a default rendering mechanism that can be overridden using templates
 * for more flexibility if you want to.
 * 
 * You ensure widgets are instanciated by declaring them in the <TemplateObjects> section
 * of XML templates, or by inserting them directly in your templates body using the
 * ([xoWidget]) plug-in.
 * 
 * @author		Skalpa Keo
 * @since       2.3.0
 * @package     xoops_opal
 * @subpackage  xoops_opal_Widget
 */
class xoops_opal_Widget {
	/**
	 * Main tag to use when rendering this widget
	 */
	var $tagName = '';
	/**
	 * XMLID given to this widget main output element
	 * @var string
	 */
	var $elementId = '';
	/**
	 * Classes of this widget output element (when rendered to HTML)
	 * @var string[]
	 */
	var $elementClasses = array();
	/**
	 * Attributes to add to this widget main tag
	 */
	var $attributes = array();
	/**
	 * Path to the stylesheet to add to the document when inserting this widget
	 * @var string
	 */
	var $stylesheet = '';
	/**
	 * Path to the javascript file to add to the document when inserting this widget
	 * @var string
	 */
	var $javascript = '';
	/**
	 * Template file to use to render this widget (use hardcoded rendering if left empty)
	 *
	 * @var string
	 */
	var $template = '';
	
	var $templateString = '';
	
	function xoInit( $options = array(), $attributes = array() ) {
		if ( !empty( $attributes ) ) {
			$this->attributes = array_merge( $this->attributes, $attributes );
		}
		return true;
	}
	
	/**
	 * Renders this widget
	 */
	function render() {
		if ( !empty( $this->template ) ) {
			trigger_error( "Template rendering of widgets is not implemented yet (but you're welcome)", E_USER_WARNING );
		} elseif ( !empty( $this->templateString ) ) {
			return preg_replace_callback( '/{([0-9a-zA-Z_]*)}/', array( &$this, 'renderStringProperty' ), $this->templateString );
		}
		return '';
	}

	function renderStringProperty( $matches ) {
		return $matches[1];
	}
	
	/**
	 * Returns an XML opening tag with its 'id' and 'class' attributes set
	 * @access protected
	 */
	function renderTag( $tagName = null, $attributes = null, $empty = false ) {
		if ( !isset($tagName) ) {
			$tagName = $this->tagName;
		}
		if ( !isset( $attributes ) ) {
			$attributes = $this->attributes;
		}
		$str = "<$tagName";
		if ( $this->elementId ) {
			$str .= ' id="' . $this->elementId . '"';
		}
		if ( !empty( $this->elementClasses ) ) {
			$str .= ' class="' . implode( ' ', $this->elementClasses ) . '"';
		}
		if ( $attrs = $this->attributesString( $attributes ) ) {
			$str .= ' ' . $attrs;
		}
		$str .= $empty ? ' />' : '>';
		return $str;
	}
	/**
	 * Renders a collection of attributes for HTML
	 * @param array $attributes
	 * @return string
	 */
	function attributesString( $attributes ) {
		$rendered = array();
		foreach ( $attributes as $name => $val ) {
			if ( isset( $val ) ) {
				$rendered[] = $name . '="' . htmlspecialchars( $val, ENT_QUOTES ) . '"';
			}
		}
		return implode( ' ', $rendered );
	}

}

