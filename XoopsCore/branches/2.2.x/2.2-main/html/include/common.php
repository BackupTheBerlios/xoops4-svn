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
if (!defined("XOOPS_MAINFILE_INCLUDED")) {
    exit();
} else {
    //Instantiate security object
    require_once XOOPS_ROOT_PATH."/class/xoopssecurity.php";
    $xoopsSecurity = new XoopsSecurity();
    global $xoopsSecurity;
    //Check super globals
    $xoopsSecurity->checkSuperglobals();

    // ############## Activate error handler ##############
    include_once XOOPS_ROOT_PATH . '/class/errorhandler.php';
    $xoopsErrorHandler =& XoopsErrorHandler::getInstance();
    // Turn on error handler by default (until config value obtained from DB)
    $xoopsErrorHandler->activate(true);

    define("XOOPS_SIDEBLOCK_LEFT",0);
    define("XOOPS_SIDEBLOCK_RIGHT",1);
    define("XOOPS_SIDEBLOCK_BOTH",2);
    define("XOOPS_CENTERBLOCK_LEFT",3);
    define("XOOPS_CENTERBLOCK_RIGHT",4);
    define("XOOPS_CENTERBLOCK_CENTER",5);
    define("XOOPS_CENTERBLOCK_ALL",6);
    define("XOOPS_BLOCK_INVISIBLE",0);
    define("XOOPS_BLOCK_VISIBLE",1);
    define("XOOPS_MATCH_START",0);
    define("XOOPS_MATCH_END",1);
    define("XOOPS_MATCH_EQUAL",2);
    define("XOOPS_MATCH_CONTAIN",3);
    define("SMARTY_DIR", XOOPS_ROOT_PATH."/class/smarty/");
//    define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache");
//    define("XOOPS_UPLOAD_PATH", XOOPS_ROOT_PATH."/uploads");
//    define("XOOPS_THEME_PATH", XOOPS_ROOT_PATH."/themes");
//    define("XOOPS_COMPILE_PATH", XOOPS_ROOT_PATH."/templates_c");
//    define("XOOPS_THEME_URL", XOOPS_URL."/themes");
//    define("XOOPS_UPLOAD_URL", XOOPS_URL."/uploads");
    set_magic_quotes_runtime(0);
    include_once XOOPS_ROOT_PATH.'/class/logger.php';
    $xoopsLogger =& XoopsLogger::instance();
    $xoopsLogger->startTime();
    if (!defined('XOOPS_XMLRPC')) {
        define('XOOPS_DB_CHKREF', 1);
    } else {
        define('XOOPS_DB_CHKREF', 0);
    }

    // ############## Include common functions file ##############
    include_once XOOPS_ROOT_PATH.'/include/functions.php';

    // ################# Include required files ##############
    require_once XOOPS_ROOT_PATH.'/kernel/object.php';
    require_once XOOPS_ROOT_PATH.'/class/criteria.php';

    // #################### Include text sanitizer ##################
    include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";

    /**#@+
     * Host abstraction layer
     */
    if ( !isset($_SERVER['PATH_TRANSLATED']) && isset($_SERVER['SCRIPT_FILENAME']) ) {
        $_SERVER['PATH_TRANSLATED'] =& $_SERVER['SCRIPT_FILENAME'];     // For Apache CGI
    } elseif ( isset($_SERVER['PATH_TRANSLATED']) && !isset($_SERVER['SCRIPT_FILENAME']) ) {
        $_SERVER['SCRIPT_FILENAME'] =& $_SERVER['PATH_TRANSLATED'];     // For IIS/2K now I think :-(
    }

    if ( empty( $_SERVER[ 'REQUEST_URI' ] ) ) {         // Not defined by IIS
        // Under some configs, IIS makes SCRIPT_NAME point to php.exe :-(
        if ( !( $_SERVER[ 'REQUEST_URI' ] = @$_SERVER['PHP_SELF'] ) ) {
            $_SERVER[ 'REQUEST_URI' ] = $_SERVER['SCRIPT_NAME'];
        }
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
        }
    }
    if (!strpos($_SERVER['REQUEST_URI'], ".php")) {
        if (strpos($_SERVER['REQUEST_URI'], "?")) {
            $_SERVER['REQUEST_URI'] = preg_replace("/(\/\?)/U", "/index.php?", $_SERVER['REQUEST_URI']);
        }
        else {
            $_SERVER['REQUEST_URI'] .= "index.php";
        }
    }
    $xoopsRequestUri = $_SERVER[ 'REQUEST_URI' ];       // Deprecated (use the corrected $_SERVER variable now)
    /**#@-*/


    define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache");
    define("XOOPS_UPLOAD_PATH", XOOPS_ROOT_PATH."/uploads");
    define("XOOPS_THEME_PATH", XOOPS_ROOT_PATH."/themes");
    define("XOOPS_COMPILE_PATH", XOOPS_ROOT_PATH."/templates_c");
    define("XOOPS_THEME_URL", XOOPS_URL."/themes");
    define("XOOPS_UPLOAD_URL", XOOPS_URL."/uploads");

    // #################### Connect to DB ##################
    require_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !$xoopsSecurity->checkReferer(XOOPS_DB_CHKREF)) {
        define('XOOPS_DB_PROXY', 1);
    }
    $xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();

	// ################# Load Config Settings ##############
    $config_handler =& xoops_gethandler('config');
    $xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

    // #################### Error reporting settings ##################
    error_reporting(0);

    if (!isset($xoopsConfig['debug_mode']) || in_array(1, $xoopsConfig['debug_mode'])) {
        error_reporting(E_ALL);
    } else {
        // Turn off error handler
        $xoopsErrorHandler->activate(false);
    }

    $xoopsSecurity->checkBadips();

    // ################# Include version info file ##############
    include_once XOOPS_ROOT_PATH."/include/version.php";

//     for older versions...DEPRECATED! use XOOPS_URL and XOOPS_ROOT_PATH instead
//    $xoopsConfig['xoops_url'] = XOOPS_URL;
//    $xoopsConfig['root_path'] = XOOPS_ROOT_PATH."/";


    // #################### Include site-wide lang file ##################
    if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
        include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
    } else {
        include_once XOOPS_ROOT_PATH."/language/english/global.php";
    }

    // ################ Include page-specific lang file ################
    if (isset($xoopsOption['pagetype']) && false === strpos($xoopsOption['pagetype'], '.')) {
        if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/".$xoopsOption['pagetype'].".php") ) {
            include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/".$xoopsOption['pagetype'].".php";
        } else {
            include_once XOOPS_ROOT_PATH."/language/english/".$xoopsOption['pagetype'].".php";
        }
    }

    if ( !defined("XOOPS_USE_MULTIBYTES") ) {
        define("XOOPS_USE_MULTIBYTES",0);
    }

    // #################### Include local string/encoding handler file ##################
    if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/local.php") ) {
        include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/local.php";
    } else {
        include_once XOOPS_ROOT_PATH."/language/english/local.php";
    }

    // ############## Login a user with a valid session ##############
    $xoopsUser = '';
    $xoopsUserIsAdmin = false;
    $member_handler =& xoops_gethandler('member');
    $sess_handler =& xoops_gethandler('session');
    if ($xoopsConfig['use_ssl'] && isset($_POST[$xoopsConfig['sslpost_name']]) && $_POST[$xoopsConfig['sslpost_name']] != '') {
        session_id($_POST[$xoopsConfig['sslpost_name']]);
    } elseif ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
        if (isset($_COOKIE[$xoopsConfig['session_name']])) {
            session_id($_COOKIE[$xoopsConfig['session_name']]);
        } else {
            // no custom session cookie set, destroy session if any
            $_SESSION = array();
            //session_destroy();
        }
        if (function_exists('session_cache_expire')) {
            session_cache_expire($xoopsConfig['session_expire']);
        }
    }
    session_set_save_handler(array(&$sess_handler, 'open'), array(&$sess_handler, 'close'), array(&$sess_handler, 'read'), array(&$sess_handler, 'write'), array(&$sess_handler, 'destroy'), array(&$sess_handler, 'gc'));
    session_start();

    if (!empty($_SESSION['xoopsUserId'])) {
        $xoopsUser =& $member_handler->getUser($_SESSION['xoopsUserId']);
        if (!is_object($xoopsUser)) {
            $xoopsUser = '';
            $_SESSION = array();
        } else {
            if ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
                setcookie($xoopsConfig['session_name'], session_id(), time()+(60*$xoopsConfig['session_expire']), '/',  '', 0);
            }
            $xoopsUser->setGroups($_SESSION['xoopsUserGroups']);
        }
    }

    $accessviolation = false;
    $module_handler =& xoops_gethandler('module');
    $xoopsModule =& $module_handler->loadModule();
    if ($xoopsModule && $xoopsModule->getVar('isactive')) {
        if ($xoopsModule->getVar('dirname') != 'system' && !$xoopsModule->checkAccess()) {
            $accessviolation = true;
        }

        if ($xoopsModule->getVar('hasconfig') == 1 || $xoopsModule->getVar('hascomments') == 1 || $xoopsModule->getVar( 'hasnotification' ) == 1) {
            $xoopsModuleConfig =& $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
        }
        if (!isset($xoopsOption['pagetype'])) {
            $xoopsOption['pagetype'] = 'main';
        }
        $xoopsModule->loadLanguage($xoopsOption['pagetype']);

        $xoopsUserIsAdmin = $xoopsUser ? $xoopsUser->isAdmin($xoopsModule->getVar('mid')) : false;
    }
    // Instantiate Theme object

    require_once XOOPS_ROOT_PATH.'/class/theme.php';
    global $xoopsTpl;
    $xTheme = new XTheme($xoopsConfig['banners'] == 1, in_array(3, $xoopsConfig['debug_mode']));

    //
    if (isset($xoopsConfig) && $xoopsConfig['closesite'] == 1) {
        $allowed = false;
        if (is_object($xoopsUser)) {
            foreach ($xoopsUser->getGroups() as $group) {
                if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
                    $allowed = true;
                    break;
                }
            }
        } elseif (!empty($_POST['xoops_login'])) {
            $xoopsConfig['theme_set'] = "default";
            include_once XOOPS_ROOT_PATH.'/include/checklogin.php';
            exit();
        }
        if (!$allowed) {
            //use default theme for site closed
            $xoopsConfig['theme_set'] = "default";
            $xTheme->tplEngine->assign(array('lang_login' => _LOGIN,
                                    'lang_username' => _USERNAME,
                                    'lang_password' => _PASSWORD,
                                    'lang_siteclosemsg' => $xoopsConfig['closesite_text']));
            $xTheme->tplEngine->xoops_setCaching(1);
            $xoopsOption['template_main'] = "system_siteclosed.html";
            include(XOOPS_ROOT_PATH."/footer.php");
            exit();
        }
        unset($allowed, $group);
    }

    if ($accessviolation != false) {
        if (!$xoopsUser) {
            redirect_header(XOOPS_URL."/user.php", 2, _NOPERM);
        }
        else {
            redirect_header(XOOPS_URL, 2, _NOPERM);
        }
    }
    unset($accessviolation);

    if (!isset($xoopsModule) || !is_object($xoopsModule) || !$xoopsModule->getVar('isactive')) {
        redirect_header(XOOPS_URL, 3, _MODULENOEXIST);
    }
    $xoopsLogger->context = "module";
}
?>