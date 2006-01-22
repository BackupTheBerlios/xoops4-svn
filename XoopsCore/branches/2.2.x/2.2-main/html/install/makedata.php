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

include_once './class/dbmanager.php';

// RMV
// TODO: Shouldn't we insert specific field names??  That way we can use
// the defaults specified in the database...!!!! (and don't have problem
// of missing fields in install file, when add new fields to database)

function make_groups(&$dbm){
    $gruops['XOOPS_GROUP_ADMIN'] = $dbm->insert('groups', " VALUES (0, '".addslashes(_INSTALL_WEBMASTER)."', '".addslashes(_INSTALL_WEBMASTERD)."', 'Admin')");
    $gruops['XOOPS_GROUP_USERS'] = $dbm->insert('groups', " VALUES (0, '".addslashes(_INSTALL_REGUSERS)."', '".addslashes(_INSTALL_REGUSERSD)."', 'User')");
    $gruops['XOOPS_GROUP_ANONYMOUS'] = $dbm->insert('groups', " VALUES (0, '".addslashes(_INSTALL_ANONUSERS)."', '".addslashes(_INSTALL_ANONUSERSD)."', 'Anonymous')");

    if(!$gruops['XOOPS_GROUP_ADMIN'] || !$gruops['XOOPS_GROUP_USERS'] || !$gruops['XOOPS_GROUP_ANONYMOUS']){
        return false;
    }

    return $gruops;
}

function make_data(&$dbm, &$cm, $adminname, $adminpass, $adminmail, $language, $gruops){

    $myts =& textSanitizer::getInstance();
    //$xoopsDB =& Database::getInstance();
    //$dbm = new db_manager;

    $tables = array();

    // data for table 'groups_users_link'

    $dbm->insert('groups_users_link', " VALUES (0, ".$gruops['XOOPS_GROUP_ADMIN'].", 1)");
    $dbm->insert('groups_users_link', " VALUES (0, ".$gruops['XOOPS_GROUP_USERS'].", 1)");

    // data for table 'group_permission'

    $dbm->insert("group_permission", " VALUES (0,".$gruops['XOOPS_GROUP_ADMIN'].",1,1,'module_admin')");
    $dbm->insert("group_permission", " VALUES (0,".$gruops['XOOPS_GROUP_ADMIN'].",1,1, 'module_read')");
    $dbm->insert("group_permission", " VALUES (0,".$gruops['XOOPS_GROUP_USERS'].",1,1,'module_read')");
    $dbm->insert("group_permission", " VALUES (0,".$gruops['XOOPS_GROUP_ANONYMOUS'].",1,1,'module_read')");

	$dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",1,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",2,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",3,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",4,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",5,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",6,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",7,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",8,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",9,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",10,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",11,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",12,1,'system_admin')");
   $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",13,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",14,1,'system_admin')");
    $dbm->insert("group_permission", " VALUES(0,".$gruops['XOOPS_GROUP_ADMIN'].",15,1,'system_admin')");

    // data for table 'banner'

    $dbm->insert("banner", " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date) VALUES (1, 1, 0, 1, 0, '".XOOPS_URL."/images/banners/xoops_banner.gif', 'http://www.xoops.org/', 1008813250)");
    $dbm->insert("banner", " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date) VALUES (2, 1, 0, 1, 0, '".XOOPS_URL."/images/banners/xoops_banner_2.gif', 'http://www.xoops.org/', 1008813250)");
    $dbm->insert("banner", " (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date) VALUES (3, 1, 0, 1, 0, '".XOOPS_URL."/images/banners/banner.swf', 'http://www.xoops.org/', 1008813250)");

    // default theme

    $time = time();
    $dbm->insert('tplset', " VALUES (1, 'default', 'XOOPS Default Template Set', '', ".$time.")");

    // system modules

    if ( file_exists('../modules/system/language/'.$language.'/modinfo.php') ) {
        include '../modules/system/language/'.$language.'/modinfo.php';
    } else {
        include '../modules/system/language/english/modinfo.php';
        $language = 'english';
    }

    $modversion = array();
    include_once '../modules/system/xoops_version.php';
    $time = time();

    // data for table 'users'

    /*
    $temp = md5($adminpass);
    $regdate = time();
    //$dbadminname= addslashes($adminname);
	// RMV-NOTIFY (updated for extra columns in user table)
	$member_handler =& xoops_gethandler('member');
	$user =& $member_handler->createUser();
	$user->setVar('uid', 1);
	$user->setVar('uname', addslashes($adminname));
	$user->setVar('name', addslashes($adminname)); //set real name to username for now
	$user->setVar('email', addslashes($adminmail));
	$user->setVar('user_avatar', 'blank.gif');
	$user->setVar('pass', $temp);
	$user->setVar('rank', 7);
	$user->setVar('level', 5);
	if (!$member_handler->insertUser($user)) {
	    echo $user->getHtmlErrors();
	}
	*/

    return $gruops;
}
?>