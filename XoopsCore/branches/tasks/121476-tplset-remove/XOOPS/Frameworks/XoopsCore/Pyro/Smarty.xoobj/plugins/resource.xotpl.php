<?php
/**
 * Smarty plugin
 * 
 * Kept for XOOPS 2.0 backward compatibility only !!!
 * 
 * Use the new xotpl: handler to access XOOPS templates
 * ------------------------------------------------------------- 
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * -------------------------------------------------------------
 * @package xoops20
 * @deprecated
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
	
	if ( $smarty->currentTheme ) {
		$name = str_replace( '/templates/', '/', $tplName );
		$themed = $smarty->currentTheme->resourcePath( $name );
		if ( substr( $themed, 0, 7 ) == 'themes/' ) {
			$tplName = $themed;
		} elseif ( substr( $tplName, -6 ) == '.xotpl' ) {
			// If the template is not in the theme folder,
			// check if it's not here, but with an .html extension
			$name = substr( $name, 0, -6 ) . '.html';
			$themed = $smarty->currentTheme->resourcePath( $name );
			if ( substr( $themed, 0, 7 ) == 'themes/' ) {
				$tplName = $themed;
			}
		}
	}
	return $xoops->path( $tplName );

}



?>