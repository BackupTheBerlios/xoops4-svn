<?php
// $id: Exp $ //
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

$xTheme->loadModuleAdminMenu(2);

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : "form";
$pmhandler =& xoops_getmodulehandler('privmessage');
switch ($op) {
    default:
    case "form":
    $form =& $pmhandler->getPruneForm();
    $form->display();
    break;
    
    case "prune":
    $criteria = new CriteriaCompo();
    if ($_REQUEST['after']['date'] && $_REQUEST['after']['date'] != "YYYY/MM/DD") {
        $criteria->add(new Criteria('msg_time', strtotime($_REQUEST['after']['date']) + intval($_REQUEST['after']['time']), ">"));
    }
    if ($_REQUEST['before']['date'] && $_REQUEST['before']['date'] != "YYYY/MM/DD") {
        $criteria->add(new Criteria('msg_time', strtotime($_REQUEST['before']['date']) + intval($_REQUEST['before']['time']), "<"));
    }
    if (isset($_REQUEST['onlyread']) && $_REQUEST['onlyread'] == 1) {
        $criteria->add(new Criteria('read_msg', 1));
    }
    if ((!isset($_REQUEST['includesave']) || $_REQUEST['includesave'] == 0)) {
        $savecriteria = new CriteriaCompo(new Criteria('to_save', 0));
        $savecriteria->add(new Criteria('from_save', 0));
        $criteria->add($savecriteria);
    }
    if (isset($_REQUEST['notifyusers']) && $_REQUEST['notifyusers'] == 1) {
        $notifycriteria = $criteria;
        $notifycriteria->add(new Criteria('to_delete', 0));
        $notifycriteria->setGroupBy('to_userid');
        // Get array of uid => number of deleted messages
        $uids = $pmhandler->getCount($notifycriteria);
    }
    $deletedrows = $pmhandler->deleteAll($criteria);
    if ($deletedrows === false) {
        redirect_header('prune.php', 2, _PM_AM_ERRORWHILEPRUNING);
    }
    if (isset($_REQUEST['notifyusers']) && $_REQUEST['notifyusers'] == 1) {
        $errors = false;
        foreach ($uids as $uid => $messagecount) {
            $pm = $pmhandler->create();
            
            $pm->setVar("subject", $xoopsModuleConfig['prunesubject']);
            $pm->setVar("msg_text", str_replace('{PM_COUNT}', $messagecount, $xoopsModuleConfig['prunemessage']));
            $pm->setVar("to_userid", $uid);
            $pm->setVar("from_userid", $xoopsUser->getVar("uid"));
            
            if (!$pmhandler->insert($pm)) {
                $errors = true;
                $errormsg[] = $pm->getHtmlErrors();
            }
            unset($pm);
        }
        if ($errors == true) {
            echo implode('<br />', $errormsg);
            xoops_cp_footer();
            exit();
        }
    }
    redirect_header('admin.php', 2, sprintf(_PM_AM_MESSAGESPRUNED, $deletedrows));
    break;
}
xoops_cp_footer();
?>