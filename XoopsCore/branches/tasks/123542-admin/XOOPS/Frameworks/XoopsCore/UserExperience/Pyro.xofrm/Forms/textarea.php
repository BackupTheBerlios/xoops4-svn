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
 * Form widget for the XForms 'textarea' element
 * 
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_FormTextarea extends xoops_pyro_FormElement {

	var $tagName = 'textarea';

	function renderControl() {
		$attrs = $this->attributes;
		if ( $this->renderAsHtml ) {
			if ( $this->name ) {
				$attrs['name'] = $this->name;
			}
			return $this->renderTag( 'textarea', $attrs ) . htmlspecialchars( $this->value ) . '</textarea>';
		} else {
			if ( $this->refPath ) {
				$attrs['ref'] = $this->refPath;
			}
			return $this->renderTag( $this->tagName, $attrs ) . $this->renderLabel() . "</$this->tagName>";
		}
	}

}

?>