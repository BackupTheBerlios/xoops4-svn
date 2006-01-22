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
}
include_once XOOPS_ROOT_PATH."/modules/system/admin/modulesadmin/modulesadmin.php";
$op = "list";
if ( isset($_POST) ) {
    foreach ( $_POST as $k => $v ) {
        ${$k} = $v;
    }
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
    $module = $_GET['module'];
}

if (in_array($op, array('submit', 'install_ok', 'update_ok', 'uninstall_ok'))) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $op = 'list';
    }
}

if ( $op == "list" ) {
    xoops_module_list();
    exit();
}

if ( $op == "confirm" ) {
    xoops_cp_header();
    //OpenTable();
    $error = array();
    if ( !is_writable(XOOPS_CACHE_PATH.'/') ) {
        // attempt to chmod 666
        if ( !chmod(XOOPS_CACHE_PATH.'/', 0777) ) {
            $error[] = sprintf(_MUSTWABLE, "<b>".XOOPS_CACHE_PATH.'/</b>');
        }
    }
    if ( count($error) > 0 ) {
        xoops_error($error);
        echo "<p><a href='admin.php?fct=modulesadmin'>"._MD_AM_BTOMADMIN."</a></p>";
        xoops_cp_footer();
        exit();
    }
    echo "<h4 style='text-align:left;'>"._MD_AM_PCMFM."</h4>
    <form action='admin.php' method='post'>
    <input type='hidden' name='fct' value='modulesadmin' />
    <input type='hidden' name='op' value='submit' />
    <table width='100%' border='0' cellspacing='1' class='outer'>
    <tr align='center'><th>"._MD_AM_MODULE."</th><th>"._MD_AM_ACTION."</th><th>"._MD_AM_ORDER."</th></tr>";
    $mcount = 0;
    $myts =& MyTextsanitizer::getInstance();
    foreach ($module as $mid) {
        if ($mcount % 2 != 0) {
            $class = 'odd';
        } else {
            $class = 'even';
        }
        echo '<tr class="'.$class.'"><td align="center">'.$myts->stripSlashesGPC($oldname[$mid]);
        $newname[$mid] = trim($myts->stripslashesGPC($newname[$mid]));
        if ($newname[$mid] != $oldname[$mid]) {
            echo '&nbsp;&raquo;&raquo;&nbsp;<span style="color:#ff0000;font-weight:bold;">'.$newname[$mid].'</span>';
        }
        echo '</td><td align="center">';
        if (isset($newstatus[$mid]) && $newstatus[$mid] ==1) {
            if ($oldstatus[$mid] == 0) {
                echo "<span style='color:#ff0000;font-weight:bold;'>"._MD_AM_ACTIVATE."</span>";
            } else {
                echo _MD_AM_NOCHANGE;
            }
        } else {
            $newstatus[$mid] = 0;
            if ($oldstatus[$mid] == 1) {
                echo "<span style='color:#ff0000;font-weight:bold;'>"._MD_AM_DEACTIVATE."</span>";
            } else {
                echo _MD_AM_NOCHANGE;
            }
        }
        echo "</td><td align='center'>";
        if ($oldweight[$mid] != $weight[$mid]) {
            echo "<span style='color:#ff0000;font-weight:bold;'>".$weight[$mid]."</span>";
        } else {
            echo $weight[$mid];
        }
        echo "
        <input type='hidden' name='module[]' value='".$mid."' />
        <input type='hidden' name='oldname[".$mid."]' value='".htmlspecialchars($oldname[$mid], ENT_QUOTES)."' />
        <input type='hidden' name='newname[".$mid."]' value='".htmlspecialchars($newname[$mid], ENT_QUOTES)."' />
        <input type='hidden' name='oldstatus[".$mid."]' value='".$oldstatus[$mid]."' />
        <input type='hidden' name='newstatus[".$mid."]' value='".$newstatus[$mid]."' />
        <input type='hidden' name='oldweight[".$mid."]' value='".intval($oldweight[$mid])."' />
        <input type='hidden' name='weight[".$mid."]' value='".intval($weight[$mid])."' />
        </td></tr>";
    }
    echo "
    <tr class='foot' align='center'><td colspan='3'><input type='submit' value='"._MD_AM_SUBMIT."' />&nbsp;<input type='button' value='"._MD_AM_CANCEL."' onclick='location=\"admin.php?fct=modulesadmin\"' />".$GLOBALS['xoopsSecurity']->getTokenHTML()."</td></tr>
    </table>
    </form>";
    xoops_cp_footer();
    exit();
}
if ( $op == "submit" ) {
    $ret = array();
    $write = false;
    foreach ($module as $mid) {
        if (isset($newstatus[$mid]) && $newstatus[$mid] ==1) {
            if ($oldstatus[$mid] == 0) {
                $ret[] = xoops_module_activate($mid);
            }
        } else {
            if ($oldstatus[$mid] == 1) {
                $ret[] = xoops_module_deactivate($mid);
            }
        }
        $newname[$mid] = trim($newname[$mid]);
        if ($oldname[$mid] != $newname[$mid] || $oldweight[$mid] != $weight[$mid]) {
            $ret[] = xoops_module_change($mid, $weight[$mid], $newname[$mid]);
            $write = true;
        }
        flush();
    }
    xoops_cp_header();
    if ( count($ret) > 0 ) {
        foreach ($ret as $msg) {
            if ($msg != '') {
                echo $msg;
            }
        }
    }
    echo "<br /><a href='admin.php?fct=modulesadmin'>"._MD_AM_BTOMADMIN."</a>";
    xoops_cp_footer();
    exit();
}

if ($op == 'install') {
    $module_handler =& xoops_gethandler('module');
    $mod =& $module_handler->create();
    $mod->loadInfoAsVar($module);
    if ($mod->getInfo('image') != false && trim($mod->getInfo('image')) != '') {
        $msgs ='<img src="'.XOOPS_URL.'/modules/'.$mod->getVar('dirname').'/'.trim($mod->getInfo('image')).'" alt="" />';
    }
    $msgs .= '<br /><span style="font-size:smaller;";>'.$mod->getVar('name').'</span><br /><br />'._MD_AM_RUSUREINS;
    xoops_cp_header();
    include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
    $form = new XoopsThemeForm('', 'form', 'admin.php', "post", true);
    $form->addElement(new XoopsFormHidden('module', $module));
    $form->addElement(new XoopsFormHidden('op', 'install_ok'));
    $form->addElement(new XoopsFormHidden('fct', 'modulesadmin'));
    $form->addElement(new XoopsFormLabel('', $msgs));
    if ($mod->getVar('hasadmin')) {
        $form->addElement(new XoopsFormSelectGroup(_MD_AM_SELECT_ADMINS, 'admingroups', false, null, 5, true));
    }
    if ($mod->getVar('hasmain')) {
        $form->addElement(new XoopsFormSelectGroup(_MD_AM_SELECT_ACCESS, 'accessgroups', true, null, 5, true));
    }
    $form->addElement(new XoopsFormButton('', 'submit', _MD_AM_INSTALL, 'submit'));
    $form->display();
    xoops_cp_footer();
    exit();
}

if ($op == 'install_ok') {
    $dirname = $module;
    $module_handler =& xoops_gethandler('module');
    if ($module_handler->getCount(new Criteria('dirname', trim($dirname))) == 0) {
        $module =& $module_handler->create();
        $module->setVar('dirname', trim($dirname));
        xoops_cp_header();
        $html = '<h4 style="text-align:left;padding: 5px 0px; margin-bottom: 5px;border-bottom: dashed 1px #000000;">Installing '.$module->getInfo('name').'</h4>';

        if ($module->getInfo('image') != false && trim($module->getInfo('image')) != '') {
            $html .='<img src="'.XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.trim($module->getInfo('image')).'" alt="" />';
        }
        $html .='<br /><b>Version:</b> '.$module->getInfo('version');
        if ($module->getInfo('author') != false && trim($module->getInfo('author')) != '') {
            $html .='<br /><b>Author:</b> '.trim($module->getInfo('author'));
        }
        $html .= '<hr style="border-top: 0px; border-bottom: dashed 1px #000000;" />';
        if (!isset($_REQUEST['admingroups'])) {
            $_REQUEST['admingroups'] = array();
        }
        if (!isset($_REQUEST['accessgroups'])) {
            $_REQUEST['accessgroups'] = array();
        }
        $html .= $module->install($_REQUEST['admingroups'], $_REQUEST['accessgroups']);
        echo $html;
    }
    else {
        echo "<p>".sprintf(_MD_AM_FAILINS, "<b>".$dirname."</b>")."&nbsp;"._MD_AM_ERRORSC."<br />&nbsp;&nbsp;".sprintf(_MD_AM_ALEXISTS, $dirname)."</p>";
    }
    echo "<br /><a href='admin.php?fct=modulesadmin'>"._MD_AM_BTOMADMIN."</a>";
    xoops_cp_footer();
    exit();
}

if ($op == 'uninstall') {
    $module_handler =& xoops_gethandler('module');
    $mod =& $module_handler->getByDirname($module);
    if ($mod->getInfo('image') != false && trim($mod->getInfo('image')) != '') {
        $msgs ='<img src="'.XOOPS_URL.'/modules/'.$mod->getVar('dirname').'/'.trim($mod->getInfo('image')).'" alt="" />';
    }
    $msgs .= '<br /><span style="font-size:smaller;";>'.$mod->getVar('name').'</span><br /><br />'._MD_AM_RUSUREUNINS;
    xoops_cp_header();
    xoops_confirm(array('module' => $module, 'op' => 'uninstall_ok', 'fct' => 'modulesadmin'), 'admin.php', $msgs, _YES);
    xoops_cp_footer();
    exit();
}

if ($op == 'uninstall_ok') {
    $ret = array();
    $ret[] = xoops_module_uninstall($module);
    xoops_cp_header();
    if (count($ret) > 0) {
        foreach ($ret as $msg) {
            if ($msg != '') {
                echo $msg;
            }
        }
    }
    echo "<a href='admin.php?fct=modulesadmin'>"._MD_AM_BTOMADMIN."</a>";
    xoops_cp_footer();
    exit();
}

if ($op == 'update') {
    $module_handler =& xoops_gethandler('module');
    $mod =& $module_handler->getByDirname($module);
    if ($mod->getInfo('image') != false && trim($mod->getInfo('image')) != '') {
        $msgs ='<img src="'.XOOPS_URL.'/modules/'.$mod->getVar('dirname').'/'.trim($mod->getInfo('image')).'" alt="" />';
    }
    $msgs .= '<br /><span style="font-size:smaller;";>'.$mod->getVar('name').'</span><br /><br />'._MD_AM_RUSUREUPD;
    xoops_cp_header();
    xoops_confirm(array('dirname' => $module, 'op' => 'update_ok', 'fct' => 'modulesadmin'), 'admin.php', $msgs, _MD_AM_UPDATE);
    xoops_cp_footer();
    exit();
}

if ($op == 'update_ok') {
    $dirname = trim($dirname);
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname($dirname);
    xoops_cp_header();
    echo $module->update();
    echo "<br /><a href='admin.php?fct=modulesadmin'>"._MD_AM_BTOMADMIN."</a>";
    xoops_cp_footer();
}

?>
