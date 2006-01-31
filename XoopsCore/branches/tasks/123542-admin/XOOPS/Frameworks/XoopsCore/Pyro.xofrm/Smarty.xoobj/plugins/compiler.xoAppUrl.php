<?php
/**
* xoAppUrl Smarty compiler plug-in
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

function smarty_compiler_xoAppUrl( $argStr, &$smarty ) {
	global $xoops;
	
	$argStr = trim( $argStr );

	@list( $modId, $location ) = explode( '#', $argStr, 2 );
	if ( isset( $location ) ) {
		if ( $module = $xoops->loadModule( $modId ) ) {
			$uri = $module->xoBundleRoot;
			if ( isset( $location ) && isset( $module->moduleLocations[$location] ) ) {
				$uri .= $module->moduleLocations[$location]['scriptFile'];
			}
		}
	} else {
		$uri = $argStr;
	}
	
	return "\necho '" . addslashes( $xoops->path( $uri, true ) ) . "';";

}

?>