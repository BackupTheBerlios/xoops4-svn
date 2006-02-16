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

include_once "../../mainfile.php";
$xoopsOption['output_type'] = 'plain';
$reply = !empty($_GET['reply']) ? 1 : 0;
$send = !empty($_GET['send']) ? 1 : 0;
$send2 = !empty($_GET['send2']) ? 1 : 0;
$sendmod = !empty($_POST['sendmod']) ? 1 : 0; // send from other modules with post data
$to_userid = !empty($_GET['to_userid']) ? intval($_GET['to_userid']) : 0;
$msg_id = !empty($_GET['msg_id']) ? intval($_GET['msg_id']) : 0;
if ( empty($_GET['refresh'] ) && isset($_POST['op']) && $_POST['op'] != "submit" ) {
    $jump = "pmlite.php?refresh=".time()."";
    if ( $send == 1 ) {
        $jump .= "&amp;send=".$send."";
    } elseif ( $send2 == 1 ) {
        $jump .= "&amp;send2=".$send2."&amp;to_userid=".$to_userid."";
    } elseif ( $reply == 1 ) {
        $jump .= "&amp;reply=".$reply."&amp;msg_id=".$msg_id."";
    } else {
    }
    header('location: '.$jump);
    exit();
}
if ($xoopsUser) {
    $myts =& MyTextSanitizer::getInstance();
    if (isset($_POST['op']) && $_POST['op'] == "submit") {
        $member_handler =& xoops_gethandler('member');
        $count = $member_handler->getUserCount(new Criteria('uid', intval($_POST['to_userid'])));
        if ($count != 1) {
            echo "<br /><br /><div><h4>"._PM_USERNOEXIST."<br />";
            echo _PM_PLZTRYAGAIN."</h4><br />";
            echo "[ <a href='javascript:history.go(-1)'>"._PM_GOBACK."</a> ]</div>";
        } elseif ($GLOBALS['xoopsSecurity']->check()) {
            $pm_handler =& xoops_getmodulehandler('privmessage');
            $pm =& $pm_handler->create();
            $pm->setVar("subject", $_POST['subject']);
            $pm->setVar("msg_text", $_POST['message']);
            $pm->setVar("to_userid", $_POST['to_userid']);
            $pm->setVar("from_userid", $xoopsUser->getVar("uid"));
            if (isset($_REQUEST['savecopy']) && $_REQUEST['savecopy'] == 1) {
                //PMs are by default not saved in outbox
                $pm->setVar('from_delete', 0);
            }
            if (!$pm_handler->insert($pm)) {
                echo $pm->getHtmlErrors();
                echo "<br /><a href='javascript:history.go(-1)'>"._PM_GOBACK."</a>";
            } else {
                // @todo: Send notification email if user has selected this in the profile
                
                echo "<br /><br /><div style='text-align:center;'><h4>"._PM_MESSAGEPOSTED."</h4><br /><a href=\"javascript:window.opener.location='".XOOPS_URL."/viewpmsg.php';window.close();\">"._PM_CLICKHERE."</a><br /><br /><a href=\"javascript:window.close();\">"._PM_ORCLOSEWINDOW."</a></div>";
            }
        }
        else {
            echo implode('<br />', $GLOBALS['xoopsSecurity']->getErrors());
            echo "<br /><a href=\"javascript:window.close();\">"._PM_ORCLOSEWINDOW."</a>";
        }
            
    } elseif ($reply == 1 || $send == 1 || $send2 == 1 || $sendmod =1) {
        if ($reply == 1) {
            $pm_handler =& xoops_gethandler('privmessage');
            $pm =& $pm_handler->get($msg_id);
            if ($pm->getVar("to_userid") == $xoopsUser->getVar('uid')) {
                $pm_uname = XoopsUser::getUnameFromId($pm->getVar("from_userid"));
                $message  = "[quote]\n";
                $message .= sprintf(_PM_USERWROTE,$pm_uname);
                $message .= "\n".$pm->getVar("msg_text", "E")."\n[/quote]";
            } else {
                unset($pm);
                $reply = $send2 = 0;
            }
        }
        
        include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
        $pmform = new XoopsForm('', 'pmform', 'pmlite.php', 'post', true);
        
        if ($reply == 1) {
            $subject = $pm->getVar('subject', 'E');
            if (!preg_match("/^Re:/i",$subject)) {
                $subject = 'Re: '.$subject;
            }
            $xoopsTpl->assign('to_username', $pm_uname);
            $pmform->addElement(new XoopsFormHidden('to_userid', $pm->getVar("from_userid")));
        }
        elseif($sendmod == 1){
            $xoopsTpl->assign('to_username', XoopsUser::getUnameFromId($_POST["to_userid"]));
            $pmform->addElement(new XoopsFormHidden('to_userid', $_POST["to_userid"]));
			$subject = $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['subject']));
			$message = $myts->htmlSpecialChars($myts->stripSlashesGPC($_POST['message']));
        }
        else {
            if ($send2 == 1) {
                $xoopsTpl->assign('to_username', XoopsUser::getUnameFromId($to_userid, false));
                $pmform->addElement(new XoopsFormHidden('to_userid', $to_userid));
            }
            else {
                $to_username = new XoopsFormSelectUser('', 'to_userid');
                $xoopsTpl->assign('to_username', $to_username->render());
            }
            $subject = "";
            $message = "";
        }
        $pmform->addElement(new XoopsFormText('', 'subject', 30, 100, $subject), true);
        $pmform->addElement(new XoopsFormDhtmlTextArea('', 'message', $message, 8, 37), true);
        $pmform->addElement(new XoopsFormRadioYN('', 'savecopy', 0));
        
        $pmform->addElement(new XoopsFormHidden('op', 'submit'));
        $pmform->addElement(new XoopsFormButton('', 'submit', _PM_SUBMIT, 'submit'));
        $pmform->addElement(new XoopsFormButton('', 'reset', _PM_CLEAR, 'reset'));
        
        $cancel_send = new XoopsFormButton('', 'cancel', _PM_CANCELSEND, 'button');
        $cancel_send->setExtra("onclick='javascript:window.close();'");
        $pmform->addElement($cancel_send);
        $pmform->assign($xoopsTpl);
        
        $xoopsOption['template_main'] = "pm_pmlite.html";
    }
} else {
    echo _PM_SORRY."<br /><br /><a href='".XOOPS_URL."/register.php'>"._PM_REGISTERNOW."</a>.";
}

include(XOOPS_ROOT_PATH."/footer.php");
?>