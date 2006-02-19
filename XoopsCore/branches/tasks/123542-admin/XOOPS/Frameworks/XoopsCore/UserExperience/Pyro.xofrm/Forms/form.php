<?php
/**
 * xoops_pyro_Form component main class file
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
 * Pyro form widget
 * 
 * The form widgets have been modelled after the XForms specification.
 * When rendered as HTML form elements, some of the features (like support for multiple submission locations)
 * are emulated using javascript, so you should restrict yourself to the features set supported
 * by HTML forms if you don't want your forms to rely on client scripting.
 * @package		xoops_pyro
 * @subpackage 	xoops_pyro_Forms
 */
class xoops_pyro_Form extends xoops_opal_Widget {
	
	/**
	 * The different locations where this form can be submitted
	 * @var array
	 */
	var $submissions = array();
	/**
	 * Default submission id (used when rendering as HTML)
	 * @var string
	 */
	var $defaultSubmission = '';
	
	var $tagName = 'form';
	var $renderAsHtml = true;
	
	function xoInit( $options = array(), $attributes = array() ) {
	  	$attributes = array_merge( array( 'method' => 'post', 'action' => $_SERVER['REQUEST_URI'] ), $attributes );
		parent::xoInit( $options, $attributes );

		//$this->xoElements =& $GLOBALS['xoops2']->create( 'xoops.form.group' );
		//$this->elements =& $this->xoElements->elements;
		return true;
	}
	
	/**
	 * Add a submission URI to this form
	 * 
	 * As XForms, pyro forms let you specify several submission locations. When a form accepting more than
	 * one submission location is rendered as HTML, this functionality will be emulated by adding javascript
	 * event handlers to the submit buttons. Thus, you should not add more than one submission location if 
	 * you want your form to work with Javascript disabled.
	 * 
	 * The submission method should be specified using valid XForms submission methods:
	 * - get (transformed to get in HTML)
	 * - urlencoded-post (post + application/x-www-form-urlencoded in HTML)
	 * - form-post-data (post + multipart/form-data in HTML)
	 *
	 * @param string $id ID of this submission
	 * @param string $action Location to submit this form data to
	 * @param string $method Method used
	 */
	function addSubmission( $id, $action = '', $method = 'get' ) {
		if ( empty( $this->submissions ) ) {
			$this->defaultSubmission = $id;
		}
		$this->submissions[$id] = array( empty($action) ? $_SERVER['REQUEST_URI'] : $action, $method );
	}
	
	
	function addElement( &$elt ) {
		$elt->ownerForm =& $this;
		$this->elements[] =& $elt;
	}

	function renderdTag() {
		
	}
	
}

?>