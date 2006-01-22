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

$modversion['name'] = _PROFILE_MI_NAME;
$modversion['version'] = 0.1;
$modversion['description'] = _PROFILE_MI_DESC;
$modversion['author'] = "Jan Pedersen";
$modversion['credits'] = "The XOOPS Project, Ackbarr";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 1;
$modversion['image'] = "images/profile_logo.jpg";
$modversion['dirname'] = "profile";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/admin.php";
$modversion['adminmenu'] = "admin/menu.php";

// Menu
$modversion['hasMain'] = 1;
global $xoopsUser;
if ($xoopsUser) {
    $modversion['sub'][1]['name'] = _PROFILE_MI_EDITACCOUNT;
    $modversion['sub'][1]['url'] = "edituser.php";
    $modversion['sub'][2]['name'] = _PROFILE_MI_PAGE_SEARCH;
    $modversion['sub'][2]['url'] = "search.php";
    $modversion['sub'][3]['name'] = _PROFILE_MI_CHANGEPASS;
    $modversion['sub'][3]['url'] = "changepass.php";
    if (isset($GLOBALS['xoopsModuleConfig']) && isset($GLOBALS['xoopsModuleConfig']['allow_chgmail']) && $GLOBALS['xoopsModuleConfig']['allow_chgmail'] == 1) {
        $modversion['sub'][4]['name'] = _PROFILE_MI_CHANGEMAIL;
        $modversion['sub'][4]['url'] = "changemail.php";
    }
}

// Page Awareness
$modversion['pages'][1]['name'] = _PROFILE_MI_PAGE_INFO;
$modversion['pages'][1]['url'] = "userinfo.php";
$modversion['pages'][2]['name'] = _PROFILE_MI_PAGE_EDIT;
$modversion['pages'][2]['url'] = "edituser.php";
$modversion['pages'][3]['name'] = _PROFILE_MI_PAGE_SEARCH;
$modversion['pages'][3]['url'] = "search.php";

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][1] = "profile_category";
$modversion['tables'][2] = "profile_fieldcategory";

//update things
//$modversion['onUpdate'] = 'include/update.php';

// Config categories
$modversion['configcat'][1]['nameid'] = 'settings';
$modversion['configcat'][1]['name'] = '_PROFILE_MI_CAT_SETTINGS';
$modversion['configcat'][1]['description'] = '_PROFILE_MI_CAT_SETTINGS_DSC';

$modversion['configcat'][2]['nameid'] = 'user';
$modversion['configcat'][2]['name'] = '_PROFILE_MI_CAT_USER';
$modversion['configcat'][2]['description'] = '_PROFILE_MI_CAT_USER_DSC';

// Config items
$modversion['config'][1]['name'] = 'profile_search';
$modversion['config'][1]['title'] = '_PROFILE_MI_PROFILE_SEARCH';
$modversion['config'][1]['description'] = '_PROFILE_MI_PROFILE_SEARCH_DSC';
$modversion['config'][1]['formtype'] = 'yesno';
$modversion['config'][1]['valuetype'] = 'int';
$modversion['config'][1]['default'] = 1;
$modversion['config'][1]['category'] = 'settings';

$modversion['config'][2]['name'] = 'max_uname';
$modversion['config'][2]['title'] = '_PROFILE_MI_MAX_UNAME';
$modversion['config'][2]['description'] = '_PROFILE_MI_MAX_UNAME_DESC';
$modversion['config'][2]['formtype'] = 'textbox';
$modversion['config'][2]['valuetype'] = 'int';
$modversion['config'][2]['default'] = 20;
$modversion['config'][2]['category'] = 'user';

$modversion['config'][3]['name'] = 'min_uname';
$modversion['config'][3]['title'] = '_PROFILE_MI_MIN_UNAME';
$modversion['config'][3]['description'] = '_PROFILE_MI_MIN_UNAME_DESC';
$modversion['config'][3]['formtype'] = 'textbox';
$modversion['config'][3]['valuetype'] = 'int';
$modversion['config'][3]['default'] = 3;
$modversion['config'][3]['category'] = 'user';

$modversion['config'][4]['name'] = 'display_disclaimer';
$modversion['config'][4]['title'] = '_PROFILE_MI_DISPLAY_DISCLAIMER';
$modversion['config'][4]['description'] = '_PROFILE_MI_DISPLAY_DISCLAIMER_DESC';
$modversion['config'][4]['formtype'] = 'yesno';
$modversion['config'][4]['valuetype'] = 'int';
$modversion['config'][4]['default'] = 0;
$modversion['config'][4]['category'] = 'settings';

$modversion['config'][5]['name'] = 'disclaimer';
$modversion['config'][5]['title'] = '_PROFILE_MI_DISCLAIMER';
$modversion['config'][5]['description'] = '_PROFILE_MI_DISCLAIMER_DESC';
$modversion['config'][5]['formtype'] = 'textarea';
$modversion['config'][5]['valuetype'] = 'text';
$modversion['config'][5]['default'] = "";
$modversion['config'][5]['category'] = 'settings';

$modversion['config'][6]['name'] = 'bad_unames';
$modversion['config'][6]['title'] = '_PROFILE_MI_BAD_UNAMES';
$modversion['config'][6]['description'] = '_PROFILE_MI_BAD_UNAMES_DESC';
$modversion['config'][6]['formtype'] = 'textarea';
$modversion['config'][6]['valuetype'] = 'array';
$modversion['config'][6]['default'] = "webmaster|^xoops|^admin";
$modversion['config'][6]['category'] = 'user';

$modversion['config'][7]['name'] = 'bad_emails';
$modversion['config'][7]['title'] = '_PROFILE_MI_BAD_EMAILS';
$modversion['config'][7]['description'] = '_PROFILE_MI_BAD_EMAILS_DESC';
$modversion['config'][7]['formtype'] = 'textarea';
$modversion['config'][7]['valuetype'] = 'array';
$modversion['config'][7]['default'] = "xoops.org$";
$modversion['config'][7]['category'] = 'user';

$modversion['config'][8]['name'] = 'minpass';
$modversion['config'][8]['title'] = '_PROFILE_MI_MINPASS';
$modversion['config'][8]['description'] = '_PROFILE_MI_MINPASS_DESC';
$modversion['config'][8]['formtype'] = 'textbox';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = 3;
$modversion['config'][8]['category'] = 'user';

$modversion['config'][9]['name'] = 'new_user_notify';
$modversion['config'][9]['title'] = '_PROFILE_MI_NEWUNOTIFY';
$modversion['config'][9]['description'] = '_PROFILE_MI_NEWUNOTIFY_DESC';
$modversion['config'][9]['formtype'] = 'yesno';
$modversion['config'][9]['valuetype'] = 'int';
$modversion['config'][9]['default'] = 1;
$modversion['config'][9]['category'] = 'settings';

$modversion['config'][10]['name'] = 'new_user_notify_group';
$modversion['config'][10]['title'] = '_PROFILE_MI_NOTIFYTO';
$modversion['config'][10]['description'] = '_PROFILE_MI_NOTIFYTO_DESC';
$modversion['config'][10]['formtype'] = 'group';
$modversion['config'][10]['valuetype'] = 'int';
$modversion['config'][10]['default'] = 1;
$modversion['config'][10]['category'] = 'settings';

$modversion['config'][11]['name'] = 'activation_type';
$modversion['config'][11]['title'] = '_PROFILE_MI_ACTVTYPE';
$modversion['config'][11]['description'] = '_PROFILE_MI_ACTVTYPE_DESC';
$modversion['config'][11]['formtype'] = 'select';
$modversion['config'][11]['valuetype'] = 'int';
$modversion['config'][11]['default'] = 0;
$modversion['config'][11]['options'] = array('_PROFILE_MI_USERACTV' => 0,  '_PROFILE_MI_AUTOACTV' => 1, '_PROFILE_MI_ADMINACTV' => 2);$modversion['config'][11]['category'] = 'settings';

$modversion['config'][12]['name'] = 'activation_group';
$modversion['config'][12]['title'] = '_PROFILE_MI_ACTVGROUP';
$modversion['config'][12]['description'] = '_PROFILE_MI_ACTVGROUP_DESC';
$modversion['config'][12]['formtype'] = 'group';
$modversion['config'][12]['valuetype'] = 'int';
$modversion['config'][12]['default'] = 1;
$modversion['config'][12]['category'] = 'settings';

$modversion['config'][13]['name'] = 'uname_test_level';
$modversion['config'][13]['title'] = '_PROFILE_MI_UNAMELVL';
$modversion['config'][13]['description'] = '_PROFILE_MI_UNAMELVL_DESC';
$modversion['config'][13]['formtype'] = 'select';
$modversion['config'][13]['valuetype'] = 'int';
$modversion['config'][13]['default'] = 0;
$modversion['config'][13]['options'] = array('_PROFILE_MI_STRICT' => 0, '_PROFILE_MI_MEDIUM' => 1, '_PROFILE_MI_LIGHT' => 2);
$modversion['config'][13]['category'] = 'user';

$modversion['config'][14]['name'] = 'allow_register';
$modversion['config'][14]['title'] = '_PROFILE_MI_ALLOWREG';
$modversion['config'][14]['description'] = '_PROFILE_MI_ALLOWREG_DESC';
$modversion['config'][14]['formtype'] = 'yesno';
$modversion['config'][14]['valuetype'] = 'int';
$modversion['config'][14]['default'] = 1;
$modversion['config'][14]['category'] = 'settings';

$modversion['config'][15]['name'] = 'avatar_allow_upload';
$modversion['config'][15]['title'] = '_PROFILE_MI_AVATARALLOW';
$modversion['config'][15]['description'] = '_PROFILE_MI_AVATARALLOW_DESC';
$modversion['config'][15]['formtype'] = 'yesno';
$modversion['config'][15]['valuetype'] = 'int';
$modversion['config'][15]['default'] = 0;
$modversion['config'][15]['category'] = 'user';

$modversion['config'][16]['name'] = 'avatar_width';
$modversion['config'][16]['title'] = '_PROFILE_MI_AVATARWIDTH';
$modversion['config'][16]['description'] = '_PROFILE_MI_AVATARWIDTH_DESC';
$modversion['config'][16]['formtype'] = 'textbox';
$modversion['config'][16]['valuetype'] = 'int';
$modversion['config'][16]['default'] = 80;
$modversion['config'][16]['category'] = 'user';

$modversion['config'][17]['name'] = 'avatar_height';
$modversion['config'][17]['title'] = '_PROFILE_MI_AVATARHEIGHT';
$modversion['config'][17]['description'] = '_PROFILE_MI_AVATARHEIGHT_DESC';
$modversion['config'][17]['formtype'] = 'textbox';
$modversion['config'][17]['valuetype'] = 'int';
$modversion['config'][17]['default'] = 80;
$modversion['config'][17]['category'] = 'user';

$modversion['config'][18]['name'] = 'avatar_maxsize';
$modversion['config'][18]['title'] = '_PROFILE_MI_AVATARMAX';
$modversion['config'][18]['description'] = '_PROFILE_MI_AVATARMAX_DESC';
$modversion['config'][18]['formtype'] = 'textbox';
$modversion['config'][18]['valuetype'] = 'int';
$modversion['config'][18]['default'] = 35000;
$modversion['config'][18]['category'] = 'user';

$modversion['config'][19]['name'] = 'self_delete';
$modversion['config'][19]['title'] = '_PROFILE_MI_SELFDELETE';
$modversion['config'][19]['description'] = '_PROFILE_MI_SELFDELETE_DESC';
$modversion['config'][19]['formtype'] = 'yesno';
$modversion['config'][19]['valuetype'] = 'int';
$modversion['config'][19]['default'] = 0;
$modversion['config'][19]['category'] = 'settings';

$modversion['config'][20]['name'] = 'avatar_minposts';
$modversion['config'][20]['title'] = '_PROFILE_MI_AVATARMINPOSTS';
$modversion['config'][20]['description'] = '_PROFILE_MI_AVATARMINPOSTS_DESC';
$modversion['config'][20]['formtype'] = 'textbox';
$modversion['config'][20]['valuetype'] = 'int';
$modversion['config'][20]['default'] = 0;
$modversion['config'][20]['category'] = 'user';

$modversion['config'][21]['name'] = 'allow_chgmail';
$modversion['config'][21]['title'] = '_PROFILE_MI_ALLOWCHGMAIL';
$modversion['config'][21]['description'] = '_PROFILE_MI_ALLOWCHGMAIL_DESC';
$modversion['config'][21]['formtype'] = 'yesno';
$modversion['config'][21]['valuetype'] = 'int';
$modversion['config'][21]['default'] = 0;
$modversion['config'][21]['category'] = 'settings';

$modversion['config'][22]['name'] = 'allowed_groups';
$modversion['config'][22]['title'] = '_PROFILE_MI_ALLOWVIEWACC';
$modversion['config'][22]['description'] = '_PROFILE_MI_ALLOWVIEWACC_DESC';
$modversion['config'][22]['formtype'] = 'group_multi';
$modversion['config'][22]['valuetype'] = 'array';
$modversion['config'][22]['default'] = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS);
$modversion['config'][22]['category'] = 'settings';

// Templates
$modversion['templates'][1]['file'] = 'profile_admin_fieldlist.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'profile_userinfo.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'profile_admin_categorylist.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'profile_search.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'profile_results.html';
$modversion['templates'][5]['description'] = '';

// User Profile
$modversion['hasProfile'] = 1;

//$modversion['hasProfile'] = 1;
//$modversion['profile']['field'][1]['name'] = 'profile_aim'; 
// field name - can be referenced with $xoopsUser->getVar('user_aim')
//$modversion['profile']['field'][1]['type'] = 'textbox'; 
//type of form element for editing
//$modversion['profile']['field'][1]['valuetype'] = XOBJ_DTYPE_TXTBOX; 
//type of field - use XoopsObject valuetypes found in kernel/object.php

//$modversion['profile']['field'][1]['maxlength'] = 255; 
// maxlength of the field - Note: Mandatory when dealing with XOBJ_DTYPE_TXTBOX fields
//$modversion['profile']['field'][1]['default'] = ''; 
// Default value
//
//$modversion['profile']['field'][1]['show'] = 1; 
// can this field be shown in user profiles (still subject to group permissions)
//$modversion['profile']['field'][1]['title'] = _PROFILE_AIM_TITLE; 
// Name of field, when displayed - such as in user profile or editing
//
//$modversion['profile']['field'][1]['edit'] = 1; 
//can this field be edited in user profile editing (still subject to group permissions)
//$modversion['profile']['field'][1]['description'] = _PROFILE_AIM_DESCRIPTION; 
//description - such as when editing the profile, this will show up
//$modversion['profile']['field'][1]['required'] = 0; 
// is field required when editing?
//
//$modversion['profile']['field'][1]['config'] = 1; 
// can this field be configured? Don't use this if you rely on this field's information in your module code as configuration can alter the field completely - or DELETE it - if this is enabled
//$modversion['profile']['field'][1]['options'] = array();
$modversion['profile']['field'][1]['name'] = 'user_aim';
$modversion['profile']['field'][1]['type'] = 'textbox';
$modversion['profile']['field'][1]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][1]['maxlength'] = 255;
$modversion['profile']['field'][1]['default'] = '';
$modversion['profile']['field'][1]['show'] = 1; 
$modversion['profile']['field'][1]['title'] = _PROFILE_MI_AIM_TITLE; 
$modversion['profile']['field'][1]['edit'] = 1; 
$modversion['profile']['field'][1]['description'] = _PROFILE_MI_AIM_DESCRIPTION; 
$modversion['profile']['field'][1]['required'] = 0;
$modversion['profile']['field'][1]['config'] = 1; 
$modversion['profile']['field'][1]['options'] = array();

$modversion['profile']['field'][2]['name'] = 'user_icq';
$modversion['profile']['field'][2]['type'] = 'textbox';
$modversion['profile']['field'][2]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][2]['maxlength'] = 255;
$modversion['profile']['field'][2]['default'] = '';
$modversion['profile']['field'][2]['show'] = 1; 
$modversion['profile']['field'][2]['title'] = _PROFILE_MI_ICQ_TITLE; 
$modversion['profile']['field'][2]['edit'] = 1; 
$modversion['profile']['field'][2]['description'] = _PROFILE_MI_ICQ_DESCRIPTION; 
$modversion['profile']['field'][2]['required'] = 0;
$modversion['profile']['field'][2]['config'] = 1; 

$modversion['profile']['field'][3]['name'] = 'user_from';
$modversion['profile']['field'][3]['type'] = 'textbox';
$modversion['profile']['field'][3]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][3]['maxlength'] = 255;
$modversion['profile']['field'][3]['default'] = '';
$modversion['profile']['field'][3]['show'] = 1; 
$modversion['profile']['field'][3]['title'] = _PROFILE_MI_FROM_TITLE; 
$modversion['profile']['field'][3]['edit'] = 1; 
$modversion['profile']['field'][3]['description'] = _PROFILE_MI_FROM_DESCRIPTION; 
$modversion['profile']['field'][3]['required'] = 0;
$modversion['profile']['field'][3]['config'] = 1; 

$modversion['profile']['field'][4]['name'] = 'user_sig';
$modversion['profile']['field'][4]['title'] = _PROFILE_MI_SIG_TITLE;
$modversion['profile']['field'][4]['description'] = _PROFILE_MI_SIG_DESCRIPTION; 
$modversion['profile']['field'][4]['type'] = 'dhtml';
$modversion['profile']['field'][4]['valuetype'] = XOBJ_DTYPE_TXTAREA;
$modversion['profile']['field'][4]['default'] = '';
$modversion['profile']['field'][4]['show'] = 1; 
$modversion['profile']['field'][4]['edit'] = 1; 
$modversion['profile']['field'][4]['required'] = 0;
$modversion['profile']['field'][4]['config'] = 1; 

$modversion['profile']['field'][5]['name'] = 'user_viewemail';
$modversion['profile']['field'][5]['title'] = _PROFILE_MI_VIEWEMAIL_TITLE;
$modversion['profile']['field'][5]['description'] = ""; 
$modversion['profile']['field'][5]['type'] = 'yesno';
$modversion['profile']['field'][5]['valuetype'] = XOBJ_DTYPE_INT;
$modversion['profile']['field'][5]['maxlength'] = 1;
$modversion['profile']['field'][5]['default'] = 0;
$modversion['profile']['field'][5]['show'] = 0;
$modversion['profile']['field'][5]['edit'] = 1; 
$modversion['profile']['field'][5]['required'] = 0;
$modversion['profile']['field'][5]['config'] = 0; 

$modversion['profile']['field'][6]['name'] = 'user_yim';
$modversion['profile']['field'][6]['title'] = _PROFILE_MI_YIM_TITLE;
$modversion['profile']['field'][6]['description'] = _PROFILE_MI_YIM_DESCRIPTION; 
$modversion['profile']['field'][6]['type'] = 'textbox';
$modversion['profile']['field'][6]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][6]['maxlength'] = 255;
$modversion['profile']['field'][6]['default'] = "";
$modversion['profile']['field'][6]['show'] = 1;
$modversion['profile']['field'][6]['edit'] = 1; 
$modversion['profile']['field'][6]['required'] = 0;
$modversion['profile']['field'][6]['config'] = 1;

$modversion['profile']['field'][7]['name'] = 'user_msnm';
$modversion['profile']['field'][7]['title'] = _PROFILE_MI_MSN_TITLE;
$modversion['profile']['field'][7]['description'] = _PROFILE_MI_MSN_DESCRIPTION; 
$modversion['profile']['field'][7]['type'] = 'textbox';
$modversion['profile']['field'][7]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][7]['maxlength'] = 255;
$modversion['profile']['field'][7]['default'] = "";
$modversion['profile']['field'][7]['show'] = 1;
$modversion['profile']['field'][7]['edit'] = 1; 
$modversion['profile']['field'][7]['required'] = 0;
$modversion['profile']['field'][7]['config'] = 1;

$modversion['profile']['field'][8]['name'] = 'bio';
$modversion['profile']['field'][8]['title'] = _PROFILE_MI_BIO_TITLE;
$modversion['profile']['field'][8]['description'] = _PROFILE_MI_BIO_DESCRIPTION; 
$modversion['profile']['field'][8]['type'] = 'textarea';
$modversion['profile']['field'][8]['valuetype'] = XOBJ_DTYPE_TXTAREA;
$modversion['profile']['field'][8]['default'] = '';
$modversion['profile']['field'][8]['show'] = 1; 
$modversion['profile']['field'][8]['edit'] = 1; 
$modversion['profile']['field'][8]['required'] = 0;
$modversion['profile']['field'][8]['config'] = 1; 

$modversion['profile']['field'][9]['name'] = 'user_intrest';
$modversion['profile']['field'][9]['title'] = _PROFILE_MI_INTEREST_TITLE;
$modversion['profile']['field'][9]['description'] = _PROFILE_MI_INTEREST_DESCRIPTION; 
$modversion['profile']['field'][9]['type'] = 'textbox';
$modversion['profile']['field'][9]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][9]['maxlength'] = 150;
$modversion['profile']['field'][9]['default'] = "";
$modversion['profile']['field'][9]['show'] = 1;
$modversion['profile']['field'][9]['edit'] = 1; 
$modversion['profile']['field'][9]['required'] = 0;
$modversion['profile']['field'][9]['config'] = 1;

$modversion['profile']['field'][10]['name'] = 'user_occ';
$modversion['profile']['field'][10]['title'] = _PROFILE_MI_OCCUPATION_TITLE;
$modversion['profile']['field'][10]['description'] = _PROFILE_MI_OCCUPATION_DESCRIPTION; 
$modversion['profile']['field'][10]['type'] = 'textbox';
$modversion['profile']['field'][10]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][10]['maxlength'] = 100;
$modversion['profile']['field'][10]['default'] = "";
$modversion['profile']['field'][10]['show'] = 1;
$modversion['profile']['field'][10]['edit'] = 1; 
$modversion['profile']['field'][10]['required'] = 0;
$modversion['profile']['field'][10]['config'] = 1;

$modversion['profile']['field'][11]['name'] = 'url';
$modversion['profile']['field'][11]['title'] = _PROFILE_MI_URL_TITLE;
$modversion['profile']['field'][11]['description'] = _PROFILE_MI_URL_DESCRIPTION; 
$modversion['profile']['field'][11]['type'] = 'textbox';
$modversion['profile']['field'][11]['valuetype'] = XOBJ_DTYPE_URL;
$modversion['profile']['field'][11]['maxlength'] = 100;
$modversion['profile']['field'][11]['default'] = "";
$modversion['profile']['field'][11]['show'] = 1;
$modversion['profile']['field'][11]['edit'] = 1; 
$modversion['profile']['field'][11]['required'] = 0;
$modversion['profile']['field'][11]['config'] = 1;

$modversion['profile']['field'][12]['name'] = 'newemail';
$modversion['profile']['field'][12]['title'] = _PROFILE_MI_NEWEMAIL_TITLE;
$modversion['profile']['field'][12]['description'] = _PROFILE_MI_NEWEMAIL_DESCRIPTION; 
$modversion['profile']['field'][12]['type'] = 'textbox';
$modversion['profile']['field'][12]['valuetype'] = XOBJ_DTYPE_EMAIL;
$modversion['profile']['field'][12]['maxlength'] = 100;
$modversion['profile']['field'][12]['default'] = "";
$modversion['profile']['field'][12]['show'] = 0;
$modversion['profile']['field'][12]['edit'] = 0; 
$modversion['profile']['field'][12]['required'] = 0;
$modversion['profile']['field'][12]['config'] = 0;
?>