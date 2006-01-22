<?php

/**
* Check a user's uname, email, password and password verification
*
* @param object $user {@link XoopsUser} to check
*
* @return string
*/
function userCheck($user)
{
    global $xoopsModuleConfig;
    $stop = '';
    if (!checkEmail($user->getVar('email'))) {
        $stop .= _PROFILE_MA_INVALIDMAIL.'<br />'.print_r($user->getVar('email'));
    }
    foreach ($xoopsModuleConfig['bad_emails'] as $be) {
        if (!empty($be) && preg_match("/".$be."/i", $user->getVar('email'))) {
            $stop .= _PROFILE_MA_INVALIDMAIL.'<br />'.print_r($user->getVar('email'));
            break;
        }
    }
    if (strrpos($user->getVar('email'),' ') > 0) {
        $stop .= _PROFILE_MA_EMAILNOSPACES.'<br />';
    }
    switch ($xoopsModuleConfig['uname_test_level']) {
        case 0:
        // strict
        $restriction = '/[^a-zA-Z0-9\_\-]/';
        break;
        case 1:
        // medium
        $restriction = '/[^a-zA-Z0-9\_\-\<\>\,\.\$\%\#\@\!\\\'\"]/';
        break;
        case 2:
        // loose
        $restriction = '/[\000-\040]/';
        break;
    }
    if ($user->getVar('loginname') == "" || preg_match($restriction, $user->getVar('loginname'))) {
        $stop .= _PROFILE_MA_INVALIDNICKNAME."<br />";
    }
//    if ($user->getVar('name') == "" || preg_match($restriction, $user->getVar('name'))) {
//        $stop .= _PROFILE_MA_INVALIDDISPLAYNAME."<br />";
//    }
    if (strlen($user->getVar('loginname')) > $xoopsModuleConfig['max_uname']) {
        $stop .= sprintf(_PROFILE_MA_NICKNAMETOOLONG, $xoopsModuleConfig['max_uname'])."<br />";
    }
    if (strlen($user->getVar('uname')) > $xoopsModuleConfig['max_uname']) {
        $stop .= sprintf(_PROFILE_MA_DISPLAYNAMETOOLONG, $xoopsModuleConfig['max_uname'])."<br />";
    }
    if (strlen($user->getVar('loginname')) < $xoopsModuleConfig['min_uname']) {
        $stop .= sprintf(_PROFILE_MA_NICKNAMETOOSHORT, $xoopsModuleConfig['min_uname'])."<br />";
    }
    if (strlen($user->getVar('uname')) < $xoopsModuleConfig['min_uname']) {
        $stop .= sprintf(_PROFILE_MA_DISPLAYNAMETOOSHORT, $xoopsModuleConfig['min_uname'])."<br />";
    }
    foreach ($xoopsModuleConfig['bad_unames'] as $bu) {
	    if(empty($bu) ||$user->isAdmin()) continue;
        if (preg_match("/".$bu."/i", $user->getVar('loginname'))) {
            $stop .= _PROFILE_MA_NAMERESERVED."<br />";
            break;
        }
        if (preg_match("/".$bu."/i", $user->getVar('uname'))) {
            $stop .= _PROFILE_MA_DISPLAYNAMERESERVED."<br />";
            break;
        }
    }
    if (strrpos($user->getVar('loginname'), ' ') > 0) {
        $stop .= _PROFILE_MA_NICKNAMENOSPACES."<br />";
    }
//    if (strrpos($user->getVar('name'), ' ') > 0) {
//        $stop .= _PROFILE_MA_DISPLAYNAMENOSPACES."<br />";
//    }
    $member_handler =& xoops_gethandler('member');
    $count_criteria = new Criteria('loginname', $user->getVar('loginname'));
    $display_criteria = new Criteria('uname', $user->getVar('uname'));
    if ($user->getVar('uid') > 0) {
        //existing user, so let's keep the user's own row out of this
        $count_criteria = new CriteriaCompo($count_criteria);
        $display_criteria = new CriteriaCompo($display_criteria);

        $useridcount_criteria = new Criteria('uid', $user->getVar('uid'), '!=');
        $useriddisplay_criteria = new Criteria('uid', $user->getVar('uid'), '!=');

        $count_criteria->add($useridcount_criteria);
        $display_criteria->add($useriddisplay_criteria);
    }
    $count = $member_handler->getUserCount($count_criteria);
    $display_count = $member_handler->getUserCount($display_criteria);
    unset($count_criteria);
    unset($display_criteria);
    if ($count > 0) {
        $stop .= _PROFILE_MA_NICKNAMETAKEN."<br />";
    }
    if ($display_count > 0) {
        $stop .= _PROFILE_MA_DISPLAYNAMETAKEN."<br />";
    }
    $count = 0;
    if ( $user->getVar('email')) {
        $count_criteria = new Criteria('email', $user->getVar('email'));
        if ($user->getVar('uid') > 0) {
            //existing user, so let's keep the user's own row out of this
            $count_criteria = new CriteriaCompo($count_criteria);
            $count_criteria->add(new Criteria('uid', $user->getVar('uid'), '!='));
        }
        $count = $member_handler->getUserCount($count_criteria);
        unset($count_criteria);
        if ( $count > 0 ) {
            $stop .= _PROFILE_MA_EMAILTAKEN."<br />";
        }
    }

    return $stop;
}

/**
* Check password - used when changing password
*
* @param string $uname username of the user changing password
* @param string $oldpass old password
* @param string $newpass new password
* @param string $vpass verification of new password (must be the same as $newpass)
*
* @return string
**/
function checkPassword($uname, $oldpass, $newpass, $vpass) {
    $stop = "";
    $uname = trim($uname);
    $myts = MyTextSanitizer::getInstance();
    if ($oldpass == "") {
        $stop .= _PROFILE_MA_ENTERPWD;
    }
    else {
        //check if $oldpass is correct
        $member_handler =& xoops_gethandler('member');
        if (!$member_handler->loginUser($myts->addSlashes($uname), $myts->addSlashes($oldpass))) {
            $stop .= _PROFILE_MA_WRONGPASSWORD;
        }
    }
    if ( $newpass == '' || !$vpass || $vpass == '' ) {
        $stop .= _PROFILE_MA_ENTERPWD.'<br />';
    }
    global $xoopsModuleConfig;
    if ( ($newpass != $vpass) ) {
        $stop .= _PROFILE_MA_PASSNOTSAME.'<br />';
    } elseif ( ($newpass != '') && (strlen($myts->stripSlashesGPC($newpass)) < $xoopsModuleConfig['minpass']) ) {
        $stop .= sprintf(_PROFILE_MA_PWDTOOSHORT,$xoopsModuleConfig['minpass'])."<br />";
    }
    return $stop;
}
?>