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

function smarty_compiler_xoWidget( $argStr, &$smarty ) {
	global $xoops;
	
	$args = $smarty->_parse_attrs( trim( $argStr ) );
	array_map( array( &$smarty, '_expand_quoted_text' ), $args );
	
	if ( !isset( $args['bundleId'] ) ) {
		trigger_error( "Cannot insert widget: no bundleId specified", E_USER_WARNING );
		return '';
	}
	$bundleId = $args['bundleId'];
	unset( $args['bundleId'] );

	$realBundleId = substr( $bundleId, 1, strlen( $bundleId ) - 2 );
	
	$code = "\n";
	// Get this widget stylesheet and javascript properties
	if ( $css = XOS::classVar( $realBundleId, 'stylesheet' ) ) {
		$css = $realBundleId . '#' . $css;
		$attrs = array(
			'type' => 'text/css',
			'href' => $xoops->url( $smarty->template_engine->currentTheme->resourcePath( $css ) ),
		);
		$code .= '$this->currentTheme->setMeta( "stylesheet", "' . $css . '", ' . var_export( $attrs, true ) . ");\n";
	}
	if ( $js = XOS::classVar( $realBundleId, 'javascript' ) ) {
		$js = $realBundleId . '#' . $js;
		$attrs = array(
			'type' => 'text/javascript',
			'src' => $xoops->url( $smarty->template_engine->currentTheme->resourcePath( $js ) ),
		);
		$code .= '$this->currentTheme->setMeta( "script", "' . $js . '", ' . var_export( $attrs, true ) . ");\n";
	}


	$code .= '$widget = XOS::create( ' . $bundleId;
	if ( !empty( $args ) ) {
		$code .= ", array(\n";
		foreach ( $args as $prop => $value ) {
			$code .= "\t'$prop' => " . $value . ",\n";
		}
		$code .= ')';
	}
	$code .= " );\n";
	$code .= "echo ( \$widget ? \$widget->render() : \"Failed to instanciate $bundleId widget.\" );\n";
	
	return $code;

}

?>