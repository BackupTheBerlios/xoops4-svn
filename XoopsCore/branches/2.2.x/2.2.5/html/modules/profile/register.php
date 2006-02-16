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
include '../../mainfile.php';
include_once 'include/functions.php';
$myts =& MyTextSanitizer::getInstance();


if (empty($xoopsModuleConfig['allow_register'])) {
    redirect_header('../../index.php', 6, _PROFILE_MA_NOREGISTER);
    exit();
}
include XOOPS_ROOT_PATH.'/header.php';

$member_handler =& xoops_gethandler('member');
$newuser =& $member_handler->createUser();
$op = !isset($_POST['op']) ? 'register' : $_POST['op'];
if ($op != "register") {
    $newuser->setVar('uname', isset($_POST['uname']) ? trim($_POST['uname']) : '');
    $newuser->setVar('loginname', isset($_POST['loginname']) ? trim($_POST['loginname']) : '');
    $newuser->setVar('email', isset($_POST['email']) ? trim($_POST['email']) : '');

    //Dynamic fields
    $profile_handler =& xoops_gethandler('profile');
    // Get fields
    $fields =& $profile_handler->loadFields();
    if (count($fields) > 0) {
        foreach (array_keys($fields) as $i) {
            $fieldname = $fields[$i]->getVar('field_name');
            if ($fields[$i]->getVar('field_register') && isset($_POST[$fieldname])) {
                $newuser->setVar($fieldname, $_POST[$fieldname]);
            }
        }
    }

    $vpass = isset($_POST['vpass']) ? $myts->stripSlashesGPC($_POST['vpass']) : '';
    $agree_disc = (isset($_POST['agree_disc']) && intval($_POST['agree_disc'])) ? 1 : 0;
}
switch ( $op ) {
    case 'newuser':
    if (!$GLOBALS['xoopsSecurity']->check()) {
        echo implode('<br />', $GLOBALS['xoopsSecurity']->getErrors());
        break;
    }
    $newuser->setVar('pass', isset($_POST['pass']) ? md5(trim($_POST['pass'])) : '');
    $stop = '';
    if ($xoopsModuleConfig['display_disclaimer'] != 0 && $xoopsModuleConfig['disclaimer'] != '') {
        if (empty($agree_disc)) {
            $stop .= _PROFILE_MA_UNEEDAGREE.'<br />';
        }
    }
    if ( strcmp(trim($_POST['pass']), trim($_POST['vpass'])) ) {
        $stop .= _PROFILE_MA_PASSNOTSAME."<br />";
    }elseif (!empty($xoopsModuleConfig['minpass']) && strlen(trim($_POST['pass'])) < $xoopsModuleConfig['minpass']) {
        $stop .= sprintf(_PROFILE_MA_PWDTOOSHORT,$xoopsModuleConfig['minpass'])."<br />";
    }
    $stop .= userCheck($newuser);
    if (empty($stop)) {
        echo _PROFILE_MA_USERNAME.": ".$newuser->getVar('loginname')."<br />";
        echo _PROFILE_MA_EMAIL.": ".$newuser->getVar('email')."<br />";
        echo _PROFILE_MA_DISPLAYNAME.": ".$newuser->getVar('uname')."<br />";
        $profile_handler =& xoops_gethandler('profile');
        // Get fields
        $fields =& $profile_handler->loadFields();
        if (count($fields) > 0) {
            foreach (array_keys($fields) as $i) {
                $fieldname = $fields[$i]->getVar('field_name');
                if ($fields[$i]->getVar('field_register') && isset($_POST[$fieldname])) {
                    $value = $newuser->getVar($fieldname);
                    if (is_array($value)) {
                        $values = array();
                        $options = $fields[$i]->getVar('field_options');
                        foreach ($value as $thisvalue) {
                            $values = $options[$thisvalue];
                        }
                        $value = implode(', ', $values);
                    }
                    echo $fields[$i]->getVar('field_title').": ".$value."<br />";
                }
            }
        }
        //hidden POST form with variables
        include_once 'include/forms.php';
        $finish_form =& getFinishForm($newuser, $vpass);
        $finish_form->display();
    } else {
        echo "<div class='errorMsg'>$stop</div><br clear='both'>";
        include_once 'include/forms.php';
        $reg_form =& getRegisterForm($newuser);
        $reg_form->display();
    }
    break;
    
    case 'finish':
    if (!$GLOBALS['xoopsSecurity']->check()) {
        echo implode('<br />', $GLOBALS['xoopsSecurity']->getErrors());
        break;
    }

    $stop = '';
    if ($xoopsModuleConfig['display_disclaimer'] != 0 && $xoopsModuleConfig['disclaimer'] != '') {
        if (empty($agree_disc)) {
            $stop .= _PROFILE_MA_UNEEDAGREE.'<br />';
        }
    }
    $stop = userCheck($newuser);
    if ( empty($stop) ) {
        $newuser->setVar('pass', $_POST['pass']);
        $newuser->setVar('user_avatar','blank.gif');
        $actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
        $newuser->setVar('actkey', $actkey);
        $newuser->setVar('user_regdate', time());
        if ($xoopsModuleConfig['activation_type'] == 1) {
            $newuser->setVar('level', 1);
        }

        $profile_handler =& xoops_gethandler('profile');
        // Get fields
        $fields = $profile_handler->loadFields();
        if (count($fields) > 0) {
            foreach (array_keys($fields) as $i) {
                $fieldname = $fields[$i]->getVar('field_name');
                if ($fields[$i]->getVar('field_register') && isset($_POST[$fieldname])) {
                    $newuser->setVar($fieldname, $_POST[$fieldname]);
                }
            }
        }
        if (!$member_handler->insertUser($newuser)) {
            echo _PROFILE_MA_REGISTERNG;
            echo implode('<br />', $newuser->getErrors());
            break;
        }
        $newid = $newuser->getVar('uid');
        if (!$member_handler->addUserToGroup(XOOPS_GROUP_USERS, $newid)) {
            echo _PROFILE_MA_REGISTERNG;
            break;
        }
        if ($xoopsModuleConfig['activation_type'] == 1) {
            redirect_header(XOOPS_URL.'/index.php', 4, _PROFILE_MA_ACTLOGIN);
        }
        if ($xoopsModuleConfig['activation_type'] == 0) {
            $xoopsMailer =& getMailer();
            $xoopsMailer->useMail();
            $xoopsMailer->setTemplate('register.tpl');
            $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH."/modules/profile/language/".$xoopsConfig['language']."/mail_template");
            $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
            $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
            $xoopsMailer->assign('SITEURL', XOOPS_URL."/");
            $xoopsMailer->setToUsers(new XoopsUser($newid));
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(sprintf(_PROFILE_MA_USERKEYFOR, $newuser->getVar('uname')));
            if ( !$xoopsMailer->send() ) {
                echo _PROFILE_MA_YOURREGMAILNG;
            } else {
                echo _PROFILE_MA_YOURREGISTERED;
            }
        } elseif ($xoopsModuleConfig['activation_type'] == 2) {
            $xoopsMailer =& getMailer();
            $xoopsMailer->useMail();
            $xoopsMailer->setTemplate('adminactivate.tpl');
            $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH."/modules/profile/language/".$xoopsConfig['language']."/mail_template");
            $xoopsMailer->assign('USERNAME', $newuser->getVar('uname'));
            $xoopsMailer->assign('USEREMAIL', $newuser->getVar('email'));
            $xoopsMailer->assign('USERACTLINK', XOOPS_URL.'/modules/profile/activate.php?op=actv&id='.$newid.'&actkey='.$actkey);
            $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
            $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
            $xoopsMailer->assign('SITEURL', XOOPS_URL."/");
            $member_handler =& xoops_gethandler('member');
            $xoopsMailer->setToGroups($member_handler->getGroup($xoopsModuleConfig['activation_group']));
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(sprintf(_PROFILE_MA_USERKEYFOR, $newuser->getVar('uname')));
            if ( !$xoopsMailer->send() ) {
                echo _PROFILE_MA_YOURREGMAILNG;
            } else {
                echo _PROFILE_MA_YOURREGISTERED2;
            }
        }
        if ($xoopsModuleConfig['new_user_notify'] == 1 && !empty($xoopsModuleConfig['new_user_notify_group'])) {
            $xoopsMailer =& getMailer();
            $xoopsMailer->useMail();
            $member_handler =& xoops_gethandler('member');
            $xoopsMailer->setToGroups($member_handler->getGroup($xoopsModuleConfig['new_user_notify_group']));
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(sprintf(_PROFILE_MA_NEWUSERREGAT,$xoopsConfig['sitename']));
            $xoopsMailer->setBody(sprintf(_PROFILE_MA_HASJUSTREG, $newuser->getVar('uname')));
            $xoopsMailer->send();
        }
    } else {
        echo "<div class='errorMsg'>$stop</div><br clear='both'>";
        include_once 'include/forms.php';
        $reg_form =& getRegisterForm($newuser);
        $reg_form->display();
    }
    break;
    
    case 'register':
    default:
    include_once 'include/forms.php';
    $reg_form =& getRegisterForm($newuser);
    $reg_form->display();

    break;
}
include XOOPS_ROOT_PATH.'/footer.php';
?>