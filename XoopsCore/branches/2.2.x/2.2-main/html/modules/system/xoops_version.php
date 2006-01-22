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

$modversion['name'] = _MI_SYSTEM_NAME;
$modversion['version'] = 2.13;
$modversion['description'] = _MI_SYSTEM_DESC;
$modversion['author'] = "";
$modversion['credits'] = "The XOOPS Project";
$modversion['help'] = "system.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 1;
$modversion['image'] = "images/system_slogo.png";
$modversion['dirname'] = "system";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin.php";
$modversion['adminmenu'] = "menu.php";

// Menu
$modversion['hasMain'] = 0;

//update things
$modversion['onUpdate'] = 'include/update.php';

// Templates
$modversion['templates'][1]['file'] = 'system_imagemanager.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'system_imagemanager2.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'system_userinfo.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'system_userform.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'system_rss.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'system_redirect.html';
$modversion['templates'][6]['description'] = '';
$modversion['templates'][7]['file'] = 'system_comment.html';
$modversion['templates'][7]['description'] = '';
$modversion['templates'][8]['file'] = 'system_comments_flat.html';
$modversion['templates'][8]['description'] = '';
$modversion['templates'][9]['file'] = 'system_comments_thread.html';
$modversion['templates'][9]['description'] = '';
$modversion['templates'][10]['file'] = 'system_comments_nest.html';
$modversion['templates'][10]['description'] = '';
$modversion['templates'][11]['file'] = 'system_siteclosed.html';
$modversion['templates'][11]['description'] = '';
$modversion['templates'][12]['file'] = 'system_dummy.html';
$modversion['templates'][12]['description'] = 'Dummy template file for holding non-template contents. This should not be edited.';
$modversion['templates'][13]['file'] = 'system_notification_list.html';
$modversion['templates'][13]['description'] = '';
$modversion['templates'][14]['file'] = 'system_notification_select.html';
$modversion['templates'][14]['description'] = '';
//$modversion['templates'][15]['file'] = 'system_block_dummy.html';
//$modversion['templates'][15]['description'] = 'Dummy template for custom blocks or blocks without templates';
$modversion['templates'][16]['file'] = 'system_error.html';
$modversion['templates'][16]['description'] = 'Template for error pages';
$modversion['templates'][17]['file'] = 'system_plain.html';
$modversion['templates'][17]['description'] = 'Plain template for displaying only page content (no logo, no banner, no blocks)';
$modversion['templates'][18]['file'] = 'system_admin_block.html';
$modversion['templates'][18]['description'] = 'Blocks Administration Template';

// Blocks
$modversion['blocks'][1]['file'] = "system_blocks.php";
$modversion['blocks'][1]['name'] = _MI_SYSTEM_BNAME2;
$modversion['blocks'][1]['description'] = "Shows user block";
$modversion['blocks'][1]['show_func'] = "b_system_user_show";
$modversion['blocks'][1]['template'] = 'system_block_user.html';

$modversion['blocks'][2]['file'] = "system_blocks.php";
$modversion['blocks'][2]['name'] = _MI_SYSTEM_BNAME3;
$modversion['blocks'][2]['description'] = "Shows login form";
$modversion['blocks'][2]['show_func'] = "b_system_login_show";
$modversion['blocks'][2]['template'] = 'system_block_login.html';

$modversion['blocks'][3]['file'] = "system_blocks.php";
$modversion['blocks'][3]['name'] = _MI_SYSTEM_BNAME4;
$modversion['blocks'][3]['description'] = "Shows search form block";
$modversion['blocks'][3]['show_func'] = "b_system_search_show";
$modversion['blocks'][3]['template'] = 'system_block_search.html';

$modversion['blocks'][4]['file'] = "system_blocks.php";
$modversion['blocks'][4]['name'] = _MI_SYSTEM_BNAME5;
$modversion['blocks'][4]['description'] = "Shows contents waiting for approval";
$modversion['blocks'][4]['show_func'] = "b_system_waiting_show";
$modversion['blocks'][4]['template'] = 'system_block_waiting.html';

$modversion['blocks'][5]['file'] = "system_blocks.php";
$modversion['blocks'][5]['name'] = _MI_SYSTEM_BNAME6;
$modversion['blocks'][5]['description'] = "Shows the main navigation menu of the site";
$modversion['blocks'][5]['show_func'] = "b_system_main_show";
$modversion['blocks'][5]['template'] = 'system_block_mainmenu.html';

$modversion['blocks'][6]['file'] = "system_blocks.php";
$modversion['blocks'][6]['name'] = _MI_SYSTEM_BNAME7;
$modversion['blocks'][6]['description'] = "Shows basic info about the site and a link to Recommend Us pop up window";
$modversion['blocks'][6]['show_func'] = "b_system_info_show";
$modversion['blocks'][6]['edit_func'] = "b_system_info_edit";
$modversion['blocks'][6]['options'] = "320|190|s_poweredby.gif|1";
$modversion['blocks'][6]['template'] = 'system_block_siteinfo.html';

$modversion['blocks'][7]['file'] = "system_blocks.php";
$modversion['blocks'][7]['name'] = _MI_SYSTEM_BNAME8;
$modversion['blocks'][7]['description'] = "Displays users/guests currently online";
$modversion['blocks'][7]['show_func'] = "b_system_online_show";
$modversion['blocks'][7]['template'] = 'system_block_online.html';

$modversion['blocks'][8]['file'] = "system_blocks.php";
$modversion['blocks'][8]['name'] = _MI_SYSTEM_BNAME9;
$modversion['blocks'][8]['description'] = "Top posters";
$modversion['blocks'][8]['show_func'] = "b_system_topposters_show";
$modversion['blocks'][8]['options'] = "10|1";
$modversion['blocks'][8]['edit_func'] = "b_system_topposters_edit";
$modversion['blocks'][8]['template'] = 'system_block_topusers.html';

$modversion['blocks'][9]['file'] = "system_blocks.php";
$modversion['blocks'][9]['name'] = _MI_SYSTEM_BNAME10;
$modversion['blocks'][9]['description'] = "Shows most recent users";
$modversion['blocks'][9]['show_func'] = "b_system_newmembers_show";
$modversion['blocks'][9]['options'] = "10|1";
$modversion['blocks'][9]['edit_func'] = "b_system_newmembers_edit";
$modversion['blocks'][9]['template'] = 'system_block_newusers.html';

$modversion['blocks'][10]['file'] = "system_blocks.php";
$modversion['blocks'][10]['name'] = _MI_SYSTEM_BNAME11;
$modversion['blocks'][10]['description'] = "Shows most recent comments";
$modversion['blocks'][10]['show_func'] = "b_system_comments_show";
$modversion['blocks'][10]['options'] = "10";
$modversion['blocks'][10]['edit_func'] = "b_system_comments_edit";
$modversion['blocks'][10]['template'] = 'system_block_comments.html';

// RMV-NOTIFY:
// Adding a block...
$modversion['blocks'][11]['file'] = "system_blocks.php";
$modversion['blocks'][11]['name'] = _MI_SYSTEM_BNAME12;
$modversion['blocks'][11]['description'] = "Shows notification options";
$modversion['blocks'][11]['show_func'] = "b_system_notification_show";
$modversion['blocks'][11]['template'] = 'system_block_notification.html';

$modversion['blocks'][12]['file'] = "system_blocks.php";
$modversion['blocks'][12]['name'] = _MI_SYSTEM_BNAME13;
$modversion['blocks'][12]['description'] = "Shows theme selection box";
$modversion['blocks'][12]['show_func'] = "b_system_themes_show";
$modversion['blocks'][12]['options'] = "0|80";
$modversion['blocks'][12]['edit_func'] = "b_system_themes_edit";
$modversion['blocks'][12]['template'] = 'system_block_themes.html';

$modversion['blocks'][13]['file'] = "system_blocks.php";
$modversion['blocks'][13]['name'] = _MI_SYSTEM_BNAME14;
$modversion['blocks'][13]['description'] = "Custom block for manual content input";
$modversion['blocks'][13]['show_func'] = "b_system_custom_show";
$modversion['blocks'][13]['options'] = "|1";
$modversion['blocks'][13]['edit_func'] = "b_system_custom_edit";
$modversion['blocks'][13]['template'] = 'system_block_dummy.html';

// Profile fields
$modversion['hasProfile'] = 1;
include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
$modversion['profile']['field'][1]['name'] = 'umode';
$modversion['profile']['field'][1]['type'] = 'select';
$modversion['profile']['field'][1]['valuetype'] = XOBJ_DTYPE_OTHER;
//$modversion['profile']['field'][1]['maxlength'] = 255;
$modversion['profile']['field'][1]['default'] = '';
$modversion['profile']['field'][1]['show'] = 0;
$modversion['profile']['field'][1]['title'] = _MI_SYSTEM_UMODE_TITLE; 
$modversion['profile']['field'][1]['edit'] = 1; 
$modversion['profile']['field'][1]['description'] = ""; 
$modversion['profile']['field'][1]['required'] = 0;
$modversion['profile']['field'][1]['config'] = 0; 
$modversion['profile']['field'][1]['options'] = array('nest'=>_NESTED, 'flat'=>_FLAT, 'thread'=>_THREADED);

$modversion['profile']['field'][2]['name'] = 'uorder';
$modversion['profile']['field'][2]['type'] = 'select';
$modversion['profile']['field'][2]['valuetype'] = XOBJ_DTYPE_OTHER;
//$modversion['profile']['field'][1]['maxlength'] = 255;
$modversion['profile']['field'][2]['default'] = '';
$modversion['profile']['field'][2]['show'] = 0;
$modversion['profile']['field'][2]['title'] = _MI_SYSTEM_UORDER_TITLE; 
$modversion['profile']['field'][2]['edit'] = 1; 
$modversion['profile']['field'][2]['description'] = ""; 
$modversion['profile']['field'][2]['required'] = 0;
$modversion['profile']['field'][2]['config'] = 0; 
$modversion['profile']['field'][2]['options'] = array(XOOPS_COMMENT_OLD1ST => _OLDESTFIRST, XOOPS_COMMENT_NEW1ST => _NEWESTFIRST);


include_once XOOPS_ROOT_PATH . "/language/" . $xoopsConfig['language'] . '/notification.php';
include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
$modversion['profile']['field'][3]['name'] = 'notify_method';
$modversion['profile']['field'][3]['type'] = 'select';
$modversion['profile']['field'][3]['valuetype'] = XOBJ_DTYPE_OTHER;
//$modversion['profile']['field'][1]['maxlength'] = 255;
$modversion['profile']['field'][3]['default'] = '';
$modversion['profile']['field'][3]['show'] = 0;
$modversion['profile']['field'][3]['title'] = _NOT_NOTIFYMETHOD; 
$modversion['profile']['field'][3]['edit'] = 1; 
$modversion['profile']['field'][3]['description'] = _NOT_NOTIFYMETHOD_DESC; 
$modversion['profile']['field'][3]['required'] = 0;
$modversion['profile']['field'][3]['config'] = 0; 
$modversion['profile']['field'][3]['options'] = array(XOOPS_NOTIFICATION_METHOD_DISABLE=>_NOT_METHOD_DISABLE, XOOPS_NOTIFICATION_METHOD_PM=>_NOT_METHOD_PM, XOOPS_NOTIFICATION_METHOD_EMAIL=>_NOT_METHOD_EMAIL);

$modversion['profile']['field'][4]['name'] = 'notify_mode';
$modversion['profile']['field'][4]['type'] = 'select';
$modversion['profile']['field'][4]['valuetype'] = XOBJ_DTYPE_OTHER;
//$modversion['profile']['field'][1]['maxlength'] = 255;
$modversion['profile']['field'][4]['default'] = '';
$modversion['profile']['field'][4]['show'] = 0;
$modversion['profile']['field'][4]['title'] = _NOT_NOTIFYMODE; 
$modversion['profile']['field'][4]['edit'] = 1; 
$modversion['profile']['field'][4]['description'] = ""; 
$modversion['profile']['field'][4]['required'] = 0;
$modversion['profile']['field'][4]['config'] = 0; 
$modversion['profile']['field'][4]['options'] = array(XOOPS_NOTIFICATION_MODE_SENDALWAYS=>_NOT_MODE_SENDALWAYS, XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE=>_NOT_MODE_SENDONCE, XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT=>_NOT_MODE_SENDONCEPERLOGIN);

/*$modversion['profile']['field'][5]['name'] = 'name';
$modversion['profile']['field'][5]['type'] = 'textbox';
$modversion['profile']['field'][5]['valuetype'] = XOBJ_DTYPE_TXTBOX;
$modversion['profile']['field'][5]['maxlength'] = 255;
$modversion['profile']['field'][5]['default'] = '';
$modversion['profile']['field'][5]['show'] = 1;
$modversion['profile']['field'][5]['title'] = _MI_SYSTEM_REALNAME; 
$modversion['profile']['field'][5]['edit'] = 1; 
$modversion['profile']['field'][5]['description'] = ""; 
$modversion['profile']['field'][5]['required'] = 0;
$modversion['profile']['field'][5]['config'] = 1;*/

$modversion['profile']['field'][6]['name'] = 'user_regdate';
$modversion['profile']['field'][6]['type'] = 'date';
$modversion['profile']['field'][6]['valuetype'] = XOBJ_DTYPE_INT;
$modversion['profile']['field'][6]['maxlength'] = 10;
$modversion['profile']['field'][6]['default'] = 0;
$modversion['profile']['field'][6]['show'] = 1;
$modversion['profile']['field'][6]['title'] = _MI_SYSTEM_USERREGDATE;
$modversion['profile']['field'][6]['edit'] = 0; 
$modversion['profile']['field'][6]['description'] = ""; 
$modversion['profile']['field'][6]['required'] = 0;
$modversion['profile']['field'][6]['config'] = 0;

$modversion['profile']['field'][7]['name'] = 'posts';
$modversion['profile']['field'][7]['type'] = 'textbox';
$modversion['profile']['field'][7]['valuetype'] = XOBJ_DTYPE_INT;
$modversion['profile']['field'][7]['maxlength'] = 10;
$modversion['profile']['field'][7]['default'] = 0;
$modversion['profile']['field'][7]['show'] = 1;
$modversion['profile']['field'][7]['title'] = _MI_SYSTEM_USERPOSTS;
$modversion['profile']['field'][7]['edit'] = 0; 
$modversion['profile']['field'][7]['description'] = ""; 
$modversion['profile']['field'][7]['required'] = 0;
$modversion['profile']['field'][7]['config'] = 0;

$modversion['profile']['field'][8]['name'] = 'attachsig';
$modversion['profile']['field'][8]['type'] = 'yesno';
$modversion['profile']['field'][8]['valuetype'] = XOBJ_DTYPE_INT;
$modversion['profile']['field'][8]['maxlength'] = 1;
$modversion['profile']['field'][8]['default'] = '';
$modversion['profile']['field'][8]['show'] = 0;
$modversion['profile']['field'][8]['title'] = _MI_SYSTEM_ATTACHSIG;
$modversion['profile']['field'][8]['edit'] = 1; 
$modversion['profile']['field'][8]['description'] = ""; 
$modversion['profile']['field'][8]['required'] = 0;
$modversion['profile']['field'][8]['config'] = 0;

$modversion['profile']['field'][9]['name'] = 'timezone_offset';
$modversion['profile']['field'][9]['type'] = 'timezone';
$modversion['profile']['field'][9]['valuetype'] = XOBJ_DTYPE_OTHER;
$modversion['profile']['field'][9]['maxlength'] = 255;
$modversion['profile']['field'][9]['default'] = '';
$modversion['profile']['field'][9]['show'] = 0;
$modversion['profile']['field'][9]['title'] = _MI_SYSTEM_TIMEZONEOFFSET;
$modversion['profile']['field'][9]['edit'] = 1; 
$modversion['profile']['field'][9]['description'] = "";
$modversion['profile']['field'][9]['required'] = 0;
$modversion['profile']['field'][9]['config'] = 0;

$modversion['profile']['field'][10]['name'] = 'user_mailok';
$modversion['profile']['field'][10]['type'] = 'yesno';
$modversion['profile']['field'][10]['valuetype'] = XOBJ_DTYPE_INT;
$modversion['profile']['field'][10]['maxlength'] = 1;
$modversion['profile']['field'][10]['default'] = 1;
$modversion['profile']['field'][10]['show'] = 0;
$modversion['profile']['field'][10]['title'] = _MI_SYSTEM_MAILOK;
$modversion['profile']['field'][10]['edit'] = 1; 
$modversion['profile']['field'][10]['description'] = _MI_SYSTEM_MAILOK_DESC; 
$modversion['profile']['field'][10]['required'] = 0;
$modversion['profile']['field'][10]['config'] = 0;

$modversion['profile']['field'][11]['name'] = 'theme';
$modversion['profile']['field'][11]['type'] = 'theme';
$modversion['profile']['field'][11]['valuetype'] = XOBJ_DTYPE_OTHER;
//$modversion['profile']['field'][11]['maxlength'] = 255;
$modversion['profile']['field'][11]['default'] = "0";
$modversion['profile']['field'][11]['show'] = 0;
$modversion['profile']['field'][11]['title'] = _MI_SYSTEM_USERTHEME;
$modversion['profile']['field'][11]['edit'] = 1; 
$modversion['profile']['field'][11]['description'] = ""; 
$modversion['profile']['field'][11]['required'] = 0;
$modversion['profile']['field'][11]['config'] = 0;

$modversion['profile']['field'][12]['name'] = 'actkey';
$modversion['profile']['field'][12]['type'] = 'textbox';
$modversion['profile']['field'][12]['valuetype'] = XOBJ_DTYPE_OTHER;
//$modversion['profile']['field'][12]['maxlength'] = 255;
$modversion['profile']['field'][12]['default'] = "";
$modversion['profile']['field'][12]['show'] = 0;
$modversion['profile']['field'][12]['title'] = _MI_SYSTEM_ACTKEY;
$modversion['profile']['field'][12]['edit'] = 0;
$modversion['profile']['field'][12]['description'] = ""; 
$modversion['profile']['field'][12]['required'] = 0;
$modversion['profile']['field'][12]['config'] = 0;

$modversion['profile']['field'][13]['name'] = 'last_login';
$modversion['profile']['field'][13]['type'] = 'date';
$modversion['profile']['field'][13]['valuetype'] = XOBJ_DTYPE_INT;
$modversion['profile']['field'][13]['maxlength'] = 10;
$modversion['profile']['field'][13]['default'] = "";
$modversion['profile']['field'][13]['show'] = 1;
$modversion['profile']['field'][13]['title'] = _MI_SYSTEM_LASTLOGIN;
$modversion['profile']['field'][13]['edit'] = 0;
$modversion['profile']['field'][13]['description'] = ""; 
$modversion['profile']['field'][13]['required'] = 0;
$modversion['profile']['field'][13]['config'] = 0;

// Config categories
$modversion['configcat'][1]['nameid'] = 'general';
$modversion['configcat'][1]['name'] = '_MD_AM_GENERAL';
$modversion['configcat'][1]['description'] = '';

//category 2 is the old user settings category

$modversion['configcat'][3]['nameid'] = 'meta';
$modversion['configcat'][3]['name'] = '_MD_AM_METAFOOTER';
$modversion['configcat'][3]['description'] = '';

$modversion['configcat'][4]['nameid'] = 'censor';
$modversion['configcat'][4]['name'] = '_MD_AM_CENSOR';
$modversion['configcat'][4]['description'] = '';

$modversion['configcat'][5]['nameid'] = 'search';
$modversion['configcat'][5]['name'] = '_MD_AM_SEARCH';
$modversion['configcat'][5]['description'] = '';

$modversion['configcat'][6]['nameid'] = 'mail';
$modversion['configcat'][6]['name'] = '_MD_AM_MAILER';
$modversion['configcat'][6]['description'] = '';

$modversion['configcat'][7]['nameid'] = 'auth';
$modversion['configcat'][7]['name'] = '_MD_AM_AUTHENTICATION';
$modversion['configcat'][7]['description'] = '';

// data for table 'config' 
$modversion['config'][1]['name'] = 'sitename';
$modversion['config'][1]['title'] = '_MD_AM_SITENAME';
$modversion['config'][1]['description'] = '_MD_AM_SITENAMEDSC';
$modversion['config'][1]['formtype'] = 'textbox';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = "XOOPS Site";
$modversion['config'][1]['category'] = 'general';

$modversion['config'][2]['name'] = 'slogan';
$modversion['config'][2]['title'] = '_MD_AM_SLOGAN';
$modversion['config'][2]['description'] = '_MD_AM_SLOGANDSC';
$modversion['config'][2]['formtype'] = 'textbox';
$modversion['config'][2]['valuetype'] = 'text';
$modversion['config'][2]['default'] = "Powered by You!";
$modversion['config'][2]['category'] = 'general';

$modversion['config'][3]['name'] = 'adminmail';
$modversion['config'][3]['title'] = '_MD_AM_ADMINML';
$modversion['config'][3]['description'] = '_MD_AM_ADMINMLDSC';
$modversion['config'][3]['formtype'] = 'textbox';
$modversion['config'][3]['valuetype'] = 'text';
$modversion['config'][3]['default'] = addslashes($GLOBALS['xoopsConfig']['adminmail']); //TODO: Where to get adminmail?
$modversion['config'][3]['category'] = 'general';

$modversion['config'][4]['name'] = 'language';
$modversion['config'][4]['title'] = '_MD_AM_LANGUAGE';
$modversion['config'][4]['description'] = '_MD_AM_LANGUAGEDSC';
$modversion['config'][4]['formtype'] = 'language';
$modversion['config'][4]['valuetype'] = 'other';
$modversion['config'][4]['default'] = addslashes($GLOBALS['xoopsConfig']['language']);
$modversion['config'][4]['category'] = 'general';

$modversion['config'][5]['name'] = 'startpage';
$modversion['config'][5]['title'] = '_MD_AM_STARTPAGE';
$modversion['config'][5]['description'] = '_MD_AM_STARTPAGEDSC';
$modversion['config'][5]['formtype'] = 'startpage';
$modversion['config'][5]['valuetype'] = 'other';
$modversion['config'][5]['default'] = '--';
$modversion['config'][5]['category'] = 'general';

$modversion['config'][6]['name'] = 'server_TZ';
$modversion['config'][6]['title'] = '_MD_AM_SERVERTZ';
$modversion['config'][6]['description'] = '_MD_AM_SERVERTZDSC';
$modversion['config'][6]['formtype'] = 'timezone';
$modversion['config'][6]['valuetype'] = 'float';
$modversion['config'][6]['default'] = "0";
$modversion['config'][6]['category'] = 'general';

$modversion['config'][7]['name'] = 'default_TZ';
$modversion['config'][7]['title'] = '_MD_AM_DEFAULTTZ';
$modversion['config'][7]['description'] = '_MD_AM_DEFAULTTZDSC';
$modversion['config'][7]['formtype'] = 'timezone';
$modversion['config'][7]['valuetype'] = 'float';
$modversion['config'][7]['default'] = "0";
$modversion['config'][7]['category'] = 'general';

$modversion['config'][8]['name'] = 'theme_set';
$modversion['config'][8]['title'] = '_MD_AM_DTHEME';
$modversion['config'][8]['description'] = '_MD_AM_DTHEMEDSC';
$modversion['config'][8]['formtype'] = 'theme';
$modversion['config'][8]['valuetype'] = 'other';
$modversion['config'][8]['default'] = "default";
$modversion['config'][8]['category'] = 'general';

$modversion['config'][9]['name'] = 'theme_set_admin';
$modversion['config'][9]['title'] = '_MD_AM_ADMINTHEME';
$modversion['config'][9]['description'] = '_MD_AM_ADMINTHEMEDSC';
$modversion['config'][9]['formtype'] = 'theme_admin';
$modversion['config'][9]['valuetype'] = 'other';
$modversion['config'][9]['default'] = "0";
$modversion['config'][9]['category'] = 'general';

$modversion['config'][10]['name'] = 'theme_fromfile';
$modversion['config'][10]['title'] = '_MD_AM_THEMEFILE';
$modversion['config'][10]['description'] = '_MD_AM_THEMEFILEDSC';
$modversion['config'][10]['formtype'] = 'yesno';
$modversion['config'][10]['valuetype'] = 'int';
$modversion['config'][10]['default'] = "0";
$modversion['config'][10]['category'] = 'general';

$modversion['config'][11]['name'] = 'theme_set_allowed';
$modversion['config'][11]['title'] = '_MD_AM_THEMEOK';
$modversion['config'][11]['description'] = '_MD_AM_THEMEOKDSC';
$modversion['config'][11]['formtype'] = 'theme_multi';
$modversion['config'][11]['valuetype'] = 'array';
$modversion['config'][11]['default'] = array('default');
$modversion['config'][11]['category'] = 'general';

$modversion['config'][12]['name'] = 'template_set';
$modversion['config'][12]['title'] = '_MD_AM_DTPLSET';
$modversion['config'][12]['description'] = '_MD_AM_DTPLSETDSC';
$modversion['config'][12]['formtype'] = 'tplset';
$modversion['config'][12]['valuetype'] = 'other';
$modversion['config'][12]['default'] = "default";
$modversion['config'][12]['category'] = 'general';

$modversion['config'][13]['name'] = 'anonymous';
$modversion['config'][13]['title'] = '_MD_AM_ANONNAME';
$modversion['config'][13]['description'] = '_MD_AM_ANONNAMEDSC';
$modversion['config'][13]['formtype'] = 'textbox';
$modversion['config'][13]['valuetype'] = 'text';
$modversion['config'][13]['default'] = addslashes(_MI_SYSTEM_ANONYMOUS);
$modversion['config'][13]['category'] = 'general';

$modversion['config'][14]['name'] = 'gzip_compression';
$modversion['config'][14]['title'] = '_MD_AM_USEGZIP';
$modversion['config'][14]['description'] = '_MD_AM_USEGZIPDSC';
$modversion['config'][14]['formtype'] = 'yesno';
$modversion['config'][14]['valuetype'] = 'int';
$modversion['config'][14]['default'] = "0";
$modversion['config'][14]['category'] = 'general';

$modversion['config'][15]['name'] = 'usercookie';
$modversion['config'][15]['title'] = '_MD_AM_USERCOOKIE';
$modversion['config'][15]['description'] = '_MD_AM_USERCOOKIEDSC';
$modversion['config'][15]['formtype'] = 'textbox';
$modversion['config'][15]['valuetype'] = 'text';
$modversion['config'][15]['default'] = "xoops_user";
$modversion['config'][15]['category'] = 'general';

$modversion['config'][16]['name'] = 'use_mysession';
$modversion['config'][16]['title'] = '_MD_AM_USEMYSESS';
$modversion['config'][16]['description'] = '_MD_AM_USEMYSESSDSC';
$modversion['config'][16]['formtype'] = 'yesno';
$modversion['config'][16]['valuetype'] = 'int';
$modversion['config'][16]['default'] = "0";
$modversion['config'][16]['category'] = 'general';

$modversion['config'][17]['name'] = 'session_expire';
$modversion['config'][17]['title'] = '_MD_AM_SESSEXPIRE';
$modversion['config'][17]['description'] = '_MD_AM_SESSEXPIREDSC';
$modversion['config'][17]['formtype'] = 'textbox';
$modversion['config'][17]['valuetype'] = 'int';
$modversion['config'][17]['default'] = "15";
$modversion['config'][17]['category'] = 'general';

$modversion['config'][18]['name'] = 'session_name';
$modversion['config'][18]['title'] = '_MD_AM_SESSNAME';
$modversion['config'][18]['description'] = '_MD_AM_SESSNAMEDSC';
$modversion['config'][18]['formtype'] = 'textbox';
$modversion['config'][18]['valuetype'] = 'text';
$modversion['config'][18]['default'] = "xoops_session";
$modversion['config'][18]['category'] = 'general';

$modversion['config'][19]['name'] = 'debug_mode';
$modversion['config'][19]['title'] = '_MD_AM_DEBUGMODE';
$modversion['config'][19]['description'] = '_MD_AM_DEBUGMODEDSC';
$modversion['config'][19]['formtype'] = 'select_multi';
$modversion['config'][19]['valuetype'] = 'array';
$modversion['config'][19]['default'] = array(1);
$modversion['config'][19]['options'] = array("_MD_AM_DEBUGMODE0" => 0, "_MD_AM_DEBUGMODE1" => 1, "_MD_AM_DEBUGMODE2" => 2, "_MD_AM_DEBUGMODE3" => 3);
$modversion['config'][19]['category'] = 'general';

$modversion['config'][20]['name'] = 'banners';
$modversion['config'][20]['title'] = '_MD_AM_BANNERS';
$modversion['config'][20]['description'] = '_MD_AM_BANNERSDSC';
$modversion['config'][20]['formtype'] = 'yesno';
$modversion['config'][20]['valuetype'] = 'int';
$modversion['config'][20]['default'] = "1";
$modversion['config'][20]['category'] = 'general';

$modversion['config'][21]['name'] = 'closesite';
$modversion['config'][21]['title'] = '_MD_AM_CLOSESITE';
$modversion['config'][21]['description'] = '_MD_AM_CLOSESITEDSC';
$modversion['config'][21]['formtype'] = 'yesno';
$modversion['config'][21]['valuetype'] = 'int';
$modversion['config'][21]['default'] = "0";
$modversion['config'][21]['category'] = 'general';

$modversion['config'][22]['name'] = 'closesite_okgrp';
$modversion['config'][22]['title'] = '_MD_AM_CLOSESITEOK';
$modversion['config'][22]['description'] = '_MD_AM_CLOSESITEOKDSC';
$modversion['config'][22]['formtype'] = 'group_multi';
$modversion['config'][22]['valuetype'] = 'array';
$modversion['config'][22]['default'] = array('1');
$modversion['config'][22]['category'] = 'general';

$modversion['config'][23]['name'] = 'closesite_text';
$modversion['config'][23]['title'] = '_MD_AM_CLOSESITETXT';
$modversion['config'][23]['description'] = '_MD_AM_CLOSESITETXTDSC';
$modversion['config'][23]['formtype'] = 'textarea';
$modversion['config'][23]['valuetype'] = 'text';
$modversion['config'][23]['default'] = _MI_SYSTEM_SITECLOSEDMSG;
$modversion['config'][23]['category'] = 'general';

$modversion['config'][24]['name'] = 'my_ip';
$modversion['config'][24]['title'] = '_MD_AM_MYIP';
$modversion['config'][24]['description'] = '_MD_AM_MYIPDSC';
$modversion['config'][24]['formtype'] = 'textbox';
$modversion['config'][24]['valuetype'] = 'text';
$modversion['config'][24]['default'] = "127.0.0.1";
$modversion['config'][24]['category'] = 'general';

$modversion['config'][25]['name'] = 'use_ssl';
$modversion['config'][25]['title'] = '_MD_AM_USESSL';
$modversion['config'][25]['description'] = '_MD_AM_USESSLDSC';
$modversion['config'][25]['formtype'] = 'yesno';
$modversion['config'][25]['valuetype'] = 'int';
$modversion['config'][25]['default'] = "0";
$modversion['config'][25]['category'] = 'general';

$modversion['config'][26]['name'] = 'sslpost_name';
$modversion['config'][26]['title'] = '_MD_AM_SSLPOST';
$modversion['config'][26]['description'] = '_MD_AM_SSLPOSTDSC';
$modversion['config'][26]['formtype'] = 'textbox';
$modversion['config'][26]['valuetype'] = 'text';
$modversion['config'][26]['default'] = "xoops_ssl";
$modversion['config'][26]['category'] = 'general';

$modversion['config'][27]['name'] = 'sslloginlink';
$modversion['config'][27]['title'] = '_MD_AM_SSLLINK';
$modversion['config'][27]['description'] = '_MD_AM_SSLLINKDSC';
$modversion['config'][27]['formtype'] = 'textbox';
$modversion['config'][27]['valuetype'] = 'text';
$modversion['config'][27]['default'] = "https://";
$modversion['config'][27]['category'] = 'general';

$modversion['config'][28]['name'] = 'com_mode';
$modversion['config'][28]['title'] = '_MD_AM_COMMODE';
$modversion['config'][28]['description'] = '_MD_AM_COMMODEDSC';
$modversion['config'][28]['formtype'] = 'select';
$modversion['config'][28]['valuetype'] = 'text';
$modversion['config'][28]['default'] = "nest";
$modversion['config'][28]['category'] = 'general';
$modversion['config'][28]['options'] = array("_NESTED" => "nest", "_FLAT" => "flat", "_THREADED" => "thread");

$modversion['config'][29]['name'] = 'com_order';
$modversion['config'][29]['title'] = '_MD_AM_COMORDER';
$modversion['config'][29]['description'] = '_MD_AM_COMORDERDSC';
$modversion['config'][29]['formtype'] = 'select';
$modversion['config'][29]['valuetype'] = 'int';
$modversion['config'][29]['default'] = "0";
$modversion['config'][29]['category'] = 'general';
$modversion['config'][29]['options'] = array("_OLDESTFIRST" => 0, "_NEWESTFIRST" => 1);

$modversion['config'][30]['name'] = 'enable_badips';
$modversion['config'][30]['title'] = '_MD_AM_DOBADIPS';
$modversion['config'][30]['description'] = '_MD_AM_DOBADIPSDSC';
$modversion['config'][30]['formtype'] = 'yesno';
$modversion['config'][30]['valuetype'] = 'int';
$modversion['config'][30]['default'] = "0";
$modversion['config'][30]['category'] = 'general';

$modversion['config'][31]['name'] = 'bad_ips';
$modversion['config'][31]['title'] = '_MD_AM_BADIPS';
$modversion['config'][31]['description'] = '_MD_AM_BADIPSDSC';
$modversion['config'][31]['formtype'] = 'textarea';
$modversion['config'][31]['valuetype'] = 'array';
$modversion['config'][31]['default'] = array('127.0.0.1');
$modversion['config'][31]['category'] = 'general';

$modversion['config'][32]['name'] = 'module_cache';
$modversion['config'][32]['title'] = '_MD_AM_MODCACHE';
$modversion['config'][32]['description'] = '_MD_AM_MODCACHEDSC';
$modversion['config'][32]['formtype'] = 'module_cache';
$modversion['config'][32]['valuetype'] = 'array';
$modversion['config'][32]['default'] = array();
$modversion['config'][32]['category'] = 'general';

//Meta section
$modversion['config'][33]['name'] = 'meta_keywords';
$modversion['config'][33]['title'] = '_MD_AM_METAKEY';
$modversion['config'][33]['description'] = '_MD_AM_METAKEYDSC';
$modversion['config'][33]['formtype'] = 'textarea';
$modversion['config'][33]['valuetype'] = 'text';
$modversion['config'][33]['default'] = 'news, technology, headlines, xoops, xoop, nuke, myphpnuke, myphp-nuke, phpnuke, SE, geek, geeks, hacker, hackers, linux, software, download, downloads, free, community, mp3, forum, forums, bulletin, board, boards, bbs, php, survey, poll, polls, kernel, comment, comments, portal, odp, open, source, opensource, FreeSoftware, gnu, gpl, license, Unix, *nix, mysql, sql, database, databases, web site, weblog, guru, module, modules, theme, themes, cms, content management';
$modversion['config'][33]['category'] = 'meta';

require_once(XOOPS_ROOT_PATH."/include/version.php");
$modversion['config'][34]['name'] = 'footer';
$modversion['config'][34]['title'] = '_MD_AM_FOOTER';
$modversion['config'][34]['description'] = '_MD_AM_FOOTERDSC';
$modversion['config'][34]['formtype'] = 'textarea';
$modversion['config'][34]['valuetype'] = 'text';
$modversion['config'][34]['default'] = 'Powered by '.XOOPS_VERSION.' &copy; 2001-'.date("Y").' <a href="http://www.xoops.org/" target="_blank">The XOOPS Project</a>';
$modversion['config'][34]['category'] = 'meta';

$modversion['config'][35]['name'] = 'censor_enable';
$modversion['config'][35]['title'] = '_MD_AM_DOCENSOR';
$modversion['config'][35]['description'] = '_MD_AM_DOCENSORDSC';
$modversion['config'][35]['formtype'] = 'yesno';
$modversion['config'][35]['valuetype'] = 'int';
$modversion['config'][35]['default'] = 0;
$modversion['config'][35]['category'] = 'censor';

$modversion['config'][36]['name'] = 'censor_words';
$modversion['config'][36]['title'] = '_MD_AM_CENSORWRD';
$modversion['config'][36]['description'] = '_MD_AM_CENSORWRDDSC';
$modversion['config'][36]['formtype'] = 'textarea';
$modversion['config'][36]['valuetype'] = 'array';
$modversion['config'][36]['default'] = array('fuck', 'shit');
$modversion['config'][36]['category'] = 'censor';

$modversion['config'][37]['name'] = 'censor_replace';
$modversion['config'][37]['title'] = '_MD_AM_CENSORRPLC';
$modversion['config'][37]['description'] = '_MD_AM_CENSORRPLCDSC';
$modversion['config'][37]['formtype'] = 'textbox';
$modversion['config'][37]['valuetype'] = 'text';
$modversion['config'][37]['default'] = "#OOPS#";
$modversion['config'][37]['category'] = 'censor';

$modversion['config'][38]['name'] = 'meta_rating';
$modversion['config'][38]['title'] = '_MD_AM_METARATING';
$modversion['config'][38]['description'] = '_MD_AM_METARATINGDSC';
$modversion['config'][38]['formtype'] = 'select';
$modversion['config'][38]['valuetype'] = 'text';
$modversion['config'][38]['default'] = 'general';
$modversion['config'][38]['category'] = 'meta';
$modversion['config'][38]['options'] = array("_MD_AM_METAOGEN" => "general", "_MD_AM_METAO14YRS" => "14 years", "_MD_AM_METAOREST" => "restricted", "_MD_AM_METAOMAT", "mature");

$modversion['config'][39]['name'] = 'meta_author';
$modversion['config'][39]['title'] = '_MD_AM_METAAUTHOR';
$modversion['config'][39]['description'] = '_MD_AM_METAAUTHORDSC';
$modversion['config'][39]['formtype'] = 'textbox';
$modversion['config'][39]['valuetype'] = 'text';
$modversion['config'][39]['default'] = 'XOOPS';
$modversion['config'][39]['category'] = 'meta';

$modversion['config'][40]['name'] = 'meta_copyright';
$modversion['config'][40]['title'] = '_MD_AM_METACOPYR';
$modversion['config'][40]['description'] = '_MD_AM_METACOPYRDSC';
$modversion['config'][40]['formtype'] = 'textbox';
$modversion['config'][40]['valuetype'] = 'text';
$modversion['config'][40]['default'] = 'Copyright &copy; 2001-'.date("Y");
$modversion['config'][40]['category'] = 'meta';

$modversion['config'][41]['name'] = 'meta_description';
$modversion['config'][41]['title'] = '_MD_AM_METADESC';
$modversion['config'][41]['description'] = '_MD_AM_METADESCDSC';
$modversion['config'][41]['formtype'] = 'textarea';
$modversion['config'][41]['valuetype'] = 'text';
$modversion['config'][41]['default'] = 'XOOPS is a dynamic Object Oriented based open source portal script written in PHP.';
$modversion['config'][41]['category'] = 'meta';

$modversion['config'][42]['name'] = 'meta_robots';
$modversion['config'][42]['title'] = '_MD_AM_METAROBOTS';
$modversion['config'][42]['description'] = '_MD_AM_METAROBOTSDSC';
$modversion['config'][42]['formtype'] = 'select';
$modversion['config'][42]['valuetype'] = 'text';
$modversion['config'][42]['default'] = 'index,follow';
$modversion['config'][42]['category'] = 'meta';
$modversion['config'][42]['options'] = array("_MD_AM_INDEXFOLLOW" => "index,follow", "_MD_AM_NOINDEXFOLLOW" => "noindex,follow", "_MD_AM_INDEXNOFOLLOW" => "index,nofollow", "_MD_AM_NOINDEXNOFOLLOW" => "noindex,nofollow");

$modversion['config'][43]['name'] = 'enable_search';
$modversion['config'][43]['title'] = '_MD_AM_DOSEARCH';
$modversion['config'][43]['description'] = '_MD_AM_DOSEARCHDSC';
$modversion['config'][43]['formtype'] = 'yesno';
$modversion['config'][43]['valuetype'] = 'int';
$modversion['config'][43]['default'] = '1';
$modversion['config'][43]['category'] = 'search';

$modversion['config'][44]['name'] = 'keyword_min';
$modversion['config'][44]['title'] = '_MD_AM_MINSEARCH';
$modversion['config'][44]['description'] = '_MD_AM_MINSEARCHDSC';
$modversion['config'][44]['formtype'] = 'textbox';
$modversion['config'][44]['valuetype'] = 'int';
$modversion['config'][44]['default'] = '5';
$modversion['config'][44]['category'] = 'search';

$modversion['config'][45]['name'] = 'mailmethod';
$modversion['config'][45]['title'] = '_MD_AM_MAILERMETHOD';
$modversion['config'][45]['description'] = '_MD_AM_MAILERMETHODDESC';
$modversion['config'][45]['formtype'] = 'select';
$modversion['config'][45]['valuetype'] = 'text';
$modversion['config'][45]['default'] = 'mail';
$modversion['config'][45]['category'] = 'mail';
$modversion['config'][45]['options'] = array("PHP mail()" => "mail", "sendmail" => "sendmail", "SMTP" => "smtp", "SMTPAuth" => "smtpauth");

$modversion['config'][46]['name'] = 'sendmailpath';
$modversion['config'][46]['title'] = '_MD_AM_SENDMAILPATH';
$modversion['config'][46]['description'] = '_MD_AM_SENDMAILPATHDESC';
$modversion['config'][46]['formtype'] = 'textbox';
$modversion['config'][46]['valuetype'] = 'text';
$modversion['config'][46]['default'] = '/usr/sbin/sendmail';
$modversion['config'][46]['category'] = 'mail';

$modversion['config'][47]['name'] = 'smtphost';
$modversion['config'][47]['title'] = '_MD_AM_SMTPHOST';
$modversion['config'][47]['description'] = '_MD_AM_SMTPHOSTDESC';
$modversion['config'][47]['formtype'] = 'textarea';
$modversion['config'][47]['valuetype'] = 'text';
$modversion['config'][47]['default'] = "";
$modversion['config'][47]['category'] = 'mail';

$modversion['config'][48]['name'] = 'smtpuser';
$modversion['config'][48]['title'] = '_MD_AM_SMTPUSER';
$modversion['config'][48]['description'] = '_MD_AM_SMTPUSERDESC';
$modversion['config'][48]['formtype'] = 'textbox';
$modversion['config'][48]['valuetype'] = 'text';
$modversion['config'][48]['default'] = '';
$modversion['config'][48]['category'] = 'mail';

$modversion['config'][49]['name'] = 'smtppass';
$modversion['config'][49]['title'] = '_MD_AM_SMTPPASS';
$modversion['config'][49]['description'] = '_MD_AM_SMTPPASSDESC';
$modversion['config'][49]['formtype'] = 'password';
$modversion['config'][49]['valuetype'] = 'text';
$modversion['config'][49]['default'] = '';
$modversion['config'][49]['category'] = 'mail';

$modversion['config'][50]['name'] = 'from';
$modversion['config'][50]['title'] = '_MD_AM_MAILFROM';
$modversion['config'][50]['description'] = '_MD_AM_MAILFROMDESC';
$modversion['config'][50]['formtype'] = 'textbox';
$modversion['config'][50]['valuetype'] = 'text';
$modversion['config'][50]['default'] = '';
$modversion['config'][50]['category'] = 'mail';

$modversion['config'][51]['name'] = 'fromname';
$modversion['config'][51]['title'] = '_MD_AM_MAILFROMNAME';
$modversion['config'][51]['description'] = '_MD_AM_MAILFROMNAMEDESC';
$modversion['config'][51]['formtype'] = 'textbox';
$modversion['config'][51]['valuetype'] = 'text';
$modversion['config'][51]['default'] = '';
$modversion['config'][51]['category'] = 'mail';

$modversion['config'][52]['name'] = 'fromuid';
$modversion['config'][52]['title'] = '_MD_AM_MAILFROMUID';
$modversion['config'][52]['description'] = '_MD_AM_MAILFROMUIDDESC';
$modversion['config'][52]['formtype'] = 'user';
$modversion['config'][52]['valuetype'] = 'int';
$modversion['config'][52]['default'] = '1';
$modversion['config'][52]['category'] = 'mail';

$modversion['config'][53]['name'] = 'auth_method';
$modversion['config'][53]['title'] = '_MD_AM_AUTHMETHOD';
$modversion['config'][53]['description'] = '_MD_AM_AUTHMETHODDESC';
$modversion['config'][53]['formtype'] = 'select';
$modversion['config'][53]['valuetype'] = 'text';
$modversion['config'][53]['options'] = array("XOOPS" => "xoops", "LDAP" => "ldap");
$modversion['config'][53]['default'] = "xoops";
$modversion['config'][53]['category'] = 'auth';

$modversion['config'][54]['name'] = 'ldap_mail_attr';
$modversion['config'][54]['title'] = '_MD_AM_LDAP_MAIL_ATTR';
$modversion['config'][54]['description'] = '_MD_AM_LDAP_MAIL_ATTR_DESC';
$modversion['config'][54]['formtype'] = 'textbox';
$modversion['config'][54]['valuetype'] = 'text';
$modversion['config'][54]['default'] = 'mail';
$modversion['config'][54]['category'] = 'auth';

$modversion['config'][55]['name'] = 'ldap_name_attr';
$modversion['config'][55]['title'] = '_MD_AM_LDAP_NAME_ATTR';
$modversion['config'][55]['description'] = '_MD_AM_LDAP_NAME_ATTR_DESC';
$modversion['config'][55]['formtype'] = 'textbox';
$modversion['config'][55]['valuetype'] = 'text';
$modversion['config'][55]['default'] = "cn";
$modversion['config'][55]['category'] = 'auth';

$modversion['config'][56]['name'] = 'ldap_surname_attr';
$modversion['config'][56]['title'] = '_MD_AM_LDAP_SURNAME_ATTR';
$modversion['config'][56]['description'] = '_MD_AM_LDAP_SURNAME_ATTR_DESC';
$modversion['config'][56]['formtype'] = 'textbox';
$modversion['config'][56]['valuetype'] = 'text';
$modversion['config'][56]['default'] = "sn";
$modversion['config'][56]['category'] = 'auth';

$modversion['config'][57]['name'] = 'ldap_givenname_attr';
$modversion['config'][57]['title'] = '_MD_AM_LDAP_GIVENNAME_ATTR';
$modversion['config'][57]['description'] = '_MD_AM_LDAP_GIVENNAME_ATTR_DSC';
$modversion['config'][57]['formtype'] = 'textbox';
$modversion['config'][57]['valuetype'] = 'text';
$modversion['config'][57]['default'] = "givenname";
$modversion['config'][57]['category'] = 'auth';

$modversion['config'][58]['name'] = 'ldap_port';
$modversion['config'][58]['title'] = '_MD_AM_LDAP_PORT';
$modversion['config'][58]['description'] = '_MD_AM_LDAP_PORT';
$modversion['config'][58]['formtype'] = 'textbox';
$modversion['config'][58]['valuetype'] = 'int';
$modversion['config'][58]['default'] = "389";
$modversion['config'][58]['category'] = 'auth';

$modversion['config'][59]['name'] = 'ldap_server';
$modversion['config'][59]['title'] = '_MD_AM_LDAP_SERVER';
$modversion['config'][59]['description'] = '_MD_AM_LDAP_SERVER_DESC';
$modversion['config'][59]['formtype'] = 'textbox';
$modversion['config'][59]['valuetype'] = 'text';
$modversion['config'][59]['default'] = "your directory server";
$modversion['config'][59]['category'] = 'auth';

$modversion['config'][60]['name'] = 'ldap_base_dn';
$modversion['config'][60]['title'] = '_MD_AM_LDAP_BASE_DN';
$modversion['config'][60]['description'] = '_MD_AM_LDAP_BASE_DN_DESC';
$modversion['config'][60]['formtype'] = 'textbox';
$modversion['config'][60]['valuetype'] = 'text';
$modversion['config'][60]['default'] = "ou=Employees,o=Company";
$modversion['config'][60]['category'] = 'auth';

$modversion['config'][61]['name'] = 'ldap_uid_attr';
$modversion['config'][61]['title'] = '_MD_AM_LDAP_UID_ATTR';
$modversion['config'][61]['description'] = '_MD_AM_LDAP_UID_ATTR_DESC';
$modversion['config'][61]['formtype'] = 'textbox';
$modversion['config'][61]['valuetype'] = 'text';
$modversion['config'][61]['default'] = "uid";
$modversion['config'][61]['category'] = 'auth';

$modversion['config'][62]['name'] = 'ldap_uid_asdn';
$modversion['config'][62]['title'] = '_MD_AM_LDAP_UID_ASDN';
$modversion['config'][62]['description'] = '_MD_AM_LDAP_UID_ASDN_DESC';
$modversion['config'][62]['formtype'] = 'yesno';
$modversion['config'][62]['valuetype'] = 'int';
$modversion['config'][62]['default'] = "uid_asdn";
$modversion['config'][62]['category'] = 'auth';

$modversion['config'][63]['name'] = 'ldap_manager_dn';
$modversion['config'][63]['title'] = '_MD_AM_LDAP_MANAGER_DN';
$modversion['config'][63]['description'] = '_MD_AM_LDAP_MANAGER_DN_DESC';
$modversion['config'][63]['formtype'] = 'textbox';
$modversion['config'][63]['valuetype'] = 'text';
$modversion['config'][63]['default'] = "manager_dn";
$modversion['config'][63]['category'] = 'auth';

$modversion['config'][64]['name'] = 'ldap_manager_pass';
$modversion['config'][64]['title'] = '_MD_AM_LDAP_MANAGER_PASS';
$modversion['config'][64]['description'] = '_MD_AM_LDAP_MANAGER_PASS_DESC';
$modversion['config'][64]['formtype'] = 'textbox';
$modversion['config'][64]['valuetype'] = 'text';
$modversion['config'][64]['default'] = "manager_pass";
$modversion['config'][64]['category'] = 'auth';

$modversion['config'][65]['name'] = 'ldap_version';
$modversion['config'][65]['title'] = '_MD_AM_LDAP_VERSION';
$modversion['config'][65]['description'] = '_MD_AM_LDAP_VERSION_DESC';
$modversion['config'][65]['formtype'] = 'textbox';
$modversion['config'][65]['valuetype'] = 'text';
$modversion['config'][65]['default'] = "3";
$modversion['config'][65]['category'] = 'auth';
?>