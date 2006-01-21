<?php
//
// Startup item providing the "site closed" functionality
//
// IMO this is messy and shouldn't be done like that but at least it works
//
// Better way in case somebody would want to do it:
// - Make the closesite and closesite_okgrp new kernel properties (overridable by modifying hosts.php)
// - End the request with a 503 (service unavailable) when somebody wants to access a closed site
// - Let the ErrorDocuments module display the appropriate page
//
	global $xoopsConfig, $xoopsUser;

    if ($xoopsConfig['closesite'] == 1) {
        $allowed = false;
        if (is_object($xoopsUser)) {
            foreach ($xoopsUser->getGroups() as $group) {
                if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
                    $allowed = true;
                    break;
                }
            }
        } elseif (!empty($_POST['xoops_login'])) {
            include_once XOOPS_ROOT_PATH.'/include/checklogin.php';
            exit();
        }
        if (!$allowed) {
            include_once XOOPS_ROOT_PATH.'/class/template.php';
            $xoopsTpl = new XoopsTpl();
            $xoopsTpl->assign(array('sitename' => $xoopsConfig['sitename'], 'xoops_themecss' => xoops_getcss(), 'xoops_imageurl' => XOOPS_THEME_URL.'/'.$xoopsConfig['theme_set'].'/', 'lang_login' => _LOGIN, 'lang_username' => _USERNAME, 'lang_password' => _PASSWORD, 'lang_siteclosemsg' => $xoopsConfig['closesite_text']));
            $xoopsTpl->xoops_setCaching(1);
            $xoopsTpl->display('db:system_siteclosed.html');
            exit();
        }
        unset($allowed, $group);
    }

	return true;

?>