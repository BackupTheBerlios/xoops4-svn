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
define('XOOPS_CPFUNC_LOADED', 1);

function xoops_cp_header()
{
    global $xoopsConfig, $xoopsUser, $xoopsOption, $xoopsTpl;
    $GLOBALS['xoopsLogger']->context = "core";

    //  Disabling gzip compression since it is causing problems. Will have to investigate a better way to use it - also frontside
    //	if ($xoopsConfig['gzip_compression'] == 1) {
    //		ob_start("ob_gzhandler");
    //	} else {
    //  ob_start();
    //	}
    if (!headers_sent()) {
        header('Content-Type:text/html; charset='._CHARSET);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header('Cache-Control: no-store, no-cache, max-age=1, s-maxage=1, must-revalidate, post-check=0, pre-check=0');
        header("Pragma: no-cache");
    }
    $moduleperm_handler =& xoops_gethandler('groupperm');
    $admin_mids = $moduleperm_handler->getItemIds('module_admin', $xoopsUser->getGroups());


    $module_handler =& xoops_gethandler('module');
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('hasadmin', 1));
    $criteria->add(new Criteria('isactive', 1));
    $criteria->add(new Criteria('mid', "(".implode(',', $admin_mids).")", 'IN'));
    $criteria->setSort('name');
    $mods =& $module_handler->getObjects($criteria, true);

    //load module items
    $adminmenu = array();
    include_once XOOPS_ROOT_PATH."/modules/system/constants.php";
    $isPrefAdmin = $moduleperm_handler->checkRight('system_admin', XOOPS_SYSTEM_PREF, $xoopsUser->getGroups());

    //load system items
    $system_rights = $moduleperm_handler->getItemIds('system_admin', $xoopsUser->getGroups());
    if (count($system_rights) > 0 && is_object($mods[1])) {
        $menuitems = $mods[1]->getAdminMenu();
        if (count($menuitems) > 0) {
            foreach ($system_rights as $k) {
                if (isset($menuitems[$k])) {

                    $menuitems[$k]['link'] = trim($menuitems[$k]['link']);
                    $menuitems[$k]['target'] = isset($menuitems[$k]['target']) ? trim($menuitems[$k]['target']) : '';

                    if (isset($menuitems[$k]['absolute']) && $menuitems[$k]['absolute']) {
                        $menuitems[$k]['link'] = (empty($menuitems[$k]['link'])) ? "#" : $menuitems[$k]['link'];
                    } else {
                        $menuitems[$k]['link'] = (empty($menuitems[$k]['link'])) ? "#" : XOOPS_URL."/modules/".$mods[1]->getVar('dirname')."/".$menuitems[$k]['link'];
                    }
                }
            }
        }
        $systemmenu = array('name' => $mods[1]->getVar('name'),
        'dirname' => $mods[1]->getVar('dirname'),
        'version' => $mods[1]->getVar('version'),
        'links' => $menuitems);
    }

    if ($isPrefAdmin) {
        if (file_exists(XOOPS_ROOT_PATH."/modules/system/language/".$xoopsConfig['language']."/admin/preferences.php")) {
            include_once(XOOPS_ROOT_PATH."/modules/system/language/".$xoopsConfig['language']."/admin/preferences.php");
        }
        else {
            include_once(XOOPS_ROOT_PATH."/modules/system/language/english/admin/preferences.php");
        }
        $confcat_handler =& xoops_gethandler('configcategory');
        $confcats =& $confcat_handler->getObjects(new Criteria('confcat_modid', "(".implode(',', array_keys($mods)).")", "IN"));
        foreach (array_keys($confcats) as $i) {
            $modnames[$confcats[$i]->getVar('confcat_modid')] = ucfirst($mods[$confcats[$i]->getVar('confcat_modid')]->getVar('name'));
            if ($confcats[$i]->getVar('confcat_modid') == 1) {
                //Put System module at the top of the list
                $modnames[$confcats[$i]->getVar('confcat_modid')] = " ".$mods[$confcats[$i]->getVar('confcat_modid')]->getVar('name');
            }
        }
        foreach (array_keys($confcats) as $i) {
            $mods[$confcats[$i]->getVar('confcat_modid')]->loadLanguage('modinfo');
            $menulinks[$confcats[$i]->getVar('confcat_modid')]['name'] = $mods[$confcats[$i]->getVar('confcat_modid')]->getVar('name');
            $menulinks[$confcats[$i]->getVar('confcat_modid')]['cats'][] = array(
            'link' => XOOPS_URL."/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=".$confcats[$i]->getVar('confcat_modid')."&amp;confcat_id=".$confcats[$i]->getVar('confcat_id'),
            'title' => constant($confcats[$i]->getVar('confcat_name'))
            );
        }
        array_multisort($modnames, SORT_ASC, $menulinks);
        $systemmenu['links'][XOOPS_SYSTEM_PREF]['sublinks'] = $menulinks;
    }

    foreach (array_keys($mods) as $i) {
        if ($i > 1) {
            if (in_array($mods[$i]->getVar('mid'), $admin_mids)) {
                $menuitems = $mods[$i]->getAdminMenu();

                if (count($menuitems) > 0) {
                    foreach (array_keys($menuitems) as $k) {
                        $menuitems[$k]['link'] = trim($menuitems[$k]['link']);
                        $menuitems[$k]['target'] = isset($menuitems[$k]['target']) ? trim($menuitems[$k]['target']) : '';

                        if (isset($menuitems[$k]['absolute']) && $menuitems[$k]['absolute']) {
                            $menuitems[$k]['link'] = (empty($menuitems[$k]['link'])) ? "#" : $menuitems[$k]['link'];
                        } else {
                            $menuitems[$k]['link'] = (empty($menuitems[$k]['link'])) ? "#" : XOOPS_URL."/modules/".$mods[$i]->getVar('dirname')."/".$menuitems[$k]['link'];
                        }
                    }
                    $adminmenu[$mods[$i]->getVar('mid')] = array('name' => $mods[$i]->getVar('name'),
                        'dirname' => $mods[$i]->getVar('dirname'),
                        'version' => $mods[$i]->getVar('version'),
                        'links' => $menuitems);
                }
            }
        }
    }

    $xoopsTpl->xoops_setCaching(0);

    $xoopsTpl->assign_by_ref('adminmenu', $adminmenu);
    $xoopsTpl->assign_by_ref('systemmenu', $systemmenu);
    $adminmenucount = (count($system_rights) > 0) ? count($adminmenu) + 1 : count($adminmenu);
    $xoopsTpl->assign('adminmenucount', $adminmenucount);

    $GLOBALS['xoopsLogger']->context = "module";
}

function xoops_cp_footer()
{
    global $xoopsConfig, $xoopsLogger, $xoopsTpl, $xoopsOption, $xoopsModule, $xoopsUser, $xTheme;

    include XOOPS_ROOT_PATH."/footer.php";
}

// We need these because theme files will not be included
function OpenTable()
{
    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";
}

function CloseTable()
{
    echo '</td></tr></table>';
}

function themecenterposts($title, $content)
{
    echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer"><tr><td class="head">'.$title.'</td></tr><tr><td><br />'.$content.'<br /></td></tr></table>';
}

function myTextForm($url , $value)
{
    return '<form action="'.$url.'" method="post"><input type="submit" value="'.$value.'" /></form>';
}
/*
function xoopsfwrite()
{
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
return false;
} else {

}
if (!xoops_refcheck()) {
return false;
} else {

}
return true;
}
*/
function xoops_module_get_admin_menu()
{

}

function xoops_module_write_admin_menu($content)
{

}
?>