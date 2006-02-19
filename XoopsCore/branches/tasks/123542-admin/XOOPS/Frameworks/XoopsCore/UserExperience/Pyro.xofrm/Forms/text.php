<?php
/**
 * xoops_pyro_FormText main class file
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
 * Form widget for the XForms 'input' element
 * 
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormText extends xoops_pyro_FormElement {

	var $tagName = 'input';

	var $attributes = array();

	function renderControl() {
		$attrs = $this->attributes;
		if ( $this->renderAsHtml ) {
			$attrs['type'] = 'text';
			$attrs['value'] = $this->value;
			if ( $this->name ) {
				$attrs['name'] = $this->name;
			}
			return $this->renderTag( 'input', $attrs, true );
		} else {
			if ( $this->refPath ) {
				$attrs['ref'] = $this->refPath;
			}
			return $this->renderTag( $this->tagName, $attrs ) . $this->renderLabel() . "</$this->tagName>";
		}
	}

}
/**
 * Form widget for the XForms 'secret' element
 * 
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormSecret extends xoops_pyro_FormText {

	var $attributes = array( 'type' => 'password' );

}



?>