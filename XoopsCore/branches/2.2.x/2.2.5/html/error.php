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
$xoopsOption['template_main'] = "system_error.html";
include 'header.php';
$module_handler =& xoops_gethandler('module');
if (!isset($_REQUEST['m']) || $_REQUEST['m'] == 0) {
    $module =& $module_handler->create(false);
    $module->assignVar('dirname', 'system');
}
else {
    $module =& $module_handler->get($_REQUEST['m']);
}
$error_arr =& $module->loadErrorMessages();
$constant_name = $module->getVar('dirname')."_ERROR";
$errormessage = (isset($_REQUEST['c']) && defined(strtoupper($constant_name).intval($_REQUEST['c']))) ? constant(strtoupper($constant_name).intval($_REQUEST['c'])) : _NOERRORMESSAGE;

$errormessage = str_replace("{SITE_NAME}", $xoopsConfig['sitename'], $errormessage );
$errormessage = str_replace("{SITE_URL}", XOOPS_URL, $errormessage );

$myts =& MyTextSanitizer::getInstance();
$xoopsTpl->assign('errormsg', $myts->displayTarea($errormessage));
include 'footer.php';
?>