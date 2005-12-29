<?php
/**
 * Smarty xotpl resource handler
 * 
 * The xotpl resource handler is integrated with the core theming layer.
 * It may be very resource inten
 * 
 * 
 * ------------------------------------------------------------- 
 */
function smarty_resource_xotpl_source($tpl_name, &$tpl_source, &$smarty) {

	if ( $tplPath = smarty_resource_xotpl_getpath( $tpl_name, $smarty ) ) {
		if ( $tpl_source = file_get_contents( $tplPath ) ) {
			$smarty->realTemplatePath = $tplPath;
			return true;
		}
	}
	return false;
}

function smarty_resource_xotpl_timestamp($tpl_name, &$tpl_timestamp, &$smarty) {

	if ( $tplPath = smarty_resource_xotpl_getpath( $tpl_name, $smarty ) ) {
		if ( $tpl_timestamp = filemtime( $tplPath ) ) {
			return true;
		}
	}
	return false;
}

function smarty_resource_xotpl_secure($tpl_name, &$smarty) {
    return true;
}

function smarty_resource_xotpl_trusted($tpl_name, &$smarty) {
	return false;
}

function smarty_resource_xotpl_getpath( $tplName, &$smarty ) {
	global $xoops;
	
	$realpath = '';
	$name = str_replace( 'templates/', '', $tplName );
	$pos = strrpos( $name, '.' );
	$basename = substr( $name, 0, $pos );
	$ext = substr( $name, $pos + 1 );

	if ( $smarty->currentTheme ) {
		// Look for an .xotpl version of the template first, then try with .html
		// this allows modules devs to provide both 2.0.x and 2.3.x templates
		// and ensure the correct one is used by 2.3+
		$themed = $smarty->currentTheme->resourcePath( "$basename.xotpl" );
		if ( substr( $themed, 0, 7 ) == 'themes/' ) {
			$realpath = $themed;
		} else {
			$themed = $smarty->currentTheme->resourcePath( "$basename.html" );
			if ( substr( $themed, 0, 7 ) == 'themes/' ) {
				$realpath = $themed;
			}
		}
	}

	if ( empty( $realpath ) ) {
		$realpath = substr( $tplName, 0, $pos + 10 );
		if ( file_exists( $xoops->path( "$realpath.xotpl" ) ) ) {
			$realpath .=  '.xotpl';
		} else {
			$realpath .= '.html';
		}
	}
	return $xoops->path( $realpath );
}



?>