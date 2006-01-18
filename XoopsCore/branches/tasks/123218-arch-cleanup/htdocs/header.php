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
if (!defined("XOOPS_ROOT_PATH")) {
    die("XOOPS root path not defined");
}

	global $xoops;
	$xoops->loadService( 'theme', 'xoops_pyro_Theme', array( 'folderName' => $xoopsConfig['theme_set'] ) );
	
	//var_export( $xoops->services['theme'] );


    $xoopsOption['theme_use_smarty'] = 1;
    // include Smarty template engine and initialize it
    require_once XOOPS_ROOT_PATH.'/class/template.php';
    $xoopsTpl = new XoopsTpl();
    $xoopsTpl->currentTheme =& $xoops->services['theme'];
    $xoopsTpl->xoops_setCaching(2);
    if ($xoopsConfig['debug_mode'] == 3) {
        $xoopsTpl->xoops_setDebugging(true);
    }
    $xoopsTpl->assign(
    	array(
	    	'xoops_theme' => $xoopsConfig['theme_set'],
	    	'xoops_imageurl' => XOOPS_THEME_URL.'/'.$xoopsConfig['theme_set'].'/',
	    	'xoops_themecss'=> xoops_getcss($xoopsConfig['theme_set']),
	    	'xoops_requesturi' => htmlspecialchars($GLOBALS['xoopsRequestUri'], ENT_QUOTES),
	    	'xoops_sitename' => htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
	    	'xoops_slogan' => htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES)
	    )
   	);

    
    // Meta tags
    $config_handler =& xoops_gethandler('config');
    $criteria = new CriteriaCompo(new Criteria('conf_modid', 0));
    $criteria->add(new Criteria('conf_catid', XOOPS_CONF_METAFOOTER));
    $config = $config_handler->getConfigs($criteria, true);
    foreach ( array_keys($config) as $i ) {
    	if ( substr( $config[$i]->getVar('conf_name'), 0, 5 ) == 'meta_' ) {
    		$xoops->services['theme']->setMeta( 'meta',
    			substr( $config[$i]->getVar('conf_name'), 5 ), $config[$i]->getConfValueForOutput() );
    	}
    }
    //unset($config);
    // show banner?

	$xoops->services['theme']->addScript( 'include/xoops.js' );

    if ($xoopsConfig['banners'] == 1) {
        $xoopsTpl->assign('xoops_banner', xoops_getbanner());
    } else {
        $xoopsTpl->assign('xoops_banner', '&nbsp;');
    }
    // get all blocks and assign to smarty
    if ($xoopsUser != '') {
        $xoopsTpl->assign(array('xoops_isuser' => true, 'xoops_userid' => $xoopsUser->getVar('uid'), 'xoops_uname' => $xoopsUser->getVar('uname'), 'xoops_isadmin' => $xoopsUserIsAdmin));
        $groups = $xoopsUser->getGroups();
    } else {
        $xoopsTpl->assign(array('xoops_isuser' => false, 'xoops_isadmin' => false));
        $groups = XOOPS_GROUP_ANONYMOUS;
    }
    if (isset($xoopsModule) && is_object($xoopsModule)) {
        // set page title
        $xoopsTpl->assign('xoops_pagetitle', $xoopsModule->getVar('name'));
        $xoopsTpl->assign('xoops_dirname', $xoopsModule->getVar('dirname'));
    } else {
        $xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES));
        $xoopsTpl->assign('xoops_dirname', "system");
    }
	
    if (xoops_getenv('REQUEST_METHOD') != 'POST' && !empty($xoopsModule) && !empty($xoopsConfig['module_cache'][$xoopsModule->getVar('mid')])) {
        $xoopsTpl->xoops_setCaching(2);
        $xoopsTpl->xoops_setCacheTime($xoopsConfig['module_cache'][$xoopsModule->getVar('mid')]);
        if (!isset($xoopsOption['template_main'])) {
            $xoopsCachedTemplate = 'db:system_dummy.html';
        } else {
            $xoopsCachedTemplate = 'db:'.$xoopsOption['template_main'];
        }
        // generate safe cache Id
        $xoopsCachedTemplateId = 'mod_'.$xoopsModule->getVar('dirname').'|'.md5(str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']));
        if ($xoopsTpl->is_cached($xoopsCachedTemplate, $xoopsCachedTemplateId)) {
            $xoopsLogger->addExtra($xoopsCachedTemplate, sprintf('Cached (regenerates every %d seconds)', $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')]));
            $xoopsTpl->assign('xoops_contents', $xoopsTpl->fetch($xoopsCachedTemplate, $xoopsCachedTemplateId));
            $xoopsTpl->xoops_setCaching(0);
            if (!headers_sent()) {
                header ('Content-Type:text/html; charset='._CHARSET);
            }
            $xoopsTpl->display($xoopsConfig['theme_set'].'/theme.html');
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
            exit();
        }
    } else {
        $xoopsTpl->xoops_setCaching(0);
    }
    if (!isset($xoopsOption['template_main'])) {
        // new themes using Smarty does not have old functions that are required in old modules, so include them now
        include XOOPS_ROOT_PATH.'/include/old_theme_functions.php';
        // need this also
        $xoopsTheme['thename'] = $xoopsConfig['theme_set'];
    }

?>