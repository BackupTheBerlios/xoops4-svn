<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_ROOT_PATH') )			die();

if ( defined( 'XOOPS_FOOTER_INCLUDED' ) )	return;


	define( 'XOOPS_FOOTER_INCLUDED', 1 );
	$xoopsLogger->stopTime();

	// RMV-NOTIFY
	include_once XOOPS_ROOT_PATH . '/include/notification_select.php';
	
	if (!headers_sent()) {
		header('Content-Type:text/html; charset='._CHARSET);
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		//header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: private, no-cache');
		header('Pragma: no-cache');
	}
	
	global $xoops;
	if ( !$xoops->services['theme']->renderCount ) {
		if ( isset( $xoopsOption['template_main'] ) ) {
			if ( strpos( $xoopsOption['template_main'], ':' ) === false ) {
				$xoops->services['theme']->contentTemplate = 'db:' . $xoopsOption['template_main'];
			} else {
				$xoops->services['theme']->contentTemplate = $xoopsOption['template_main'];
			}
		}
		$xoops->services['theme']->template->assign( $xoopsTpl->_tpl_vars );
		$xoops->services['theme']->render();
	}
		
	if ($xoopsConfig['debug_mode'] == 2 && $xoopsUserIsAdmin) {
		echo '<script type="text/javascript">
		<!--//
		debug_window = openWithSelfMain("", "popup", 680, 450, true);
		debug_window.document.clear();
		';
		$content = '<html><head><meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" /><meta http-equiv="content-language" content="'._LANGCODE.'" /><title>'.$xoopsConfig['sitename'].'</title><link rel="stylesheet" type="text/css" media="all" href="'.getcss($xoopsConfig['theme_set']).'" /></head><body>'.$xoopsLogger->dumpAll().'<div style="text-align:center;"><input class="formButton" value="'._CLOSE.'" type="button" onclick="javascript:window.close();" /></div></body></html>';
		$lines = preg_split("/(\r\n|\r|\n)( *)/", $content);
		foreach ($lines as $line) {
			echo 'debug_window.document.writeln("'.str_replace('"', '\"', $line).'");';
		}
		echo '
		debug_window.focus();
		debug_window.document.close();
		//-->
		</script>';
	}


?>
