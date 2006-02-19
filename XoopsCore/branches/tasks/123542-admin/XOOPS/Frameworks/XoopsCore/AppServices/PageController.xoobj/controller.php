<?php
/**
 * xoops_app_PageController main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_app
 * @subpackage	xoops_app_PageController
 * @version		$Id$
 */

/**
 * Base class for page controllers
 * 
 * The page controller receives a page request, extracts any relevant data,
 * invokes any updates to the model, and if necessary forwards the request to the view.
 * @package		xoops_app
 * @subpackage	xoops_app_PageController
 */
class xoops_app_PageController {
	/**
	 * Associative array containing the request parameters. If uninitialized, $_REQUEST will be used
	 * @var array
	 */
	var $request = false;
	/**
	 * If the specified $request array is concerned by get_magic_quotes_gpc()
	 * @var boolean
	 */
	var $requestIsGpc = false;
	/**
	 * Current request method (defaults to $_SERVER['REQUEST_METHOD'] )
	 *
	 * @var string
	 */
	var $requestMethod = '';
	/**
	 * Current request URI (defaults to $_SERVER['REQUEST_URI'] )
	 *
	 * @var string
	 */
	var $requestUri = '';
	/**
	 * Parameters definition
	 *
	 * @var array
	 */
	var $parametersDef = array();

	
	/**
	* This controller views as 'template/path' or '[virtual/path]'
	*/
	var $views = array();
	/**
	* Current view
	*/
	var $currentView = '';
	var $viewVars = array();
	/**
	 * Next page to show if this controller processing has been successful
	 * @var string
	 */
	var $successLocation = '';
	/**
	 * Page to display by default when an error is encountered
	 * @var string
	 */
	var $errorLocation = '';
	/**
	 * Response code to send upon request processing (status, msg, location)
	 * @var array
	 */
	var $responseCode = array( 200, '', false );

 	function xoInit( $options = array() ) {
		global $xoops;

		if ( !is_array($this->request) ) {
		 	$this->request = $_REQUEST;
		 	$this->requestIsGpc = true;
		}
		if ( empty( $this->requestMethod ) ) {
			$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		}
		if ( empty( $this->requestUri ) ) {
			$this->requestUri = $_SERVER['REQUEST_URI'];
		}
 		return true;
	}

	/**
	 * Reads request parameters, sanitizing and checking values
	 * @return boolean Whether or not the request parameters seem OK
	 */
	function readRequest() {
		$this->cleanRequest();
		return $this->checkRequest();
	}
	
	/**
	 * Clean-up the request parameters
	 */
	function cleanRequest() {
		if ( $this->requestIsGpc && get_magic_quotes_gpc() ) {
			$this->request = array_map( 'stripslashes', $this->request );
		}
		if ( !empty($this->parametersDef) ) {
			$this->request = xoops_kernel_Module::requestValues( $this->parametersDef, $this->request );
		}
	}
	/**
	 * Check request parameters
	 * @return boolean
	 */
	function checkRequest() {
		return true;
	}
	/**
	 * Perform actions according to the request
	 */
	function process() {
		return true;
	}

	function sendResponse() {
		global $xoops;

		$xoops->services['http']->sendResponse( $this->responseCode );		
		if ( !empty( $this->currentView ) ) {
			$this->viewVars['xoController'] =& $this;
			$theme =& $xoops->loadService( 'theme' );
			$theme->render( null, null, $this->views[ $this->currentView ], $this->viewVars );
		}
		return $this->responseCode;
	}

	function mergeUrl( $url, $vars = array(), $asArray = false ) {
		if ( !is_array( $url ) ) {
			$url = parse_url( $url );
		}
		if ( isset($url['query']) ) {
			$args=array();
			parse_str( $url['query'], $args );
			if ( !empty($args) ) {
				$vars = array_merge( $args, $vars );
			}
		}
		if ( !$asArray ) {
			foreach ($vars as $k=>$v) {
				$vars[$k] = rawurlencode($k) . '=' . rawurlencode($v);
			}
			$vars = implode( '&', $vars );
			$vars = $url['path'] . (empty($vars)?'':('?'.$vars));
		}
		return $vars;
	}

}

?>