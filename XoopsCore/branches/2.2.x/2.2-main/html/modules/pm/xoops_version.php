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

$modversion['name'] = _PM_MI_NAME;
$modversion['version'] = 0.1;
$modversion['description'] = _PM_MI_DESC;
$modversion['author'] = "Jan Pedersen";
$modversion['credits'] = "The XOOPS Project, Wanikoo";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 1;
$modversion['image'] = "images/pm_logo.jpg";
$modversion['dirname'] = "pm";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/admin.php";
$modversion['adminmenu'] = "admin/menu.php";

//$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Table
$modversion['tables'][0] = "priv_msgs";

//install
$modversion['onInstall'] = 'include/install.php';
//update
$modversion['onUpdate'] = 'include/update.php';

// Templates
$modversion['templates'][1]['file'] = 'pm_pmlite.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'pm_readpmsg.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'pm_lookup.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'pm_viewpmsg.html';
$modversion['templates'][4]['description'] = '';

// Menu
$modversion['hasMain'] = 1;

$modversion['config'][]=array(
	'name' => 'perpage',
	'title' => '_PM_MI_PERPAGE',
	'description' => '_PM_MI_PERPAGE_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 20);

$modversion['config'][]=array(
	'name' => 'max_save',
	'title' => '_PM_MI_MAXSAVE',
	'description' => '_PM_MI_MAXSAVE_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 10);

$modversion['config'][]=array(
	'name' => 'prunesubject',
	'title' => '_PM_MI_PRUNESUBJECT',
	'description' => '_PM_MI_PRUNESUBJECT_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => _PM_MI_PRUNESUBJECTDEFAULT);

$modversion['config'][]=array(
	'name' => 'prunemessage',
	'title' => '_PM_MI_PRUNEMESSAGE',
	'description' => '_PM_MI_PRUNEMESSAGE_DESC',
	'formtype' => 'textarea',
	'valuetype' => 'text',
	'default' => _PM_MI_PRUNEMESSAGEDEFAULT);

// User Profile
$modversion['hasProfile'] = 1;

$modversion['profile']['field'][1]['name'] = 'pm_link';
$modversion['profile']['field'][1]['type'] = 'autotext';
$modversion['profile']['field'][1]['valuetype'] = XOBJ_DTYPE_TXTAREA;
$modversion['profile']['field'][1]['default'] = "<a href=\"javascript:openWithSelfMain('{X_URL}/modules/pm/pmlite.php?send2=1&to_userid={X_UID}', 'pmlite', 550, 450);\" title=\""._PM_MI_MESSAGE." {X_UNAME}\"><img src=\"{X_URL}/modules/pm/images/pm.gif\" alt=\""._PM_MI_MESSAGE." {X_UNAME}\" /></a>";
$modversion['profile']['field'][1]['show'] = 1; 
$modversion['profile']['field'][1]['title'] = _PM_MI_LINK_TITLE; 
$modversion['profile']['field'][1]['edit'] = 0;
$modversion['profile']['field'][1]['description'] = _PM_MI_LINK_DESCRIPTION; 
$modversion['profile']['field'][1]['required'] = 0;
$modversion['profile']['field'][1]['config'] = 0;
$modversion['profile']['field'][1]['options'] = array();
?>