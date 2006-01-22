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
include XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";

$op = "list";
if ( isset($_POST) ) {
    foreach ( $_POST as $k => $v ) {
        $$k = $v;
    }
}

if ( isset($_GET['op']) ) {
    if ($_GET['op'] == "edit" || $_GET['op'] == "delete" || $_GET['op'] == "delete_ok" || $_GET['op'] == "new") {
        $op = $_GET['op'];
        $bid = isset($_GET['bid']) ? intval($_GET['bid']) : 0;
    }
}

$_REQUEST["selmod"]=empty($_REQUEST["selmod"])?0:intval($_REQUEST["selmod"]);

if ( $op == "list" ) {
    xoops_cp_header();
    list_blocks();
    xoops_cp_footer();
    exit();
}

if ( $op == "order" ) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }
    if (order_block($id, $weight, $side, $module)) {
        redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"],2,_MD_AM_DBUPDATED);
    }
    redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"], 2, _AM_ERRORDURINGSAVE);
    exit();
}

if ( $op == "save" ) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }
    if (!isset($instanceid)) {
        $instanceid = 0;
    }
    $options = isset($options) ? $options : array();
    $bmodule = isset($bmodule) ? $bmodule : array();
    $groups = isset($groups) ? $groups : array();
    if (save_block($instanceid, $bside, $bvisible, $bweight, $btitle, $bid, $bcachetime, $bmodule, $groups, $options)) {
        redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"], 2, _MD_AM_DBUPDATED);
    }
    redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"], 2, _AM_ERRORDURINGSAVE);
    
    exit();
}

if ( $op == "delete_ok" ) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header("admin.php?fct=blocksadmin&amp;selmod=".$_REQUEST["selmod"], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }
    delete_block_ok($bid);
    exit();
}

if ( $op == "delete" ) {
    xoops_cp_header();
    delete_block($_REQUEST['id']);
    xoops_cp_footer();
    exit();
}

if ( $op == "edit" ) {
    xoops_cp_header();
    edit_block($_REQUEST['id']);
    xoops_cp_footer();
    exit();
}

if ($op == 'new') {
    instantiate_block($_REQUEST['bid']);
}

?>