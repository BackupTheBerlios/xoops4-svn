<?php
/**
* xoImgUrl Smarty compiler plug-in
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

function smarty_compiler_xoImgUrl( $argStr, &$smarty ) {
	global $xoops;
	
	$argStr = trim( $argStr );
	
	if ( isset($smarty->template_engine->currentTheme) ) {
		$path = $smarty->template_engine->currentTheme->resourcePath( $argStr );
	} else {
		$path = $argStr;
	}

	return "\necho '" . addslashes( $xoops->url( $path ) ) . "';";

} 


?>