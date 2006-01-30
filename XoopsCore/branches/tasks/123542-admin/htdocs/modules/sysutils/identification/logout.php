<?php

	require_once '../../../mainfile.php';

	global $xoops;

	if ( !$xoops->currentUser ) {
		return $xoops->services['http']->sendResponse( 303, '', $xoops->url( 'mod_xoops_Identification#login' ) );
	}
	
	$xoops->services['session']->destroy();

	// clear entry from online users table
	global $xoopsUser;
	$online_handler =& xoops_gethandler( 'online' );
	$online_handler->destroy( $xoopsUser->getVar('uid') );

	return $xoops->services['http']->sendResponse( 200, XO_('You are now logged out. Thanks for your visit'), $xoops->url( '/www/' ) );
	
?>