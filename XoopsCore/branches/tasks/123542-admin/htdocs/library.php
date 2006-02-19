<?php

	$path = @$_SERVER['PATH_INFO'];
	if ( strpos( $path, '/www/' ) === false ) {
		return;
	}
	if ( strpos( $path, '.php' ) !== false ) {
		return;
	}

	$GLOBALS['xoPreventDevMode'] = true;
	
	require_once 'mainfile.php';

	global $xoops;
	
	$ext = substr( $path, strrpos( $path, '.' ) + 1 );
	switch ( $ext ) {
	case 'css':
		header( 'Content-type: text/css' );
		break;
	case 'js':
		header( 'Content-type: text/javascript' );
		break;
	case 'png':
		header( 'Content-type: image/png' );
		break;
	case 'jpg':
	case 'jpeg':
		header( 'Content-type: image/jpeg' );
		break;
	case 'gif':
		header( 'Content-type: image/gif' );
		break;
	default:
		header( 'Content-type: text/html' );
		break;
	}
	echo @file_get_contents( $xoops->path( 'XOOPS' . $path ) );

	if ( $xoops->services['logger'] ) {
		$xoops->services['logger']->activated = false;
	}
?>