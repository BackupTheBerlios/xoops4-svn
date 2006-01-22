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

function xoops_module_list()
{
        xoops_cp_header();
    //OpenTable();
    echo "
    <h4 style='text-align:left'>"._MD_AM_MODADMIN."</h4>
    <form action='admin.php' method='post' name='moduleadmin' id='moduleadmin'>
    <table class='outer' width='100%' cellpadding='4' cellspacing='1'>
    <tr align='center'><th>"._MD_AM_MODULE."</th><th>"._MD_AM_VERSION."</th><th>"._MD_AM_LASTUP."</th><th>"._MD_AM_ACTIVE."</th><th>"._MD_AM_ORDER."<br /><small>"._MD_AM_ORDER0."</small></th><th>"._MD_AM_ACTION."</th></tr>
    ";
    $module_handler =& xoops_gethandler('module');
    $installed_mods =& $module_handler->getObjects();
    $listed_mods = array();
    $count = 0;
    foreach ( $installed_mods as $module ) {
        if ($count % 2 == 0) {
            $class = 'even';
        } else {
            $class = 'odd';
        }
        $count++;
        echo "<tr class='$class' align='center' valign='middle'>\n";
        echo "<td valign='bottom'>";
        if ( $module->getVar('hasadmin') == 1 && $module->getVar('isactive') == 1) {
            echo '<a href="'.XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.$module->getInfo('adminindex').'"><img src="'.XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.$module->getInfo('image').'" alt="'.$module->getVar('name', 'E').'" border="0" /></a><br /><input type="text" name="newname['.$module->getVar('mid').']" value="'.$module->getVar('name', 'E').'" maxlength="150" size="20" />';
        } else {
            echo '<img src="'.XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.$module->getInfo('image').'" alt="'.$module->getVar('name', 'E').'" border="0" /><br /><input type="text" name="newname['.$module->getVar('mid').']" value="'.$module->getVar('name', 'E').'" maxlength="150" size="20" />';
        }
        echo '<input type="hidden" name="oldname['.$module->getVar('mid').']" value="' .$module->getVar('name').'" /></td>';
        echo "<td align='center'>".round($module->getVar('version') / 100, 2)."</td><td align='center'>".formatTimestamp($module->getVar('last_update'),'m')."<br />";
        if ($module->getVar('dirname') != 'system' && $module->getVar('isactive') == 1) {
            echo '</td><td><input type="checkbox" name="newstatus['.$module->getVar('mid').']" value="1" checked="checked" /><input type="hidden" name="oldstatus['.$module->getVar('mid').']" value="1" />';
            $extra = '<a href="'.XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module='.$module->getVar('dirname').'"><img src="'.XOOPS_URL.'/modules/system/images/update.gif" alt="'._MD_AM_UPDATE.'" /></a>';
        } elseif ($module->getVar('dirname') != 'system') {
            echo '</td><td><input type="checkbox" name="newstatus['.$module->getVar('mid').']" value="1" /><input type="hidden" name="oldstatus['.$module->getVar('mid').']" value="0" />';
            $extra = '<a href="'.XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module='.$module->getVar('dirname').'"><img src="'.XOOPS_URL.'/modules/system/images/update.gif" alt="'._MD_AM_UPDATE.'" /></a>&nbsp;<a href="'.XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&amp;op=uninstall&amp;module='.$module->getVar('dirname').'"><img src="'.XOOPS_URL.'/modules/system/images/uninstall.gif" alt="'._MD_AM_UNINSTALL.'" /></a>';
        } else {
            echo '</td><td><input type="checkbox" name="newstatus['.$module->getVar('mid').']" value="1" checked="checked" /><input type="hidden" name="oldstatus['.$module->getVar('mid').']" value="1" />';
            $extra = '<a href="'.XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module='.$module->getVar('dirname').'"><img src="'.XOOPS_URL.'/modules/system/images/update.gif" alt="'._MD_AM_UPDATE.'" /></a>';
        }
        echo "</td><td>";
        if ($module->getVar('hasmain') == 1) {
            echo '<input type="hidden" name="oldweight['.$module->getVar('mid').']" value="'.$module->getVar('weight').'" /><input type="text" name="weight['.$module->getVar('mid').']" size="3" maxlength="5" value="'.$module->getVar('weight').'" />';
        } else {
            echo '<input type="hidden" name="oldweight['.$module->getVar('mid').']" value="0" /><input type="hidden" name="weight['.$module->getVar('mid').']" value="0" />';
        }
        echo "
        </td>
        <td>".$extra."&nbsp;<a href='javascript:openWithSelfMain(\"".XOOPS_URL."/modules/system/admin.php?fct=version&amp;mid=".$module->getVar('mid')."\",\"Info\",300,230);'>";
        echo '<img src="'.XOOPS_URL.'/modules/system/images/info.gif" alt="'._INFO.'" /></a><input type="hidden" name="module[]" value="'.$module->getVar('mid').'" /></td>
        </tr>
        ';
        $listed_mods[] = $module->getVar('dirname');
    }
    echo "<tr class='foot'><td colspan='6' align='center'><input type='hidden' name='fct' value='modulesadmin' />
    <input type='hidden' name='op' value='confirm' />
    <input type='submit' name='submit' value='"._MD_AM_SUBMIT."' />
    </td></tr></table>
    </form>
    <br />
    <table width='100%' border='0' class='outer' cellpadding='4' cellspacing='1'>
    <tr align='center'><th>"._MD_AM_MODULE."</th><th>"._MD_AM_VERSION."</th><th>"._MD_AM_ACTION."</th></tr>
    ";
    $modules_dir = XOOPS_ROOT_PATH."/modules";
    $handle = opendir($modules_dir);
    $count = 0;
    while ($file = readdir($handle)) {
        clearstatcache();
        $file = trim($file);
        if ($file != '' && strtolower($file) != 'cvs' && !preg_match("/^[.]{1,2}$/",$file) && is_dir($modules_dir.'/'.$file)) {
            if ( !in_array($file, $listed_mods) ) {
                $module =& $module_handler->create();
                $module->loadInfo($file);
                if ($count % 2 == 0) {
                    $class = 'even';
                } else {
                    $class = 'odd';
                }
                echo '<tr class="'.$class.'" align="center" valign="middle">
                <td align="center" valign="bottom"><img src="'.XOOPS_URL.'/modules/'.$module->getInfo('dirname').'/'.$module->getInfo('image').'" alt="'.htmlspecialchars($module->getInfo('name')).'" border="0" /></td>
                <td align="center">'.round($module->getInfo('version'), 2).'</td>
                <td>
                <a href="'.XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&amp;op=install&amp;module='.$module->getInfo('dirname').'"><img src="'.XOOPS_URL.'/modules/system/images/install.gif" alt="'._MD_AM_INSTALL.'" /></a>';
                echo "&nbsp;<a href='javascript:openWithSelfMain(\"".XOOPS_URL."/modules/system/admin.php?fct=version&amp;mid=".$module->getInfo('dirname')."\",\"Info\",300,230);'>";
                echo '<img src="'.XOOPS_URL.'/modules/system/images/info.gif" alt="'._INFO.'" /></a></td></tr>
                ';
                unset($module);
                $count++;
            }
        }
    }
    echo "</table>";
    //CloseTable();
    xoops_cp_footer();
}

function xoops_module_uninstall($dirname)
{
    global $xoopsConfig;
    $reservedTables = array('avatar', 'avatar_users_link', 'block_module_link', 'xoopscomments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups','groups_users_link','group_permission', 'online', 'bannerclient', 'banner', 'bannerfinish', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'modules', 'tplfile', 'tplset', 'tplsource', 'xoopsnotifications', 'banner', 'bannerclient', 'bannerfinish');
    $db =& Database::getInstance();
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname($dirname);
    include_once XOOPS_ROOT_PATH.'/class/template.php';
    xoops_template_clear_module_cache($module->getVar('mid'));
    if ($module->getVar('dirname') == 'system') {
        return "<p>".sprintf(_MD_AM_FAILUNINS, "<b>".$module->getVar('name')."</b>")."&nbsp;"._MD_AM_ERRORSC."<br /> - "._MD_AM_SYSNO."</p>";
    } elseif ($module->getVar('dirname') == $xoopsConfig['startpage']) {
        return "<p>".sprintf(_MD_AM_FAILUNINS, "<b>".$module->getVar('name')."</b>")."&nbsp;"._MD_AM_ERRORSC."<br /> - "._MD_AM_STRTNO."</p>";
    } else {
        $msgs = array();
        if (!$module_handler->delete($module)) {
            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete '.$module->getVar('name').'</span>';
        } else {

            // delete template files
            $tplfile_handler = xoops_gethandler('tplfile');
            $templates =& $tplfile_handler->find(null, 'module', $module->getVar('mid'));
            $tcount = count($templates);
            if ($tcount > 0) {
                $msgs[] = 'Deleting templates...';
                for ($i = 0; $i < $tcount; $i++) {
                    if (!$tplfile_handler->delete($templates[$i])) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete template '.$templates[$i]->getVar('tpl_file').' from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id').'</b></span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;Template <b>'.$templates[$i]->getVar('tpl_file').'</b> deleted from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id').'</b>';
                    }
                }
            }
            unset($templates);

            // delete blocks and block template files
            $block_handler =& xoops_gethandler('block');
            $block_arr =& $block_handler->getByModule($module->getVar('mid'));
            if (is_array($block_arr)) {
                $bcount = count($block_arr);
                $msgs[] = 'Deleting block...';
                for ($i = 0; $i < $bcount; $i++) {
                    if (!$block_handler->delete($block_arr[$i])) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete block <b>'.$block_arr[$i]->getVar('name').'</b> Block ID: <b>'.$block_arr[$i]->getVar('bid').'</b><br />'.implode('<br />', $block_arr[$i]->getErrors()).'</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;Block <b>'.$block_arr[$i]->getVar('name').'</b> deleted. Block ID: <b>'.$block_arr[$i]->getVar('bid').'</b>';
                    }
                }
                // delete any left-over block templates
                $templates =& $tplfile_handler->find(null, 'block', $module->getVar('mid'));
                $tcount = count($templates);
                if ($tcount > 0) {
                    $msgs[] = 'Deleting templates...';
                    for ($i = 0; $i < $tcount; $i++) {
                        if (!$tplfile_handler->delete($templates[$i])) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete template '.$templates[$i]->getVar('tpl_file').' from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id').'</b></span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Template <b>'.$templates[$i]->getVar('tpl_file').'</b> deleted from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id').'</b>';
                        }
                    }
                }
                unset($templates);
            }

            // delete tables used by this module
            $modtables = $module->getInfo('tables');
            if ($modtables != false && is_array($modtables)) {
                $msgs[] = 'Deleting module tables...';
                foreach ($modtables as $table) {
                    // prevent deletion of reserved core tables!
                    if (!in_array($table, $reservedTables)) {
                        $sql = 'DROP TABLE '.$db->prefix($table);
                        if (!$db->query($sql)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not drop table <b>'.$db->prefix($table).'<b>.</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Table <b>'.$db->prefix($table).'</b> dropped.</span>';
                        }
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Not allowed to drop table <b>'.$db->prefix($table).'</b>!</span>';
                    }
                }
            }

            // delete comments if any
            if ($module->getVar('hascomments') != 0) {
                $msgs[] = 'Deleting comments...';
                $comment_handler =& xoops_gethandler('comment');
                if (!$comment_handler->deleteByModule($module->getVar('mid'))) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete comments</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;Comments deleted';
                }
            }

            // RMV-NOTIFY
            // delete notifications if any
            if ($module->getVar('hasnotification') != 0) {
                $msgs[] = 'Deleting notifications...';
                if (!xoops_notification_deletebymodule($module->getVar('mid'))) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete notifications</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;Notifications deleted';
                }
            }

            // delete permissions if any
            $gperm_handler =& xoops_gethandler('groupperm');
            if (!$gperm_handler->deleteByModule($module->getVar('mid'))) {
                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete group permissions</span>';
            } else {
                $msgs[] = '&nbsp;&nbsp;Group permissions deleted';
            }

            // delete module config options if any
            if ($module->getVar('hasconfig') != 0 || $module->getVar('hascomments') != 0) {
                $config_handler =& xoops_gethandler('config');
                $configs =& $config_handler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
                $confcount = count($configs);
                if ($confcount > 0) {
                    $msgs[] = 'Deleting module config options...';
                    for ($i = 0; $i < $confcount; $i++) {
                        if (!$config_handler->deleteConfig($configs[$i])) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete config data from the database. Config ID: <b>'.$configs[$i]->getvar('conf_id').'</b></span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Config data deleted from the database. Config ID: <b>'.$configs[$i]->getVar('conf_id').'</b>';
                        }
                    }
                }
                // delete module config categories if any
                $configcat_handler =& xoops_gethandler('configcategory');
                $configcats =& $configcat_handler->getCatByModule($module->getVar('mid'));
                if (count($configcats) > 0) {
                    $msgs[] = 'Deleting module config categories...';
                    foreach (array_keys($configcats) as $i) {
                        if (!$configcat_handler->delete($configcats[$i])) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete config category from the database. Config Category: <b>'.$configcats[$i]->getvar('confcat_nameid').'</b></span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Config category data deleted from the database. Config Category: <b>'.$configcats[$i]->getVar('confcat_nameid').'</b>';
                        }
                    }
                }
            }
            
            // delete module user profiles if any
            if ($module->getInfo('hasProfile') != 0) {
                $profile_handler =& xoops_gethandler('profile');
                $fields =& $profile_handler->getFields(new Criteria('field_moduleid', $module->getVar('mid')));
                if (count($fields) > 0) {
                    $msgs[] = 'Deleting module profile fields...';
                    foreach (array_keys($fields) as $i) {
                        if (!$profile_handler->deleteField($fields[$i])) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete field data from the database. Field : <b>'.$fields[$i]->getVar('field_name').'</b></span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Field data deleted from the database. Field : <b>'.$fields[$i]->getVar('field_name').'</b>';
                        }
                    }
                }
            }

            // execute module specific uninstall script if any
            $uninstall_script = $module->getInfo('onUninstall');
            if (false != $uninstall_script && trim($uninstall_script) != '') {
                include_once XOOPS_ROOT_PATH.'/modules/'.$dirname.'/'.trim($uninstall_script);
                if (function_exists('xoops_module_uninstall_'.$dirname)) {
                    $func = 'xoops_module_uninstall_'.$dirname;
                    if (!$func($module)) {
                        $msgs[] = 'Failed to execute <b>'.$func.'</b>';
                    } else {
                        $msgs[] = '<b>'.$func.'</b> executed successfully.';
                    }
                }
            }

            $msgs[] = '</code><p>'.sprintf(_MD_AM_OKUNINS, "<b>".$module->getVar('name')."</b>").'</p>';
        }
        $ret = '<code>';
        foreach ($msgs as $msg) {
            $ret .= $msg.'<br />';
        }
        return $ret;
    }
}

function xoops_module_activate($mid)
{
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->get($mid);
    include_once XOOPS_ROOT_PATH.'/class/template.php';
    xoops_template_clear_module_cache($module->getVar('mid'));
    $module->setVar('isactive', 1);
    if (!$module_handler->insert($module)) {
        $ret = "<p>".sprintf(_MD_AM_FAILACT, "<b>".$module->getVar('name')."</b>")."&nbsp;"._MD_AM_ERRORSC."<br />".$module->getHtmlErrors();
        return $ret."</p>";
    }
    $block_handler =& xoops_gethandler('block');
    $block_handler->updateAll('isactive', 1, new Criteria('mid', $mid));
    return "<p>".sprintf(_MD_AM_OKACT, "<b>".$module->getVar('name')."</b>")."</p>";
}

function xoops_module_deactivate($mid)
{
    global $xoopsConfig;
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->get($mid);
    include_once XOOPS_ROOT_PATH.'/class/template.php';
    xoops_template_clear_module_cache($mid);
    $module->setVar('isactive', 0);
    if ($module->getVar('dirname') == "system") {
        return "<p>".sprintf(_MD_AM_FAILDEACT, "<b>".$module->getVar('name')."</b>")."&nbsp;"._MD_AM_ERRORSC."<br /> - "._MD_AM_SYSNO."</p>";
    } elseif ($module->getVar('dirname') == $xoopsConfig['startpage']) {
        return "<p>".sprintf(_MD_AM_FAILDEACT, "<b>".$module->getVar('name')."</b>")."&nbsp;"._MD_AM_ERRORSC."<br /> - "._MD_AM_STRTNO."</p>";
    } else {
        if (!$module_handler->insert($module)) {
            $ret = "<p>".sprintf(_MD_AM_FAILDEACT, "<b>".$module->getVar('name')."</b>")."&nbsp;"._MD_AM_ERRORSC."<br />".$module->getHtmlErrors();
            return $ret."</p>";
        }
        $block_handler =& xoops_gethandler('block');
        $block_handler->updateAll('isactive', 1, new Criteria('mid', $mid));
        return "<p>".sprintf(_MD_AM_OKDEACT, "<b>".$module->getVar('name')."</b>")."</p>";
    }
}

function xoops_module_change($mid, $weight, $name)
{
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->get($mid);
    $module->setVar('weight', $weight);
    $module->setVar('name', $name);
    $myts =& MyTextSanitizer::getInstance();
    if (!$module_handler->insert($module)) {
        $ret = "<p>".sprintf(_MD_AM_FAILORDER, "<b>".$myts->stripSlashesGPC($name)."</b>")."&nbsp;"._MD_AM_ERRORSC."<br />";
        $ret .= $module->getHtmlErrors()."</p>";
        return $ret;
    }
    return "<p>".sprintf(_MD_AM_OKORDER, "<b>".$myts->stripSlashesGPC($name)."</b>")."</p>";
}

?>