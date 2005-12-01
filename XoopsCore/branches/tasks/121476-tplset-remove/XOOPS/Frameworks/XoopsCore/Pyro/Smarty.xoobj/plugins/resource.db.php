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

function smarty_resource_db_source($tpl_name, &$tpl_source, &$smarty) {

	if ( $tplPath = smarty_resource_db_lookup( $tpl_name, $smarty ) ) {
		if ( $tpl_source = file_get_contents( $tplPath ) ) {
			return true;
		}
	}
	return false;
}

function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty) {

	if ( $tplPath = smarty_resource_db_lookup( $tpl_name, $smarty ) ) {
		if ( $tpl_timestamp = filemtime( $tplPath ) ) {
			return true;
		}
	}
	return false;
}

function smarty_resource_db_secure($tpl_name, &$smarty) {
    // assume all templates are secure
    return true;
}

function smarty_resource_db_trusted($tpl_name, &$smarty) {
    // not used for templates
}

/**
 * Get a template path from its old-style name
 *
 * @param string	$tplName	XOOPS 2.0 name of the template to search
 * @param bool		$refresh	Set this to true to force regeneration of the cache
 * @return string	Full path to the template (or false if not found)
 */
function smarty_resource_db_lookup( $tplName, &$smarty, $refresh = false ) {
	static $list = null;
	global $xoops;

	if ( !isset($list) ) {
		$list = @include $xoops->path( 'var/Caches/xoops_template_Smarty/dbhandler-list.php' );
	}
	if ( !is_array($list) || $refresh ) {
		// List not found: regenerate it
		$handler =& xoops_gethandler( 'module' );
		$modules = $handler->getList( null, true );
		$templates = array();
	
		foreach ( array_keys( $modules ) as $modname ) {
			$modversion = array();
			if ( @include $xoops->path( "modules/$modname/xoops_version.php" ) ) {
				if ( isset($modversion['templates']) ) {
					foreach ( $modversion['templates'] as $tpl ) {
						$templates[ $tpl['file'] ] = "modules/$modname/templates/" . $tpl['file'];
					}
				}
				if ( isset($modversion['blocks']) ) {
					foreach ( $modversion['blocks'] as $block ) {
						if ( isset( $block['template'] ) ) {
							$templates[ $block['template'] ] = "modules/$modname/templates/blocks/" . $block['template'];
						}
					}
				}
			}
		}
		if ( $fp = fopen( $xoops->path( 'var/Caches/xoops_template_Smarty/dbhandler-list.php' ), 'wt' ) ) {
			fwrite( $fp, "<?php\nreturn " . var_export( $templates, true ) . ";\n?>" );
			fclose( $fp );
			$list = $templates;		// Only save the static if saving was successful
		} else {
			trigger_error( "Cannot create db: resource handler templates list", E_USER_WARNING );
		}
	}
	if ( !function_exists( 'smarty_resource_xotpl_getpath' ) ) {
		$path = str_replace( DIRECTORY_SEPARATOR, '/', dirname( __FILE__ ) );
		include_once "$path/resource.xotpl.php";
	}
	if ( $tplName == 'system_notification_select.html' ) {
		$tplName = 'system_notification_select.xotpl';
	}
	if ( isset( $list[$tplName] ) ) {
		return smarty_resource_xotpl_getpath( $list[$tplName], $smarty );
	}
	return false;
}



?>