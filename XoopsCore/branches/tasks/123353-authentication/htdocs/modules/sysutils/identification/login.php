<?php

	require_once '../../../mainfile.php';

	global $xoops;

	$app =& $xoops->currentModule;
	
	// Read the request params defined in xo-info.php (use default if the var isnt found)
	// sanitize them, and create corresponding global vars
	extract( $app->requestParameters(), EXTR_OVERWRITE );

	if ( empty( $xoops_redirect ) ) {
		$xoops_redirect = $xoops->path( '/www/', true );
	}
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		// Check request parameters: if something is wrong, send an HTTP 'bad request' response
		if ( empty($login) ) {
			return $xoops->services['http']->sendResponse( 400, XO_('You must supply a login name'), $_SERVER['REQUEST_URI'] );
		} else {
			$auth =& $xoops->loadService( 'auth' );
			if ( $login = $auth->checkCredentials( $login, $password, '' ) ) {
				$xoops->acceptUser( $login, true );
			    // Perform some maintenance of notification records
			    $notification_handler =& xoops_gethandler('notification');
			    $notification_handler->doLoginMaintenance($user->getVar('uid'));
				return $xoops->services['http']->sendResponse( 303, sprintf( XO_('Thank you for logging in, %s'), $login ), $xoops_redirect );
			} else {
				return $xoops->services['http']->sendResponse( 403, XO_('Incorrect login'), $_SERVER['REQUEST_URI'] );
			}
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