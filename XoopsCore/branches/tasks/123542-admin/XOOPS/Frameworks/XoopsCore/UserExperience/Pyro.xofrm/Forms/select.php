<?php
/**
 * xoops_pyro_FormSelect main class file
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
 * Form widget for the XForms 'select' element
 * 
 * When rendered as an XForms element, this widget will display a range control and
 * degrade gracefully to a select element when using HTML.
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormSelect extends xoops_pyro_FormElement {

	var $tagName = 'select';
	
	var $appearance = 'minimal';
	

	function renderControl() {
		if ( $this->renderAsHtml ) {
			if ( $this->appearance != 'full' ) {
				if ( $this->name ) {
					$attributes['name'] = $this->name;
				}
				$attributes['size'] = ( $this->appearance == 'minimal' ) ? 1 : max( 1, count( $this->options ) );
				$str = $this->renderTag( 'select', $attributes );
				foreach ( $this->options as $val => $label ) {
					$str .= '<option value="' . htmlspecialchars( $val, ENT_QUOTES ) . '"';
					if ( isset( $this->value ) && $this->value == $val ) {
						$str .= ' selected="selected"';
					}
					$str .= '>' . htmlspecialchars( $label ) . '</option>';
				}
				return $str . '</select>';
			} else {
				$str = '';
				$optionType = is_a( $this, 'xoops_pyro_FormSelect1' ) ? 'radio' : 'checkbox';
				$name = $this->name ? ( ' name="' . htmlspecialchars( $this->name, ENT_QUOTES ) . '"' ) : '';
				foreach ( $this->options as $val => $label ) {
					$str .= '<div class="xoops_pyro_FormSelect-option">';
					$str .= "<input type=\"$optionType\"$name value=\"" . htmlspecialchars( $val, ENT_QUOTES ) . '"';
					if ( isset( $this->value ) && $this->value == $val ) {
						$str .= ' checked="checked"';
					}
					$str .= ' />' . $label . '</div>';
				}
				return $str;
			}
		}
		return $this->renderTag() . $this->renderLabel() . "</$this->tagName>";
	}

}

/**
 * Form widget for the XForms 'select1' element
 * 
 * When rendered as an XForms element, this widget will display a range control and
 * degrade gracefully to a select element when using HTML.
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormSelect1 extends xoops_pyro_FormSelect {

	var $tagName = 'select1';
	
	var $appearance = 'minimal';

}



?>