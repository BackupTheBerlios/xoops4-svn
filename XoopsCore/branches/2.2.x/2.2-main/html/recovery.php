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
include 'mainfile.php';
include_once XOOPS_ROOT_PATH."/modules/system/language/".$xoopsConfig['language']."/admin/preferences.php";

$confignames = array_map(array($GLOBALS['xoopsDB'], 'quoteString'), array('debug_mode', 'theme_set', 'theme_set_admin', 'enable_badips', 'template_set', 'session_expire'));
$criteria = new CriteriaCompo(new Criteria('conf_name', "(".implode(',', $confignames).")", "IN"));
$config_handler =& xoops_gethandler('config');
/* @var $config_handler XoopsConfigHandler */

$config =& $config_handler->getConfigs($criteria);
/* @var $config XoopsConfigItem */

if (!is_object($xoopsUser) || !$xoopsUser->isAdmin(1)) {
    if (isset($_POST['login'])) {
        //do login
        include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/user.php';
        $uname = !isset($_POST['uname']) ? '' : trim($_POST['uname']);
        $pass = !isset($_POST['pass']) ? '' : trim($_POST['pass']);
        if ($uname == '' || $pass == '') {
            redirect_header(XOOPS_URL.'/user.php', 1, _US_INCORRECTLOGIN);
            exit();
        }
        $member_handler =& xoops_gethandler('member');
        $myts =& MyTextsanitizer::getInstance();
        $user =& $member_handler->loginUser(addslashes($myts->stripSlashesGPC($uname)), addslashes($myts->stripSlashesGPC($pass)));
        if (false != $user) {
            if (0 == $user->getVar('level') || (!in_array(XOOPS_GROUP_ADMIN, $user->getGroups()))) {
                redirect_header(XOOPS_URL.'/index.php', 5, _US_NOACTTPADM);
                exit();
            }
            $_SESSION = array();
            $_SESSION['xoopsUserId'] = $user->getVar('uid');
            $_SESSION['xoopsUserGroups'] = $user->getGroups();
            if ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
                setcookie($xoopsConfig['session_name'], session_id(), time()+(60 * $xoopsConfig['session_expire']), '/',  '', 0);
            }
        }
    }
    else {
        include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
        $form = new XoopsThemeForm('Login', 'login_form', 'recovery.php');
        $form->addElement(new XoopsFormText(_USERNAME, 'uname', 12, 255));
        $form->addElement(new XoopsFormPassword(_PASSWORD, 'pass', 12, 255));
        $form->addElement(new XoopsFormButton('', 'login', _SUBMIT, 'submit'));
        $form->display();
        exit();
    }

}
elseif (isset($_POST['submit'])) {
    foreach (array_keys($config) as $i) {
        $new_value = $_POST[$config[$i]->getVar('conf_name')];
        $config[$i]->setConfValueForInput($new_value);
        $config_handler->insertConfig($config[$i]);
        unset($new_value);
    }
    //    echo $xoopsLogger->dumpAll();
    echo "<div>Settings Saved</div>";
}

include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
$form = new XoopsThemeForm('Recovery Page', 'pref_form', 'recovery.php');
foreach (array_keys($config) as $i) {
    $title = (!defined($config[$i]->getVar('conf_desc')) || constant($config[$i]->getVar('conf_desc')) == '') ? constant($config[$i]->getVar('conf_title')) : constant($config[$i]->getVar('conf_title')).'<br /><br /><span style="font-weight:normal;">'.constant($config[$i]->getVar('conf_desc')).'</span>';
    switch ($config[$i]->getVar('conf_formtype')) {
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
        $form->addElement(new XoopsFormHidden('_old_theme', $config[$i]->getConfValueForOutput()));
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
        $form->addElement(new XoopsFormHidden('_old_admintheme', $config[$i]->getConfValueForOutput()));
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

        case 'yesno':
        $ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
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
    }
    $hidden = new XoopsFormHidden('conf_ids[]', $config[$i]->getVar('conf_id'));
    $form->addElement($ele);
    $form->addElement($hidden);
    unset($ele);
    unset($hidden);
}
$form->addElement(new XoopsFormHidden('op', 'save'));
$form->addElement(new XoopsFormButton('', 'submit', _GO, 'submit'));
$form->display();

?>