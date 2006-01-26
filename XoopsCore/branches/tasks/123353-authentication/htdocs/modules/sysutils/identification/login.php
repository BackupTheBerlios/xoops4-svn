<?php



	require_once '../../../mainfile.php';

	global $xoops;

	$app =& $xoops->loadModule();
	
	// Read the request params defined in xo-info.php (use default if the var isnt found)
	// sanitize them, and create corresponding global vars
	extract( $app->requestParameters(), EXTR_OVERWRITE );
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		// Check request parameters: if something is wrong, send an HTTP 'bad request' response
		if ( empty($login) ) {
			return $xoops->services['http']->sendResponse( 400, XOT_('You must supply a login name'), $_SERVER['REQUEST_URI'] );
		}
		$auth =& $xoops->loadService( 'auth' );
		if ( $auth->login( $login, $password, '' ) ) {
			$xoops->services['session']->start();
			return $xoops->services['http']->sendResponse( 303, XOT_('Thank you for logging in,') . " $login", $xoops_redirect );
		} else {
			return $xoops->services['http']->sendResponse( 403, XOT_('Incorrect login'), $_SERVER['REQUEST_URI'] );
		}
	}

	$theme =& $xoops->loadService( 'theme' );
	
	$outputVars = array(
		'login' => $login,
		'password' => '',
	);
	$theme->addStylesheet( 'mod_xoops_Identification#style.css' );
	$theme->render( null, null, 'mod_xoops_Identification#templates/page-login.xotpl', $outputVars );


?>