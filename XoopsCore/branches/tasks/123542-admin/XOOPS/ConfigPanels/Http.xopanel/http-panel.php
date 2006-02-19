<?php
/**
 * HTTP service configuration panel class definition
 */

XOS::import( 'xoops_panel_ConfigurationPanel' );

/**
 * HTTP service configuration panel
 * 
 * @package xoops_http
 * @subpackage xoops_http_HttpConfigPanel
 */
class xoops_http_HttpConfigPanel extends xoops_panel_ConfigurationPanel {
	
	var	$views = array(
		'default' => 'xoops_http_HttpConfigPanel#templates/http-main-view.xotpl',
	);
	var $currentView = 'default';

	
	var $useHostConfiguration = true;

	var $preferencesBag = array();
	
	/**
	 * Host domain the edited preferences apply to (XO_PREFS_ANYHOST or XO_PREFS_CURRENTHOST)
	 * @var string
	 */
	var $domainHost = '';
	/**
	 * User domain the edited preferences apply to (XO_PREFS_ANYUSER or XO_PREFS_CURRENTUSER)
	 * @var string
	 */
	var $domainUser = 0;
	
	/**
	 * The PreferencesHandler to use to store/retrieve settings
	 * @var xoops_core_PreferencesHandler
	 */
	var $preferencesHandler = false;


	var $parametersDef = array(
		'enableCompression' => array( null, XO_TYPE_BOOL ),
		'compressionLevel' => array( null, XO_TYPE_INT ),

		'enableRedirections' => array( null, XO_TYPE_BOOL ),
		'fakeRedirectDelay' => array( null, XO_TYPE_INT ),
		'fakeRedirectTemplate' => array( null, XO_TYPE_MARKUP ),
		'cacheLimiter' => array( null, XO_TYPE_STRING ),
		'cacheLifetime' => array( null, XO_TYPE_INT ),
	);
	


	function xoInit( $options = array() ) {
		$this->preferencesBag = $this->preferencesHandler->getMultiple( null, 'xoops_http_HttpHandler', $this->domainUser, $this->domainHost );
		return parent::xoInit( $options );
	}
	
	function performAction() {
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			foreach ( $this->request as $k => $v ) {
				if ( isset($v) ) {
					$this->preferencesHandler->setValue( $k, $v, 'xoops_http_HttpHandler', $this->domainUser, $this->domainHost, false );
				}
			}
			$this->preferencesHandler->synchronize( 'xoops_http_HttpHandler', $this->domainUser, $this->domainHost );
			$this->currentView = '';
			$this->responseCode = array( 303, XO_("Preferences successfully updated"), $_SERVER['REQUEST_URI'] );
		}
	}
	
	
	function sendResponse() {
		
		if ( $this->currentView ) {
			$form = XOS::create( 'xoops_pyro_Form', array(
				'instances' => array( 'preferences' => &$this->preferencesBag ),
				'submissions' => array( 'save-prefs' => array( $_SERVER['REQUEST_URI'], 'urlencoded-post' ) ),
				'renderAsHtml' => true,
			) );

			$this->viewVars['form'] = $form;
			$this->viewVars['prefs'] = $this->preferencesBag;
		}
		return parent::sendResponse();
	}
	
}

?>