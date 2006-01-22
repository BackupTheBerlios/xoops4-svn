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

error_reporting (E_ALL);

include_once './passwd.php';
if(INSTALL_USER != '' || INSTALL_PASSWD != ''){
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="XOOPS Installer"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You can not access this XOOPS installer.';
        exit;
    } else {
        if(INSTALL_USER != '' && $_SERVER['PHP_AUTH_USER'] != INSTALL_USER){
            header('HTTP/1.0 401 Unauthorized');
            echo 'You can not access this XOOPS installer.';
            exit;
        }
        if(INSTALL_PASSWD != $_SERVER['PHP_AUTH_PW']){
            header('HTTP/1.0 401 Unauthorized');
            echo 'You can not access this XOOPS installer.';
            exit;
        }
    }
}

include_once './class/textsanitizer.php';
$myts =& TextSanitizer::getInstance();

if ( isset($_POST) ) {
    foreach ($_POST as $k=>$v) {
        if (!is_array($v)) {
            $$k = $myts->stripSlashesGPC($v);
        }
    }
}
$xoopsConfig['language'] = 'english';
if ( !empty($_POST['lang']) ) {
    $xoopsConfig['language'] = $_POST['lang'];
} else {
	if (isset($_COOKIE['install_lang'])) {
		$xoopsConfig['language'] = $_COOKIE['install_lang'];
	} else {
		//$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja,en-us;q=0.7,zh-TW;q=0.6';
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$accept_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$language_array = array('en' => 'english', 'ja' => 'japanese', 'fr' => 'french', 'de' => 'german', 'nl' => 'dutch', 'es' => 'spanish', 'tw' => 'tchinese', 'cn' => 'schinese', 'ro' => 'romanian');
			foreach ($accept_langs as $al) {
				$al = strtolower($al);
				$al_len = strlen($al);
				if ($al_len > 2) {
					if (preg_match("/([a-z]{2});q=[0-9.]+$/", $al, $al_match)) {
						$al = $al_match[1];
					} else {
						continue;
					}
				}
				if (isset($language_array[$al])) {
					$xoopsConfig['language'] = $language_array[$al];
					break;
				}
			}
		}
	}
}

if ( file_exists("./language/".$xoopsConfig['language']."/install.php") ) {
    include_once "./language/".$xoopsConfig['language']."/install.php";
} elseif ( file_exists("./language/english/install.php") ) {
    include_once "./language/english/install.php";
    $xoopsConfig['language'] = 'english';
} else {
    echo 'no language file.';
    exit();
}
setcookie("install_lang", $xoopsConfig['language']);

//include './include/viewerrors.php';
//include './include/functions.php';

define('_OKIMG',"<img src='img/yes.gif' width='6' height='12' border='0' alt='' /> ");
define('_NGIMG',"<img src='img/no.gif' width='6' height='12' border='0' alt='' /> ");

$b_back = '';
$b_reload = '';
$b_next = '';

// options for mainfile.php
$xoopsOption['nocommon'] = true;
define('XOOPS_INSTALL', 1);

if(!empty($_POST['op']))
    $op = $_POST['op'];
elseif(!empty($_GET['op']))
    $op = $_GET['op'];
else
    $op = '';

///// main

switch ($op) {

default:
case "langselect":
    $title = _INSTALL_L0;
	if (!defined('_INSTALL_L128')) {
		define('_INSTALL_L128', 'Choose language to be used for the installation process');
	}
    $content = "<p>"._INSTALL_L128."</p>"
              ."<select name='lang'>";

    $langarr = getDirList("./language/");
    foreach ($langarr as $lang) {
        $content .= "<option value='".$lang."'";
        if (strtolower($lang) == $xoopsConfig['language']) {
            $content .= ' selected="selected"';
        }
        $content .= ">".$lang."</option>";
    }
    $content .= "</select>";

    $b_next = array('start', _INSTALL_L80 );
    include 'install_tpl.php';
    break;

case "start":
    $title = _INSTALL_L0;
    $content = "<table width='80%' align='center'><tr><td align='left'>\n";
    include './language/'.$xoopsConfig['language'].'/welcome.php';
    $content .= "</td></tr></table>\n";
    $b_next = array('modcheck', _INSTALL_L81 );
    include 'install_tpl.php';
    break;

case "modcheck":
    $writeok = array("uploads/", "cache/", "templates_c/", "mainfile.php");
    $title = _INSTALL_L82;
    $content = "<table align='center'><tr><td align='left'>\n";
    $error = false;
    foreach ($writeok as $wok) {
        if (!is_dir("../".$wok)) {
            if ( file_exists("../".$wok) ) {
                @chmod("../".$wok, 0666);
                if (! is_writeable("../".$wok)) {
                    $content .= _NGIMG.sprintf(_INSTALL_L83, $wok)."<br />";
                    $error = true;
                }else{
                    $content .= _OKIMG.sprintf(_INSTALL_L84, $wok)."<br />";
                }
            }
        } else {
            @chmod("../".$wok, 0777);
            if (! is_writeable("../".$wok)) {
                $content .= _NGIMG.sprintf(_INSTALL_L85, $wok)."<br />";
                $error = true;
            }else{
                $content .= _OKIMG.sprintf(_INSTALL_L86, $wok)."<br />";
            }
        }
    }
    $content .= "</td></tr></table>\n";

    if(! $error) {
        $content .= "<p>"._INSTALL_L87."</p>";
        $b_next = array('dbform', _INSTALL_L89 );
    }else{
        $content .= "<p>"._INSTALL_L46."</p>";
        $b_reload = true;
    }

    include 'install_tpl.php';
    break;

case "dbform":
    include_once '../mainfile.php';
    include_once 'class/settingmanager.php';
    $sm = new setting_manager();
    $sm->readConstant();
    $content = $sm->editform();
    $title = _INSTALL_L90;
    $b_next = array('dbconfirm',_INSTALL_L91);
    include 'install_tpl.php';
    break;

case "dbconfirm":
    include_once 'class/settingmanager.php';
    $sm = new setting_manager(true);

    $content = $sm->checkData();
    if (!empty($content)) {
        $content .= $sm->editform();
        $b_next = array('dbconfirm',_INSTALL_L91);
        include 'install_tpl.php';
        break;
    }

    $title = _INSTALL_L53;
    $content = $sm->confirmForm();
    $b_next = array('dbsave',_INSTALL_L92 );
    $b_back = array('', _INSTALL_L93 );
    include 'install_tpl.php';
    break;

case "dbsave":
    include_once "./class/mainfilemanager.php";
    $title = _INSTALL_L88;
    $mm = new mainfile_manager("../mainfile.php");

    $ret = $mm->copyDistFile();
    if(! $ret){
        $content = _INSTALL_L60;
        include 'install_tpl.php';
        exit();
    }

    $mm->setRewrite('XOOPS_ROOT_PATH', trim($myts->stripSlashesGPC($_POST['root_path'])));
    $mm->setRewrite('XOOPS_URL', trim($myts->stripSlashesGPC($_POST['xoops_url'])));
    $mm->setRewrite('XOOPS_DB_TYPE', trim($myts->stripSlashesGPC($_POST['database'])));
    $mm->setRewrite('XOOPS_DB_PREFIX', trim($myts->stripSlashesGPC($_POST['prefix'])));
    $mm->setRewrite('XOOPS_DB_HOST', trim($myts->stripSlashesGPC($_POST['dbhost'])));
    $mm->setRewrite('XOOPS_DB_USER', trim($myts->stripSlashesGPC($_POST['dbuname'])));
    $mm->setRewrite('XOOPS_DB_PASS', trim($myts->stripSlashesGPC($_POST['dbpass'])));
    $mm->setRewrite('XOOPS_DB_NAME', trim($myts->stripSlashesGPC($_POST['dbname'])));
    $mm->setRewrite('XOOPS_DB_PCONNECT', intval($_POST['db_pconnect']));
    $mm->setRewrite('XOOPS_GROUP_ADMIN', 1);
    $mm->setRewrite('XOOPS_GROUP_USERS', 2);
    $mm->setRewrite('XOOPS_GROUP_ANONYMOUS', 3);

	// Check if XOOPS_CHECK_PATH should be initially set or not
	$xoopsPathTrans = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] :  $_SERVER['SCRIPT_FILENAME'];
	if ( DIRECTORY_SEPARATOR != '/' ) {
	 	// IIS6 doubles the \ chars
		$xoopsPathTrans = str_replace( strpos( $xoopsPathTrans, '\\\\', 2 ) ? '\\\\' : DIRECTORY_SEPARATOR, '/', $xoopsPathTrans);
	}
	$mm->setRewrite('XOOPS_CHECK_PATH', strcasecmp( substr($xoopsPathTrans, 0, strlen($_POST['root_path'])), $_POST['root_path']) ? 0 : 1 );

    $ret = $mm->doRewrite();
    if(! $ret){
        $content = _INSTALL_L60;
        include 'install_tpl.php';
        exit();
    }

    $content = $mm->report();
    $content .= "<p>"._INSTALL_L62."</p>\n";
    $b_next = array('mainfile', _INSTALL_L94 );
    include 'install_tpl.php';
    break;

case "mainfile":
    // checking XOOPS_ROOT_PATH and XOOPS_URL
    include_once "../mainfile.php";
    $title = _INSTALL_L94;
    $content = "<table align='center'><tr><td align='left'>\n";

    $detected = str_replace("\\", "/", getcwd()); // "
    $detected = str_replace("/install", "", $detected);
    if ( substr($detected, -1) == "/" ) {
        $detected = substr($detected, 0, -1);
    }

    if (empty($detected)){
        $content .= _NGIMG._INSTALL_L95.'<br />';
    }
    elseif ( XOOPS_ROOT_PATH != $detected ) {
        $content .= _NGIMG.sprintf(_INSTALL_L96,$detected). '<br />';
    }else {
        $content .= _OKIMG._INSTALL_L97.'<br />';
    }

    if(!is_dir(XOOPS_ROOT_PATH)){
        $content .= _NGIMG._INSTALL_L99.'<br />';
    }


    if(preg_match('/^http[s]?:\/\/(.*)[^\/]+$/i',XOOPS_URL)){
        $content .= _OKIMG._INSTALL_L100.'<br />';
    }else{
        $content .= _NGIMG._INSTALL_L101.'<br />';
    }


    $content .= "<br /></td></tr></table>\n";

    $content .= "<table align='center'><tr><td align='left'>\n";
    $content .= _INSTALL_L11."<b>".XOOPS_ROOT_PATH."</b><br />";
    $content .= _INSTALL_L12."<b>".XOOPS_URL."</b><br />";
    $content .= "</td></tr></table>\n";
    $content .= "<p align='center'>"._INSTALL_L13."</p>\n";

    $b_next = array('initial', _INSTALL_L102 );
    $b_back = array('start', _INSTALL_L103 );
    $b_reload = true;

    include 'install_tpl.php';
    //mainfile_settings();
    break;

case "initial":
    // confirm database setting
    include_once "../mainfile.php";
    $content = "<table align=\"center\">\n";
    $content .= "<tr><td align='center'>";
    $content .= "<table align=\"center\">\n";
    $content .= "<tr><td>"._INSTALL_L27."&nbsp;&nbsp;</td><td><b>".XOOPS_DB_HOST."</b></td></tr>\n";
    $content .= "<tr><td>"._INSTALL_L28."&nbsp;&nbsp;</td><td><b>".XOOPS_DB_USER."</b></td></tr>\n";
    $content .= "<tr><td>"._INSTALL_L29."&nbsp;&nbsp;</td><td><b>".XOOPS_DB_NAME."</b></td></tr>\n";
    $content .= "<tr><td>"._INSTALL_L30."&nbsp;&nbsp;</td><td><b>".XOOPS_DB_PREFIX."</b></td></tr>\n";
    $content .= "</table><br />\n";
    $content .= "</td></tr><tr><td align=\"center\">";
    $content .= _INSTALL_L13."<br /><br />\n";
    $content .= "</td></tr></table>\n";
    $b_next = array('checkDB', _INSTALL_L104);
    $b_back = array('start', _INSTALL_L103);
    $b_reload = true;
    $title = _INSTALL_L102;
    include 'install_tpl.php';
    break;

case "checkDB":
    include_once "../mainfile.php";
    include_once './class/dbmanager.php';
    $dbm = new db_manager;
    $title = _INSTALL_L104;
    $content = "<table align='center'><tr><td align='left'>\n";

    if (! $dbm->isConnectable()) {
        $content .= _NGIMG._INSTALL_L106."<br />";
        $content .= "<div style='text-align:center'><br />"._INSTALL_L107;
        $content .= "</div></td></tr></table>\n";
        $b_back = array('start', _INSTALL_L103);
        $b_reload = true;
    }else{
        $content .= _OKIMG._INSTALL_L108."<br />";
        if (! $dbm->dbExists()) {
            $content .= _NGIMG.sprintf(_INSTALL_L109, XOOPS_DB_NAME)."<br />";
            $content .= "</td></tr></table>\n";

            $content .= "<p>"._INSTALL_L21."<br />"
                        ."<b>".XOOPS_DB_NAME."</b></p>"
                        ."<p>"._INSTALL_L22."</p>";

            $b_next = array('createDB', _INSTALL_L105);
            $b_back = array('start', _INSTALL_L103);
            $b_reload = true;
        }else{
            $content .= _OKIMG.sprintf(_INSTALL_L110, XOOPS_DB_NAME)."<br />";
            $content .= "</td></tr></table>\n";
            $content .= "<p>"._INSTALL_L111."</p>";
            $b_next = array('createTables', _INSTALL_L40);
        }
    }

    include 'install_tpl.php';
    break;

case "createDB":
    include_once "../mainfile.php";
    include_once './class/dbmanager.php';
    $dbm = new db_manager;

    if(! $dbm->createDB()){
        $content = "<p>"._INSTALL_L31."</p>";
        $b_next = array('checkDB', _INSTALL_L104);
        $b_back = array('start', _INSTALL_L103);
    }else{
        $content = "<p>".sprintf(_INSTALL_L43, XOOPS_DB_NAME)."</p>";
        $b_next = array('checkDB', _INSTALL_L104);
    }
    include 'install_tpl.php';
    break;

case "createTables":
    include_once "../mainfile.php";

    include_once './class/dbmanager.php';
    $dbm = new db_manager;

    //$content = "<table align='center'><tr><td align='left'>\n";
    $tables = array();
    $result = $dbm->queryFromFile('./sql/'.XOOPS_DB_TYPE.'.structure.sql');
    $content = $dbm->report();

    if(! $result ){
        //$deleted = $dbm->deleteTables($tables);
        $content .= "<p>"._INSTALL_L114."</p>\n";
        $b_back = array('start', _INSTALL_L103);
    }else{
        $content .= "<p>"._INSTALL_L115."</p>\n";
        $b_next = array('siteInit', _INSTALL_L112);
    }

    include 'install_tpl.php';
    break;

case "siteInit":
    include_once "../mainfile.php";

    $content = "<table align='center' width='70%'>\n";
    $content .= "<tr><td colspan='2' align='center'>"._INSTALL_L36."</td></tr>\n";
    $content .= "<tr><td align='right'><b>"._INSTALL_L37."</b></td><td><input type=\"text\" name=\"adminname\" /></td></tr>\n";
    $content .= "<tr><td align='right'><b>"._INSTALL_L167."</b></td><td><input type='text' name='loginname' value='' maxlength='25' /></td></tr>\n";
    $content .= "<tr><td align='right'><b>"._INSTALL_L38."</b></td><td><input type='text' name='adminmail' value='' maxlength='60' /></td></tr>\n";
    $content .= "<tr><td align='right'><b>"._INSTALL_L39."</b></td><td><input type='password' name='adminpass' /></td></tr>\n";
    $content .= "<tr><td align='right'><b>"._INSTALL_L74."</b></td><td><input type='password' name='adminpass2' /></td></tr>\n";
    $content .= "</table>\n";
    $b_next = array('insertData', _INSTALL_L116);

    include 'install_tpl.php';
    break;

case "insertData":
    $adminname = $myts->stripSlashesGPC($_POST['adminname']);
    $loginname = $myts->stripSlashesGPC($_POST['loginname']);
    $adminpass = $myts->stripSlashesGPC($_POST['adminpass']);
    $adminmail = $myts->stripSlashesGPC($_POST['adminmail']);

    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$adminmail)) {
        $content = "<p>"._INSTALL_L73."</p>\n";
        $b_back = array('', _INSTALL_L112 );
        include 'install_tpl.php';
        exit();
    }
    if ( !isset($adminname) || !isset($adminpass) || !isset($adminmail) || $adminmail == "" || $adminname =="" || $adminpass =="" || !isset($loginname) || $loginname == "") {
        $content = "<p>"._INSTALL_L41."</p>\n";
        $b_back = array('', _INSTALL_L112 );
        include 'install_tpl.php';
        exit();
    }
    if ( $adminpass != $adminpass2) {
        $content = "<p>"._INSTALL_L48."</p>\n";
        $b_back = array('', _INSTALL_L112 );
        include 'install_tpl.php';
        exit();
    }
    error_reporting(0);
    include_once "../mainfile.php";
    //include_once './include/createtables2.php';
    include_once './makedata.php';
    include_once './class/dbmanager.php';
    $dbm = new db_manager;
    include_once './class/cachemanager.php';
    $cm = new cache_manager;

    $xoopsConfig['language'] = check_language($xoopsConfig['language']);
    if ( file_exists("./language/".$xoopsConfig['language']."/install2.php") ) {
        include_once "./language/".$xoopsConfig['language']."/install2.php";
    } elseif ( file_exists("./language/english/install2.php") ) {
        include_once "./language/english/install2.php";
        $xoopsConfig['language'] = 'english';
    } else {
        echo 'no language file (install2.php).';
        exit();
    }

    if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
        include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
    } else {
        include_once XOOPS_ROOT_PATH."/language/english/global.php";
    }

    if ( file_exists("../modules/system/language/".$xoopsConfig['language']."/admin/modulesadmin.php") ) {
        include_once "../modules/system/language/".$xoopsConfig['language']."/admin/modulesadmin.php";
    } else {
        include_once "../modules/system/language/english/admin/modulesadmin.php";
    }
    $content = "";

    // #################### Include API ##################
    include_once XOOPS_ROOT_PATH.'/include/functions.php';
    require_once XOOPS_ROOT_PATH.'/kernel/object.php';
    require_once XOOPS_ROOT_PATH.'/class/criteria.php';
    include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";
    require_once XOOPS_ROOT_PATH.'/class/criteria.php';

    $xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
    define("SMARTY_DIR", XOOPS_ROOT_PATH."/class/smarty/");
    define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache/");

    $xoopsConfig['adminmail'] = $adminmail;
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->create();
    $module->setVar('dirname', 'system');
    $html = '<h4 style="text-align:left;margin-bottom: 0px;border-bottom: dashed 1px #000000;">Installing '.$module->getInfo('name').'</h4>';

    if ($module->getInfo('image') != false && trim($module->getInfo('image')) != '') {
        $html .='<img src="'.XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.trim($module->getInfo('image')).'" alt="" />';
    }
    $html .='<br /><b>Version:</b> '.$module->getInfo('version');
    if ($module->getInfo('author') != false && trim($module->getInfo('author')) != '') {
        $html .='<br /><b>Author:</b> '.trim($module->getInfo('author'));
    }
    //make sure that the profilefields.tmp cache file is NOT there
    $profile_handler =& xoops_gethandler('profile');
    $profile_handler->updateCache();

    //$tables = array();
    $result = $dbm->queryFromFile('./sql/'.XOOPS_DB_TYPE.'.data.sql');

    $result = $dbm->queryFromFile('./language/'.$xoopsConfig['language'].'/'.XOOPS_DB_TYPE.'.lang.data.sql');

    $group = make_groups($dbm);
    $result = make_data($dbm, $cm, $adminname, $adminpass, $adminmail, $xoopsConfig['language'], $group);

    //Install system module
    $html .= $module->install(array(XOOPS_GROUP_ADMIN), array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));


	$member_handler =& xoops_gethandler('member');
	$user =& $member_handler->createUser();
	$user->setVar('uid', 1);
	$user->setVar('uname', addslashes($adminname));
	$user->setVar('loginname', addslashes($loginname));
	$user->setVar('email', addslashes($adminmail));
	$user->setVar('pass', md5($adminpass));
	$user->setVar('user_avatar', 'blank.gif');
	$user->setVar('rank', 7);
	$user->setVar('level', 5);
	$user->setVar('user_regdate', time());
	if (!$member_handler->insertUser($user)) {
	    echo $user->getHtmlErrors();
	}

    $block_handler =& xoops_gethandler('block');
    $blocks_to_install =& $block_handler->getObjects(new Criteria('show_func', "(".implode(',', array("'b_system_user_show'", "'b_system_login_show'", "'b_system_main_show'")).")", "IN"));
    //Install login, main menu and user menu blocks
    include_once XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";
    if (count($blocks_to_install) > 0) {
        foreach ($blocks_to_install as $block) {
            if (save_block(0, 0, 1, 0, $block->getVar('name'), $block->getVar('bid'), 0, array("0-0"), array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS), array())) {
                //success
                $html .= "<br />".sprintf(_INSTALL_L165, $block->getVar('name'));
            }
            else {
                //failure
                $html .= "<br />".sprintf(_INSTALL_L166, $block->getVar('name'));
            }
        }
    }
    else {
        $html .= "Blocks not inserted";
    }

    $content .= $dbm->report();
    $content .= $cm->report();
    include_once "./class/mainfilemanager.php";
    $mm = new mainfile_manager("../mainfile.php");
    foreach($group as $key => $val){
        $mm->setRewrite($key, intval($val));
    }
    $result = $mm->doRewrite();
    $content .= $mm->report();

    $content .= $html;

    $b_next = array('modules', _INSTALL_L129);
    $title = _INSTALL_L116;
    setcookie('xoops_session', '', time() - 3600);
    include 'install_tpl.php';

    break;

case 'modules':
    error_reporting(0);
    include_once "../mainfile.php";
    // #################### Include API ##################
    include_once './class/dbmanager.php';
    include_once XOOPS_ROOT_PATH.'/include/functions.php';
    require_once XOOPS_ROOT_PATH.'/kernel/object.php';
    require_once XOOPS_ROOT_PATH.'/class/criteria.php';
    include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";
    require_once XOOPS_ROOT_PATH.'/class/criteria.php';

    $xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
    define("SMARTY_DIR", XOOPS_ROOT_PATH."/class/smarty/");
    define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache/");

    $config_handler =& xoops_gethandler('config');
    $xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

    if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
        include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
    } else {
        include_once XOOPS_ROOT_PATH."/language/english/global.php";
    }

    if ( file_exists("../modules/system/language/".$xoopsConfig['language']."/admin/modulesadmin.php") ) {
        include_once "../modules/system/language/".$xoopsConfig['language']."/admin/modulesadmin.php";
    } else {
        include_once "../modules/system/language/english/admin/modulesadmin.php";
    }

    $module_handler =& xoops_gethandler('module');
    $title = _INSTALL_L49;
    $content = "<table width='80%' align='center'>\n";
    $modules_dir = XOOPS_ROOT_PATH."/modules/";
    $count = 0;
    $modlist = getDirList($modules_dir);
    $pre_specified = array("pm", "profile");
    foreach ($modlist as $file) {
        if ( $file != "system" ) {
            $module =& $module_handler->create();
            $module->loadInfo($file);
            if ($count % 2 == 0) {
                $class = 'even';
            } else {
                $class = 'odd';
            }
            $content .= '<tr class="'.$class.'">
                <td><input type="checkbox" name="modules[]" value="'.$module->getInfo('dirname').'"' ;
            if(in_array($file, $pre_specified)){
            	$content .= ' "checked" ';
            }
            $content .= '/></td>
                <td align="center" valign="bottom"><img src="'.XOOPS_URL.'/modules/'.$module->getInfo('dirname').'/'.$module->getInfo('image').'" alt="'.htmlspecialchars($module->getInfo('name')).'" border="0" /></td>
                <td>'.$module->getInfo('name').'</td>
                <td align="center">'.round($module->getInfo('version'), 2).'</td>';
            $content .= "</td><td>";
            $content .= $module->getInfo("description");
            $content .= '</td></tr>
                ';
            unset($module);
            $count++;
        }

    }
    $content .= "</table>\n";
    $b_next = array('moduleinstall', _INSTALL_L49);
    include 'install_tpl.php';
    break;

case "moduleinstall":
    if (isset($_REQUEST['modules'])) {
        error_reporting(0);
        $content = "";
        include_once "../mainfile.php";
        // #################### Include API ##################
        include_once './class/dbmanager.php';
        include_once XOOPS_ROOT_PATH.'/include/functions.php';
        require_once XOOPS_ROOT_PATH.'/kernel/object.php';
        require_once XOOPS_ROOT_PATH.'/class/criteria.php';
        include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";
        require_once XOOPS_ROOT_PATH.'/class/criteria.php';

        $xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
        define("SMARTY_DIR", XOOPS_ROOT_PATH."/class/smarty/");
        define("XOOPS_CACHE_PATH", XOOPS_ROOT_PATH."/cache/");
        define("XOOPS_UPLOAD_URL", XOOPS_URL."/uploads/");
        define("XOOPS_THEME_PATH", XOOPS_ROOT_PATH."/themes");
        define("XOOPS_COMPILE_PATH", XOOPS_ROOT_PATH."/templates_c");

	    if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php") ) {
	        include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/global.php";
	    } else {
	        include_once XOOPS_ROOT_PATH."/language/english/global.php";
	    }

	    if ( file_exists(XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/local.php") ) {
	        include_once XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/local.php";
	    } else {
	        include_once XOOPS_ROOT_PATH."/language/english/local.php";
	    }

        if ( file_exists("../modules/system/language/".$xoopsConfig['language']."/admin/modulesadmin.php") ) {
            include_once "../modules/system/language/".$xoopsConfig['language']."/admin/modulesadmin.php";
        } else {
            include_once "../modules/system/language/english/admin/modulesadmin.php";
        }

        $module_handler =& xoops_gethandler('module');
        foreach ($_REQUEST['modules'] as $dirname) {
            $module =& $module_handler->create();
            $module->setVar('dirname', $dirname);
            $content .= $module->install(array(XOOPS_GROUP_ADMIN), array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
            unset($module);
        }
        $title = _INSTALL_L49;

        $b_next = array('finish', _INSTALL_L117);
        include 'install_tpl.php';
        break;
    }

    case 'finish':

    $title = _INSTALL_L32;
    $content = "<table width='60%' align='center'><tr><td align='left'>\n";
    include './language/'.$xoopsConfig['language'].'/finish.php';
    $content .= "</td></tr></table>\n";
    include 'install_tpl.php';
    break;
}

/*
 * gets list of name of directories inside a directory
 */
function getDirList($dirname)
{
    $dirlist = array();
    if (is_dir($dirname) && $handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) {
            if ( !preg_match("/^[.]{1,2}$/",$file) ) {
                if (strtolower($file) != 'cvs' && is_dir($dirname.$file) ) {
                    $dirlist[$file]=$file;
                }
            }
        }
        closedir($handle);
        asort($dirlist);
        reset($dirlist);
    }
    return $dirlist;
}

/*
 * gets list of name of files within a directory
 */
function getImageFileList($dirname)
{
    $filelist = array();
    if (is_dir($dirname) && $handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) {
            if (!preg_match("/^[.]{1,2}$/",$file) && preg_match("/[.gif|.jpg|.png]$/i",$file) ) {
                    $filelist[$file]=$file;
            }
        }
        closedir($handle);
        asort($filelist);
        reset($filelist);
    }
    return $filelist;
}

function check_language($language){
     if ( file_exists('../modules/system/language/'.$language.'/modinfo.php') ) {
        return $language;
    } else {
        return 'english';
    }
}
?>