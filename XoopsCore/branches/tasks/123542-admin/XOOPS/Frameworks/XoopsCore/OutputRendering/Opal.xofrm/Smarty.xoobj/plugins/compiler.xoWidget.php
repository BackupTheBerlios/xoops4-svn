<?php
/**
* xoWidget Smarty compiler plug-in
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @package		xoops_template
* @subpackage	xoops_template_Smarty
* @since        2.3.0
* @version		$Id$
*/

function smarty_compiler_xoWidget( $argStr, &$compiler ) {
	global $xoops;
	
	$args = $compiler->_parse_attrs( trim( $argStr ) );
	array_map( array( &$compiler, '_expand_quoted_text' ), $args );
	
	if ( isset( $args['tplVar'] ) ) {
		$code = '';
		foreach ( $args as $prop => $value ) {
			if ( $prop != 'tplVar' ) {
				$code .= $args['tplVar'] . "->$prop = $value;\n";
			}
		}
		return $code . "echo " . $args['tplVar'] . "->render();";
	}
	
	
	if ( !isset( $args['bundleId'] ) ) {
		trigger_error( "Cannot insert widget: no bundleId specified", E_USER_WARNING );
		return '';
	}
	// Transform __property__key settings to an arrays
	$arrays = array();
	foreach ( $args as $k => $v ) {
		if ( substr( $k, 0, 2 ) == '__' ) {
			$prop = explode( '__', substr( $k, 2 ), 2 );
			if ( isset( $prop[1] ) ) {
				$arrays[ $prop[0] ][ $prop[1] ] = $v;
			} else {
				$arrays[ $prop[0] ][] = $v;
			}
			unset( $args[$k] );
		}
	}
	if ( !empty( $arrays ) ) {
		foreach ( $arrays as $aname => $array ) {
			$args[$aname] = 'array(';
			foreach ( $array as $k => $v ) {
				$args[$aname] .= var_export($k,true) . ' => ' . $v . ', ';
			}
			$args[$aname] .= ')';
		}
	}
	
	return $compiler->insertWidget( $args );
	
}

?>