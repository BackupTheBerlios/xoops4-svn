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
// Author: XOOPS Foundation                                                  //
// URL: http://www.xoops.org/                                                //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
include '../../../include/cp_header.php';
xoops_cp_header();
$xTheme->loadModuleAdminMenu(4, _PROFILE_MI_USERS);
//$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : (isset($_REQUEST['id']) ? "edit" : 'list');
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'list';
if($op == "editordelete") {
	$op = isset($_REQUEST['delete'])?"delete":"edit";
}
$handler =& xoops_gethandler('member');
/* @var $handler XoopsMemberHandler */

switch($op) {
    default:
    case "list":
    include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
    $form = new XoopsThemeForm(_PROFILE_AM_EDITUSER, 'form', 'user.php');
    $form->addElement(new XoopsFormSelectUser(_PROFILE_AM_SELECTUSER, 'id'));
    $form->addElement(new XoopsFormHidden('op', 'editordelete'));
	$button_tray = new XoopsFormElementTray('');
	$button_tray->addElement(new XoopsFormButton('', 'edit', _EDIT, 'submit'));
	$button_tray->addElement(new XoopsFormButton('', 'delete', _DELETE, 'submit'));
	$form->addElement($button_tray);
    //$form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->display();

    case "new":
    $xoopsModule->loadLanguage();
    include_once('../include/forms.php');
    $obj =& $handler->createUser();
    $obj->setGroups(array(XOOPS_GROUP_USERS));
    $form =& getUserForm($obj);
    $form->display();
    break;

    case "edit":
    $xoopsModule->loadLanguage();
    $obj =& $handler->getUser($_REQUEST['id']);
    include_once('../include/forms.php');
    $form =& getUserForm($obj);
    $form->display();
    break;

    case "save":
    $xoopsModule->loadLanguage();
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('admin.php',3,_PROFILE_MA_NOEDITRIGHT."<br />".implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }
    $uid = 0;
    if (!empty($_POST['uid'])) {
        $uid = intval($_POST['uid']);
        $user =& $handler->getUser($uid);
    }
    else {
        $user =& $handler->createUser();
        $user->setVar('user_regdate', time());
        $user->setVar('level', 1);
    }
    $errors = array();
    $myts =& MyTextSanitizer::getInstance();
    if ($user->isNew() || $xoopsModuleConfig['allow_chgmail'] == 1) {
        $email = '';
        if (!empty($_POST['email'])) {
            $user->setVar('email', trim($_POST['email']));
        }
    }
    if ($user->getVar('uid') != $xoopsUser->getVar('uid')) {
        $password = '';
        if (!empty($_POST['password'])) {
            $password = $myts->stripSlashesGPC(trim($_POST['password']));
        }
        if ($password != '') {
            if (strlen($password) < $xoopsModuleConfig['minpass']) {
                $errors[] = sprintf(_PROFILE_MA_PWDTOOSHORT,$xoopsModuleConfig['minpass']);
            }
            $vpass = '';
            if (!empty($_POST['vpass'])) {
                $vpass = $myts->stripSlashesGPC(trim($_POST['vpass']));
            }
            if ($password != $vpass) {
                $errors[] = _PROFILE_MA_PASSNOTSAME;
            }
            $user->setVar('pass', md5($password));
        }
        elseif ($user->isNew()) {
            $errors[] = _PROFILE_MA_NOPASSWORD;
        }
        if ($xoopsUser->isAdmin()) {
            //admins can set level (activated/deactivated) for users
            $user->setVar('level', intval($_POST['level']));
        }
    }
    $user->setVar('uname', $_POST['uname']);
    $user->setVar('loginname', $_POST['loginname']);
    $user->setVar('rank', intval($_POST['rank']));
    $user->setVar('name', $_POST['name']);
    include_once('../include/functions.php');
    $stop = userCheck($user);
    if ($stop != "") {
        $errors[] = $stop;
    }


        // Dynamic fields
        $profile_handler =& xoops_gethandler('profile');
        // Get fields
        $fields =& $profile_handler->loadFields();
        // Get ids of fields that can be edited
        $gperm_handler =& xoops_gethandler('groupperm');
        $editable_fields =& $gperm_handler->getItemIds('profile_edit', $xoopsUser->getGroups(), $xoopsModule->getVar('mid'));

        foreach (array_keys($fields) as $i) {
            $fieldname = $fields[$i]->getVar('field_name');
            if (in_array($fields[$i]->getVar('fieldid'), $editable_fields) && isset($_REQUEST[$fieldname])) {
                $user->setVar($fieldname, $_REQUEST[$fieldname]);
            }
        }

    $new_groups = isset($_POST['groups']) ? $_POST['groups'] : array();
    //$user->setGroups($new_groups);

    if (count($errors) == 0) {
        if ($handler->insertUser($user)) {
            if ($gperm_handler->checkRight("system_admin", XOOPS_SYSTEM_GROUP, $xoopsUser->getGroups(), 1)) {
                //Update group memberships
                $cur_groups = $user->getGroups();

                $added_groups = array_diff($new_groups, $cur_groups);
                $removed_groups = array_diff($cur_groups, $new_groups);

                if (count($added_groups) > 0) {
                    foreach ($added_groups as $groupid) {
                        $handler->addUserToGroup($groupid, $user->getVar('uid'));
                    }
                }
                if (count($removed_groups) > 0) {
                    foreach ($removed_groups as $groupid) {
                        $handler->removeUsersFromGroup($groupid, array($user->getVar('uid')));
                    }
                }
            }
            if ($user->isNew()) {
                redirect_header('user.php', 2, _PROFILE_AM_USERCREATED, false);
            }
            else {
                redirect_header('user.php', 2, _PROFILE_MA_PROFUPDATED, false);
            }
        }
    }
    else {
        foreach ($errors as $err) {
            $user->setErrors($err);
        }
    }
    include_once('../include/forms.php');
    echo $user->getHtmlErrors();
    $form =& getUserForm($user);
    $form->display();
    break;

    case "delete":
    if ($_REQUEST['id'] == $xoopsUser->getVar('uid')) {
        redirect_header('user.php', 2, _PROFILE_AM_CANNOTDELETESELF);
    }
    $obj =& $handler->getUser($_REQUEST['id']);
    if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('user.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()), false);
        }
        if ($handler->deleteUser($obj)) {
            redirect_header('user.php', 3, sprintf(_PROFILE_AM_DELETEDSUCCESS, $obj->getVar('uname')." (".$obj->getVar('loginname').")"), false);
        }
        else {
            echo $obj->getHtmlErrors();
        }
    }
    else {
        xoops_confirm(array('ok' => 1, 'id' => $_REQUEST['id'], 'op' => 'delete'), $_SERVER['REQUEST_URI'], sprintf(_PROFILE_AM_RUSUREDEL, $obj->getVar('uname')." (".$obj->getVar('loginname').")"));
    }
    break;
}

xoops_cp_footer();
?>