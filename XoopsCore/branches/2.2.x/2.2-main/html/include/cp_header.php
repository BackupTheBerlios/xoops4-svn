<?php 
// $Id$
/**
 * module files can include this file for admin authorization
 * the file that will include this file must be located under xoops_url/modules/module_directory_name/admin_directory_name/
 */
error_reporting(0);
include_once '../../../mainfile.php';
if (!$xoopsUser) {
    redirect_header(XOOPS_URL."/user.php", 3, _AD_NORIGHT);
}
// include system category definitions
include_once XOOPS_ROOT_PATH."/modules/system/constants.php";
if (file_exists(XOOPS_ROOT_PATH."/language/" . $xoopsConfig['language'] . "/admin.php")) {
    include_once XOOPS_ROOT_PATH."/language/" . $xoopsConfig['language'] . "/admin.php";
}
else {
    include_once XOOPS_ROOT_PATH."/language/english/admin.php";
}
include_once XOOPS_ROOT_PATH . "/include/cp_functions.php";
?>