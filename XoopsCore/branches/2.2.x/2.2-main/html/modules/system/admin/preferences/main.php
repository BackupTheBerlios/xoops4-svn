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
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if ( !is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid()) ) {
    exit("Access Denied");
} else {
    $op = isset($_REQUEST['op']) ? trim($_REQUEST['op']) : "list";

    if ($op == 'list') {
        $gperm_handler =& xoops_gethandler('groupperm');
        $admin_mids =& $gperm_handler->getItemIds('module_admin', $xoopsUser->getGroups());
        if ($gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_PREF, $xoopsUser->getGroups())) {
            array_push($admin_mids, 1);
        }

        $confcat_handler =& xoops_gethandler('configcategory');
        $confcats =& $confcat_handler->getObjects(new Criteria('confcat_modid', "(".implode(',', $admin_mids).")", "IN"));
        $catcount = count($confcats);

        foreach (array_keys($confcats) as $i) {
            $modcats[$confcats[$i]->getVar('confcat_modid')]['categories'][$confcats[$i]->getVar('confcat_id')] =& $confcats[$i];
        }

        $module_handler =& xoops_gethandler('module');
        $modules =& $module_handler->getList(new Criteria('mid', "(".implode(',', array_keys($modcats)).")", 'IN'));

        foreach (array_keys($modules) as $modid) {
            $modcats[$modid]['module'] = $modules[$modid];
            $modnames[$modid] = ucfirst($modules[$modid]);
            if ($modid == 1) {
                //Put System module at the top of the list
                $modnames[$modid] = " ".$modules[$modid];
            }
        }
        array_multisort($modnames, SORT_ASC, $modcats);

        xoops_cp_header();
        foreach (array_keys($modcats) as $k) {
            echo '<h4 style="text-align:left">'.sprintf(_MD_AM_SITEPREF, $modcats[$k]['module']).'</h4><ul>';
            foreach (array_keys($modcats[$k]['categories']) as $i) {
                echo '<li>'.constant($modcats[$k]['categories'][$i]->getVar('confcat_name')).' [<a href="admin.php?fct=preferences&amp;op=showmod&amp;confcat_id='.$modcats[$k]['categories'][$i]->getVar('confcat_id').'&amp;mod='.$modcats[$k]['categories'][$i]->getVar('confcat_modid').'">'._EDIT.'</a>]</li>';
            }
            echo '</ul>';
        }
        xoops_cp_footer();
        exit();
    }

    if ($op == 'showmod') {

        $mod = isset($_REQUEST['mod']) ? intval($_REQUEST['mod']) : 1;
        $module_handler =& xoops_gethandler('module');
        $module =& $module_handler->get($mod);
        $confcat_id = isset($_REQUEST['confcat_id']) ? intval($_REQUEST['confcat_id']) : 0;

        $confcat_handler =& xoops_gethandler('configcategory');
//        if ($confcat_id == 0) {
//            $confcat = $confcat_handler->getCatByModule($module->getVar('mid'), false);
//            $confcat = $confcat[0];
//            $confcat_id = $confcat->getVar('confcat_id');
//        }
//        else {
            $confcat =& $confcat_handler->get(array($confcat_id, $mod));
//        }
        $configcriteria = new CriteriaCompo(new Criteria('conf_catid', $confcat_id));
        $configcriteria->add(new Criteria('conf_modid', $mod));

        $config_handler =& xoops_gethandler('config');
        $config =& $config_handler->getConfigs($configcriteria);
        unset($configcriteria);

        $count = count($config);
        if ($count < 1) {
            redirect_header('admin.php?fct=preferences', 1);
        }

        $module->loadLanguage('modinfo');
        include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
        $form = new XoopsThemeForm(constant($confcat->getVar('confcat_name')), 'pref_form', 'admin.php?fct=preferences', 'post', true);

        // if has comments feature, need comment lang file
        if ($module->getVar('hascomments') == 1) {
            include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/comment.php';
        }
        // RMV-NOTIFY
        // if has notification feature, need notification lang file
        if ($module->getVar('hasnotification') == 1) {
            include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/notification.php';
        }

        $modname = $module->getVar('name');
        for ($i = 0; $i < $count; $i++) {
            $title = (!defined($config[$i]->getVar('conf_desc')) || constant($config[$i]->getVar('conf_desc')) == '') ? constant($config[$i]->getVar('conf_title')) : constant($config[$i]->getVar('conf_title')).'<br /><br /><span style="font-weight:normal;">'.constant($config[$i]->getVar('conf_desc')).'</span>';
            switch ($config[$i]->getVar('conf_formtype')) {
                case 'textarea':
                $myts =& MyTextSanitizer::getInstance();
                if ($config[$i]->getVar('conf_valuetype') == 'array') {
                    // this is exceptional.. only when value type is arrayneed a smarter way for this
                    $ele = ($config[$i]->getVar('conf_value') != '') ? new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars(implode('|', $config[$i]->getConfValueForOutput())), 5, 50) : new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), '', 5, 50);
                } else {
                    $ele = new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()), 5, 50);
                }
                break;
                case 'select':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $options =& $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                $opcount = count($options);
                for ($j = 0; $j < $opcount; $j++) {
                    $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                    $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                    $ele->addOption($optval, $optkey);
                }
                break;
                case 'select_multi':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                $options =& $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                $opcount = count($options);
                for ($j = 0; $j < $opcount; $j++) {
                    $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                    $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                    $ele->addOption($optval, $optkey);
                }
                break;
                case 'yesno':
                $ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
                break;
                case 'group':
                include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
                $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                break;
                case 'group_multi':
                include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
                $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), true, $config[$i]->getConfValueForOutput(), 5, true);
                break;
                // RMV-NOTIFY: added 'user' and 'user_multi'
                case 'user':
                include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
                $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), true, $config[$i]->getConfValueForOutput());
                break;
                case 'user_multi':
                include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
                $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), true, 10, true);
                break;
                case 'password':
                $myts =& MyTextSanitizer::getInstance();
                $ele = new XoopsFormPassword($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;
                case 'textbox':
                default:
                $myts =& MyTextSanitizer::getInstance();
                $ele = new XoopsFormText($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;
                case 'theme':
                case 'theme_multi':
                $ele = ($config[$i]->getVar('conf_formtype') != 'theme_multi') ? new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput()) : new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                $handle = opendir(XOOPS_THEME_PATH.'/');
                $dirlist = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_dir(XOOPS_THEME_PATH.'/'.$file) && !preg_match("/^[.]{1,2}$/",$file) && strtolower($file) != 'cvs') {
                        if (file_exists(XOOPS_THEME_PATH."/".$file."/theme.html") || file_exists(XOOPS_THEME_PATH."/".$file."/theme.php")) {
                            $dirlist[$file]=$file;
                        }
                    }
                }
                closedir($handle);
                if (!empty($dirlist)) {
                    asort($dirlist);
                    $ele->addOptionArray($dirlist);
                }
                break;

                case 'theme_admin':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $ele->addOption(0, _MD_AM_FRONTSIDE_THEME);

                $handle = opendir(XOOPS_THEME_PATH.'/');
                $dirlist = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_dir(XOOPS_THEME_PATH.'/'.$file) && !preg_match("/^[.]{1,2}$/",$file) && strtolower($file) != 'cvs') {
                        if (file_exists(XOOPS_THEME_PATH."/".$file."/themeadmin.html")) {
                            $dirlist[$file]=$file;
                        }
                    }
                }
                closedir($handle);
                if (!empty($dirlist)) {
                    asort($dirlist);
                    $ele->addOptionArray($dirlist);
                }
                break;

                case 'tplset':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $tplset_handler =& xoops_gethandler('tplset');
                $tplsetlist =& $tplset_handler->getList();
                asort($tplsetlist);
                foreach ($tplsetlist as $key => $name) {
                    $ele->addOption($key, $name);
                }
                break;
                case 'timezone':
                $ele = new XoopsFormSelectTimezone($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                break;
                case 'language':
                $ele = new XoopsFormSelectLang($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                break;
                case 'startpage':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $module_handler =& xoops_gethandler('module');
                $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
                $criteria->add(new Criteria('isactive', 1));
                $moduleslist =& $module_handler->getList($criteria, true);
                $moduleslist['--'] = _MD_AM_NONE;
                $ele->addOptionArray($moduleslist);
                break;
                case 'site_cache':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $ele->addOptionArray(array('0' => _NOCACHE, '30' => sprintf(_SECONDS, 30), '60' => _MINUTE, '300' => sprintf(_MINUTES, 5), '1800' => sprintf(_MINUTES, 30), '3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK));
                break;
                case 'module_cache':
                $module_handler =& xoops_gethandler('module');
                $modules =& $module_handler->getObjects(new Criteria('hasmain', 1), true);
                $currrent_val = $config[$i]->getConfValueForOutput();
                $cache_options = array('0' => _NOCACHE, '30' => sprintf(_SECONDS, 30), '60' => _MINUTE, '300' => sprintf(_MINUTES, 5), '1800' => sprintf(_MINUTES, 30), '3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK);
                if (count($modules) > 0) {
                    $ele = new XoopsFormElementTray($title, '<br />');
                    foreach (array_keys($modules) as $mid) {
                        $c_val = isset($currrent_val[$mid]) ? intval($currrent_val[$mid]) : null;
                        $selform = new XoopsFormSelect($modules[$mid]->getVar('name'), $config[$i]->getVar('conf_name')."[$mid]", $c_val);
                        $selform->addOptionArray($cache_options);
                        $ele->addElement($selform);
                        unset($selform);
                    }
                } else {
                    $ele = new XoopsFormLabel($title, _MD_AM_NOMODULE);
                }
                break;
            }
            $hidden = new XoopsFormHidden('conf_ids[]', $config[$i]->getVar('conf_id'));
            $form->addElement($ele);
            $form->addElement($hidden);
            unset($ele);
            unset($hidden);
        }
        $form->addElement(new XoopsFormHidden('op', 'save'));
        $form->addElement(new XoopsFormButton('', 'button', _GO, 'submit'));
        xoops_cp_header();
        if ($confcat->getVar('confcat_description') != "") {
            echo "<div style='float: right; width: 50%'>".constant($confcat->getVar('confcat_description'))."</div>";
        }
        echo '<div style="float: left; width: 45%;"><a href="admin.php?fct=preferences">'. _MD_AM_PREFMAIN .'</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'.constant($confcat->getVar('confcat_name')).'</div><br style="clear:both;"/><br />';

        $form->display();
        xoops_cp_footer();
        exit();
    }

    if ($op == 'save') {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header("admin.php?fct=preferences", 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $xoopsTpl->clear_all_cache();

        $count = count($_REQUEST['conf_ids']);
        $tpl_updated = false;
        $theme_updated = false;
        $startmod_updated = false;
        $lang_updated = false;
        if ($count > 0) {
            array_map("intval", $_REQUEST['conf_ids']);
            $configs =& $config_handler->getConfigs(new Criteria('conf_id', "(".implode(',', $_REQUEST['conf_ids']).")", "IN"));
            foreach (array_keys($configs) as $i) {
                $config =& $configs[$i];
                $new_value =& $_REQUEST[$config->getVar('conf_name')];
                if (is_array($new_value) || $new_value != $config->getVar('conf_value')) {
                    // if language has been changed
                    if (!$lang_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'language') {
                        // regenerate admin menu file
                        $xoopsConfig['language'] = ${$config->getVar('conf_name')};
                        $lang_updated = true;
                    }

                    // if default template set has been changed
                    if (!$tpl_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'template_set') {
                        // clear cached/compiled files and regenerate them if default theme has been changed
                        if ($xoopsConfig['template_set'] != ${$config->getVar('conf_name')}) {
                            $newtplset = ${$config->getVar('conf_name')};

                            // clear all compiled and cachedfiles
                            $xoopsTpl->clear_compiled_tpl();

                            // generate compiled files for the new theme
                            // block files only for now..
                            $tplfile_handler =& xoops_gethandler('tplfile');
                            $dtemplates =& $tplfile_handler->find('default', 'block');
                            $dcount = count($dtemplates);

                            // need to do this to pass to xoops_template_touch function
                            $GLOBALS['xoopsConfig']['template_set'] = $newtplset;

                            for ($i = 0; $i < $dcount; $i++) {
                                $found =& $tplfile_handler->find($newtplset, 'block', $dtemplates[$i]->getVar('tpl_refid'), null);
                                if (count($found) > 0) {
                                    // template for the new theme found, compile it
                                    xoops_template_touch($found[0]);
                                } else {
                                    // not found, so compile 'default' template file
                                    xoops_template_touch($dtemplates[$i]);
                                }
                            }

                            // generate image cache files from image binary data, save them under cache/
                            $image_handler =& xoops_gethandler('imagesetimg');
                            $imagefiles =& $image_handler->getObjects(new Criteria('tplset_name', $newtplset), true);
                            foreach (array_keys($imagefiles) as $i) {
                                if (!$fp = fopen(XOOPS_CACHE_PATH.'/'.$newtplset.'_'.$imagefiles[$i]->getVar('imgsetimg_file'), 'wb')) {
                                } else {
                                    fwrite($fp, $imagefiles[$i]->getVar('imgsetimg_body'));
                                    fclose($fp);
                                }
                            }
                        }
                        $tpl_updated = true;
                    }

                    // add read permission for the start module to all groups
                    if (!$startmod_updated  && $new_value != '--' && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'startpage') {
                        $member_handler =& xoops_gethandler('member');
                        $groups =& $member_handler->getGroupList();
                        $moduleperm_handler =& xoops_gethandler('groupperm');
                        $module_handler =& xoops_gethandler('module');
                        $module =& $module_handler->getByDirname($new_value);
                        if (is_object($module)) {
                            foreach ($groups as $groupid => $groupname) {
                                if (!$moduleperm_handler->checkRight('module_read', $module->getVar('mid'), $groupid)) {
                                    $moduleperm_handler->addRight('module_read', $module->getVar('mid'), $groupid);
                                }
                            }
                        }
                        $startmod_updated = true;
                    }

                    $config->setConfValueForInput($new_value);
                    $config_handler->insertConfig($config);
                }
                unset($new_value);
            }
        }
        //regenerate profile field information that is cleared
        $profile_handler =& xoops_gethandler('profile');
        $profile_handler->updateCache();

        if (!empty($_REQUEST['use_mysession']) && $xoopsConfig['use_mysession'] == 0 && $_REQUEST['session_name'] != '') {
            setcookie($_REQUEST['session_name'], session_id(), time()+(60*intval($_REQUEST['session_expire'])), '/',  '', 0);
        }
        if (isset($_REQUEST['redirect']) && $_REQUEST['redirect'] != '') {
            redirect_header($_REQUEST['redirect'], 2, _MD_AM_DBUPDATED);
        } else {
            redirect_header("admin.php?fct=preferences",2,_MD_AM_DBUPDATED);
        }
    }
}
?>