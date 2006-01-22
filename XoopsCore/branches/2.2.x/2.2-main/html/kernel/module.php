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

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * A Module
 *
 * @package		kernel
 *
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsModule extends XoopsObject
{
    /**
     * @var string
     */
    var $modinfo;
    /**
     * @var string
     */
    var $adminmenu;

    /**
    * @var array
    */
    var $_msg;

    /**
     * Constructor
     */
    function XoopsModule()
    {
        $this->XoopsObject();
        $this->initVar('mid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        $this->initVar('version', XOBJ_DTYPE_INT, 100, false);
        $this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('isactive', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dirname', XOBJ_DTYPE_OTHER, null, true);
        $this->initVar('hasmain', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasadmin', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hassearch', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasconfig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hascomments', XOBJ_DTYPE_INT, 0, false);
        // RMV-NOTIFY
        $this->initVar('hasnotification', XOBJ_DTYPE_INT, 0, false);
        // Dynamic user profiles - not necessary?
        //$this->initVar('hasprofile', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Load module info
     *
     * @param   string  $dirname    Directory Name
     * @param   boolean $verbose
     **/
    function loadInfoAsVar($dirname, $verbose = true)
    {
        if ( !isset($this->modinfo) ) {
            $this->loadInfo($dirname, $verbose);
        }
        $version = intval(100 * ($this->modinfo['version']+0.001));
        $this->setVar('name', $this->modinfo['name'], true);
        $this->setVar('version', $version);
        $this->setVar('dirname', $this->modinfo['dirname'], true);
        $hasmain = (isset($this->modinfo['hasMain']) && $this->modinfo['hasMain'] == 1) ? 1 : 0;
        $hasadmin = (isset($this->modinfo['hasAdmin']) && $this->modinfo['hasAdmin'] == 1) ? 1 : 0;
        $hassearch = (isset($this->modinfo['hasSearch']) && $this->modinfo['hasSearch'] == 1) ? 1 : 0;
        $hasconfig = ((isset($this->modinfo['config']) && is_array($this->modinfo['config'])) || !empty($this->modinfo['hasComments'])) ? 1 : 0;
        $hascomments = (isset($this->modinfo['hasComments']) && $this->modinfo['hasComments'] == 1) ? 1 : 0;
        // RMV-NOTIFY
        $hasnotification = (isset($this->modinfo['hasNotification']) && $this->modinfo['hasNotification'] == 1) ? 1 : 0;
        //$hasprofile = (isset($this->modinfo['hasProfile']) && $this->modinfo['hasProfile'] == 1) ? 1 : 0;
        $this->setVar('hasmain', $hasmain);
        $this->setVar('hasadmin', $hasadmin);
        $this->setVar('hassearch', $hassearch);
        $this->setVar('hasconfig', $hasconfig);
        $this->setVar('hascomments', $hascomments);
        // RMV-NOTIFY
        $this->setVar('hasnotification', $hasnotification);
    }

    /**
     * add a message
     *
     * @param string $str message to add
     * @access public
     */
    function setMessage($str)
    {
        $this->_msg[] = trim($str);
    }

    /**
     * return the messages for this object as an array
     *
     * @return array an array of messages
     * @access public
     */
    function getMessages()
    {
        return $this->_msg;
    }

    /**
     * Set module info
     *
     * @param   string  $name
     * @param   mix  	$value
     * @return  bool
     **/
    function &setInfo($name, $value)
    {
        if(empty($name)) {
	        $this->modinfo = $value;
        }else{
	        $this->modinfo[$name] = $value;
        }
        return true;
    }

    /**
     * Get module info
     *
     * @param   string  $name
     * @return  array|string	Array of module information.
	 * 			If {@link $name} is set, returns a singel module information item as string.
     **/
    function &getInfo($name=null)
    {
        if ( !isset($this->modinfo) ) {
            $this->loadInfo($this->getVar('dirname'));
        }
        if ( !empty($name) ) {
            if ( isset($this->modinfo[$name]) ) {
                return $this->modinfo[$name];
            }
            $return = false;
            return $return;
        }
        return $this->modinfo;
    }

    /**
     * Get a link to the modules main page
     *
     * @return	string  FALSE on fail
     */
    function mainLink()
    {
        if ( $this->getVar('hasmain') == 1 ) {
            $ret = '<a href="'.XOOPS_URL.'/modules/'.$this->getVar('dirname').'/">'.$this->getVar('name').'</a>';
            return $ret;
        }
        return false;
    }

    /**
     * Get links to the subpages
     *
     * @return	string
     */
    function subLink()
    {
        $ret = array();
        if ( $this->getInfo('sub') && is_array($this->getInfo('sub')) ) {
            foreach ( $this->getInfo('sub') as $submenu ) {
                $ret[] = array('name' => $submenu['name'], 'url' => $submenu['url']);
            }
        }
        return $ret;
    }

    /**
     * Load the admin menu for the module
     */
    function loadAdminMenu() {
    	$adminmenu = array();
    	list( $dirname, $menufile ) = array( $this->getVar('dirname'), $this->getInfo('adminmenu') );
        if ( !empty( $menufile ) && file_exists( XOOPS_ROOT_PATH . "/modules/$dirname/$menufile" ) ) {
            include( XOOPS_ROOT_PATH . "/modules/$dirname/$menufile" );
        }
		$this->adminmenu = $adminmenu;
    }

    /**
     * Get the admin menu for the module
     *
     * @return	string
     */
    function &getAdminMenu()
    {
        if ( !isset($this->adminmenu) ) {
            $this->loadAdminMenu();
        }
        return $this->adminmenu;
    }

    /**
     * Load the module info for this module
     *
     * @param	string  $dirname    Module directory
     * @param	bool    $verbose    Give an error on fail?
     */
    function loadInfo($dirname, $verbose = true) {
        global $xoopsConfig;
        xoops_load_lang_file( 'modinfo', $dirname );
        if (file_exists(XOOPS_ROOT_PATH.'/modules/'.$dirname.'/xoops_version.php')) {
            include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/xoops_version.php';
        } else {
            if (false != $verbose) {
                echo "Module File for $dirname Not Found!";
            }
            return;
        }
        $this->modinfo = $modversion;
        unset($modversion);
    }

    /**
     * Search contents within a module
     *
     * @param   string  $term
     * @param   string  $andor  'AND' or 'OR'
     * @param   integer $limit
     * @param   integer $offset
     * @param   integer $userid
     * @return  mixed   Search result.
     **/
    function search($term = '', $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        if ($this->getVar('hassearch') != 1) {
            return false;
        }
        $search =& $this->getInfo('search');
        if ($this->getVar('hassearch') != 1 || !isset($search['file']) || !isset($search['func']) || $search['func'] == '' || $search['file'] == '') {
            return false;
        }
        if (file_exists(XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname').'/'.$search['file'])) {
            include_once XOOPS_ROOT_PATH.'/modules/'.$this->getVar('dirname').'/'.$search['file'];
        } else {
            return false;
        }
        if (function_exists($search['func'])) {
            $func = $search['func'];
            return $func($term, $andor, $limit, $offset, $userid);
        }
        return false;
    }

    /**
    * check user's access to the module
    *
    * @return bool
    */
    function checkAccess() {
        global $xoopsUser, $xoopsOption;
        $groupperm_handler =& xoops_gethandler('groupperm');
        $groups = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        if (file_exists('./xoops_version.php')) {
            $right = 'module_read';
        }
        elseif (file_exists('../xoops_version.php')) {
            $xoopsOption['pagetype'] = "admin";
            $right = 'module_admin';
        }
        else {
            return true;
        }
        return $groupperm_handler->checkRight($right, $this->getVar( 'mid' ), $groups );
    }

    /**
    * load language strings in a module
    *
    * @param string $type can be "main", "admin", "blocks" or any other filename located in the module's language folder
    *
    * @return void
    */
    function loadLanguage($type = "main") {
        global $xoopsConfig;
        if ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/".$xoopsConfig['language']."/".$type.".php") ) {
            include_once XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/".$xoopsConfig['language']."/".$type.".php";
        } else {
            if ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/english/".$type.".php") ) {
                include_once XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/english/".$type.".php";
            }
        }
    }

    /**
    * Load error messages
    *
    * @return void
    **/
    function loadErrorMessages() {
        global $xoopsConfig;
        if ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/".$xoopsConfig['language']."/error.php") ) {
            include_once XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/".$xoopsConfig['language']."/error.php";
        } else {
            if ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/english/error.php") ) {
                include_once XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/language/english/error.php";
            }
        }
    }

    /**#@+
    * For backward compatibility only!
    * @deprecated
    */
    function mid()
    {
        return $this->getVar('mid');
    }
    function dirname()
    {
        return $this->getVar('dirname');
    }
    function name()
    {
        return $this->getVar('name');
    }
    function getByDirName($dirname)
    {
        $modhandler =& xoops_gethandler('module');
        return $modhandler->getByDirname($dirname);
    }
    /**#@-*/

    /**
    * Get the currently accessed page
    *
    * @return array
    */
    function getCurrentPage() {
        global $xoopsConfig, $xoopsOption;

        $relative_url = XOOPS_URL;
        if (preg_match('!^http://!i', $relative_url)) {
            $uri_parts = parse_url($relative_url);
            $relative_url = empty($uri_parts['path'])?"":$uri_parts['path'];
        }

        if ($xoopsOption['pagetype'] == "admin") {
            if (strpos($_SERVER['REQUEST_URI'], $relative_url."/admin.php")=== 0) {
                //on admin frontpage
                $moduleid = 0;
                $pageid = 2;
            }
            else {
                //no blocks
                return array();
            }
        }
        elseif ($this->getvar('dirname') != "system") {
            if (preg_match("/[^\?]*index\.php$/i", $_SERVER['REQUEST_URI']) && $xoopsConfig['startpage'] == $this->getVar('dirname')) {
                //on top page
                $moduleid = 0;
                $pageid = 1;
            }
            else {
                //on module page
                $moduleid = $this->getVar('mid');
                $pageid = 0;

                $pages = $this->getInfo('pages');
                if ($pages == false) {
                    $pages = $this->getInfo('sub');
                }
                if (is_array($pages) && $pages != array()) {
                    foreach ($pages as $id => $pageinfo) {
                        if (preg_match("/[^\?]*".preg_quote($pageinfo['url'], '/')."/iU", $_SERVER['REQUEST_URI'])) {
                            $pageid = $id;
                            break;
                        }
                    }
                }
            }
        } else {
            if (!empty($xoopsOption['show_cblock'])) {
                //on top page
                $moduleid = 0;
                $pageid = 1;
            }
            else {
                //"all pages"-blocks only
                $moduleid = 0;
                $pageid = 0;
            }
        }
        return array("mid" => $moduleid, "page" => $pageid);
    }

    /**
    * Installs the module
    *
    * @param array $admingroups array of group ids to grant module admin privileges after successful installation
    * @param array $accessgroups array of group ids to grant module access privileges after successful installation
    *
    * @return string
    */
    function install($admingroups = array(), $accessgroups = array()) {
        $this->loadInfoAsVar($this->dirname());
        $this->setVar('weight', 1);

        if (!$this->executeScript('onInstall', 1)) {
            echo "<p>".sprintf(_MD_AM_FAILINS, "<b>".$this->getVar('name')."</b>")."</p>";
            foreach ($this->getMessages() as $msg) {
                echo '<code>'.$msg.'</code><br />';
            }
            return;
        }

        $error = $this->executeSQL();
        // if no error, save the module info and blocks info associated with it
        if ($error == false) {
            $this->insert();
            // execute module specific install script if any
            $this->executeScript('onInstall');
            //@todo: What if it fails? Roll back installation?

            $this->insertGroupPermissions($admingroups, 'admin');
            $this->insertGroupPermissions($accessgroups, 'access');

            $ret = '<p><code>';
            foreach ($this->getMessages() as $m) {
                $ret .= $m.'<br />';
            }
            $ret .= '</code><br />'.sprintf(_MD_AM_OKINS, "<b>".$this->getVar('name')."</b>").'</p>';
            return $ret;
        } else {
            $ret = '<p>';
	            $ret .= '<br />'.sprintf(_MD_AM_FAILINS, '<b>'.$this->getVar('dirname').'</b>').'&nbsp;'._MD_AM_ERRORSC;
            foreach ($this->getErrors() as $er) {
	                $ret .= '<br />&nbsp;&nbsp;'.$er;
            }
	            $ret .= '</p>';
            return $ret;
        }
    }

    /**
    * Updates the module
    *
    * @return void
    */
    function update() {
        // Save current version for use in the update function
        $prev_version = $this->getVar('version');
        include_once XOOPS_ROOT_PATH.'/class/template.php';
        xoops_template_clear_module_cache($this->getVar('mid'));

        // we dont want to change the module name set by admin
        $temp_name = $this->getVar('name');
        $this->loadInfoAsVar($this->getVar('dirname'));
        $this->setVar('name', $temp_name);

        // we also don't want to update version until after update process is successfully finished
        $new_version = $this->getVar('version');
        $this->setVar('version', $prev_version);

        if (!$this->executeScript('onUpdate', 1)) {
            echo "<p>".sprintf(_MD_AM_FAILUPD, "<b>".$this->getVar('name')."</b>")."</p>";
            foreach ($this->getMessages() as $msg) {
                echo '<code>'.$msg.'</code><br />';
            }
            return;
        }

        echo $this->insert();

        $this->executeScript('onUpdate');
        //@todo: what if it fails? Roll back update?

        //re-set version to new version while keeping custom name
        $this->setVar('version', $new_version);
        $module_handler =& xoops_gethandler('module');
        $module_handler->insert($this);
        foreach ($this->getMessages() as $msg) {
            echo '<code>'.$msg.'</code><br />';
        }
        echo "<p>".sprintf(_MD_AM_OKUPD, "<b>".$this->getVar('name')."</b>")."</p>";
    }

    /**
    * Updates or inserts the module's data, templates, blocks, configuration categories and items and profile fields
    *
    * @return string
    */
    function insert() {
        $this->setVar('last_update', time());
        $module_handler =& xoops_gethandler('module');
        if (!$module_handler->insert($this)) {
            $this->setErrors('Could not insert <b>'.$this->getVar('name').'</b> to database.');
            $ret = "<p>".sprintf(_MD_AM_FAILINS, "<b>".$this->name()."</b>")."&nbsp;"._MD_AM_ERRORSC."<br />";
            foreach ( $this->getErrors() as $err ) {
                $ret .= " - ".$err."<br />";
            }
            $ret .= "</p>";
            return $ret;
        }

        $this->setMessage('Module data inserted successfully. Module ID: <b>'.$this->getVar('mid').'</b>');
        $this->insertTemplates();

        include_once XOOPS_ROOT_PATH.'/class/template.php';
        xoops_template_clear_module_cache($this->getVar('mid'));

        $this->insertBlocks();

        $this->insertConfigCategories();
        $this->insertConfig();

        $this->insertProfileFields();
        $ret = "";
        foreach ($this->getMessages() as $msg) {
            $ret .= '<code>'.$msg.'</code><br />';
        }
        return $ret;
    }

    /**
    * Execute module's SQL file - if any
    *
    * @return bool
    */
    function executeSQL() {
        $error = false;
        $sqlfile =& $this->getInfo('sqlfile');
        if ($sqlfile != false && is_array($sqlfile)) {
            $reservedTables = array('avatar', 'avatar_users_link', 'block_module_link', 'xoopscomments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups','groups_users_link','group_permission', 'online', 'bannerclient', 'banner', 'bannerfinish', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'modules', 'tplfile', 'tplset', 'tplsource', 'xoopsnotifications', 'banner', 'bannerclient', 'bannerfinish');
            $sql_file_path = XOOPS_ROOT_PATH."/modules/".$this->getVar('dirname')."/".$sqlfile[XOOPS_DB_TYPE];
            if (!file_exists($sql_file_path)) {
                $this->setErrors("SQL file not found at <b>$sql_file_path</b>");
                $error = true;
            } else {
                $this->setMessage("SQL file found at <b>$sql_file_path</b>.<br  /> Creating tables...");
                include_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';
                $sql_query = fread(fopen($sql_file_path, 'r'), filesize($sql_file_path));
                $sql_query = trim($sql_query);
                SqlUtility::splitMySqlFile($pieces, $sql_query);
                $created_tables = array();
                foreach ($pieces as $piece) {
                    // [0] contains the prefixed query
                    // [4] contains unprefixed table name
                    $prefixed_query = SqlUtility::prefixQuery($piece, $GLOBALS['xoopsDB']->prefix());
                    if (!$prefixed_query) {
                        $this->setErrors("<b>$piece</b> is not a valid SQL!");
                        $error = true;
                        break;
                    }
                    // check if the table name is reserved
                    if (!in_array($prefixed_query[4], $reservedTables)) {
                        // not reserved, so try to create one
                        if (!$GLOBALS['xoopsDB']->query($prefixed_query[0])) {
                            $this->setErrors($GLOBALS['xoopsDB']->error());
                            $error = true;
                            break;
                        } else {

                            if (!in_array($prefixed_query[4], $created_tables)) {
                                $this->setMessage('&nbsp;&nbsp;Table <b>'.$GLOBALS['xoopsDB']->prefix($prefixed_query[4]).'</b> created.');
                                $created_tables[] = $prefixed_query[4];
                            } else {
                                $this->setMessage('&nbsp;&nbsp;Data inserted to table <b>'.$GLOBALS['xoopsDB']->prefix($prefixed_query[4]).'</b>.');
                            }
                        }
                    } else {
                        // the table name is reserved, so halt the installation
                        $this->setErrors('<b>'.$prefixed_query[4]."</b> is a reserved table!");
                        $error = true;
                        break;
                    }
                }
                // if there was an error, delete the tables created so far, so the next installation will not fail
                if ($error == true) {
                    foreach ($created_tables as $ct) {
                        //echo $ct;
                        $GLOBALS['xoopsDB']->query("DROP TABLE ".$GLOBALS['xoopsDB']->prefix($ct));
                    }
                }
            }
        }
        return $error;
    }

    /**
    * Inserts templates into the database
    *
    * @return void
    */
    function insertTemplates() {
        $tplfile_handler =& xoops_gethandler('tplfile');

        // Delete existing templates from this module in the default template set
        $deltpl =& $tplfile_handler->find('default', 'module', $this->getVar('mid'));
        $delng = array();
        $existing_templates = array();

        if (is_array($deltpl)) {
            global $xoopsTpl;
            if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
                include_once XOOPS_ROOT_PATH."/class/template.php";
                $xoopsTpl = new XoopsTpl();
            }

            // clear cache files
            $xoopsTpl->clear_cache(null, 'mod_'.$this->getVar('dirname'));

            // delete template file entry in db
            $dcount = count($deltpl);
            for ($i = 0; $i < $dcount; $i++) {
                $existing_templates[] = $deltpl[$i];
                if (!$tplfile_handler->delete($deltpl[$i])) {
                    $delng[] = $deltpl[$i]->getVar('tpl_file');
                }
            }
        }

        // Insert new templates
        $templates = $this->getInfo('templates');
        if ($templates != false) {
            global $xoopsConfig;
            $this->setMessage('Updating templates...');
            foreach ($templates as $tpl) {
                $tpl['file'] = trim($tpl['file']);
                if (!in_array($tpl['file'], $delng)) {
                    $new_templates[] = $tpl['file'];
                    $tpldata =& $this->gettemplate($tpl['file']);
                    $tplfile =& $tplfile_handler->create();
                    $tplfile->setVar('tpl_refid', $this->getVar('mid'));
                    $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_lastmodified', time());
                    if (preg_match("/\.css$/i", $tpl['file'])) {
                        $tplfile->setVar('tpl_type', 'css');
                    } else {
                        $tplfile->setVar('tpl_type', 'module');
                    }
                    $tplfile->setVar('tpl_source', $tpldata, true);
                    $tplfile->setVar('tpl_module', $this->getVar('dirname'));
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $tpl['file'], true);
                    $tplfile->setVar('tpl_desc', $tpl['description'], true);
                    if (!$tplfile_handler->insert($tplfile)) {
                        $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert template <b>'.$tpl['file'].'</b> to the database.</span>');
                    } else {
                        $newid = $tplfile->getVar('tpl_id');
                        $this->setMessage('&nbsp;&nbsp;Template <b>'.$tpl['file'].'</b> inserted to the database.');
                        if (isset($xoopsConfig) && isset($xoopsConfig['template_set']) && $xoopsConfig['template_set'] == 'default') {
                            if (!xoops_template_touch($tplfile)) {
                                $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not recompile template <b>'.$tpl['file'].'</b>.</span>');
                            } else {
                                $this->setMessage('&nbsp;&nbsp;Template <b>'.$tpl['file'].'</b> recompiled.</span>');
                            }
                        }
                    }
                    unset($tpldata);
                } else {
                    $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete old template <b>'.$tpl['file'].'</b>. Aborting update of this file.</span>');
                }
            }
            //delete templates that are removed from the module
            foreach (array_keys($existing_templates) as $i) {
                if (!in_array($existing_templates[$i]->getVar('tpl_file'), $new_templates)) {
                    if ($tplfile_handler->delete($existing_templates[$i])) {
                        $this->setMessage('&nbsp;&nbsp;Template <b>'.$existing_templates[$i]->getVar('tpl_file').'</b> no longer used and removed');
                    }
                    else{
                        $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete removed template <b>'.$existing_templates[$i]->getVar('tpl_file').'</b></span>');
                    }
                }
            }
        }
    }

    /**
    * Return the contents of a template file
    *
    * @param string $template name of template file
    * @param bool $block whether the template is a block template - defaults to false = module template
    *
    * @return string
    */
    function gettemplate($template, $block = false) {
        global $xoopsConfig;
        if ($block) {
            $path = XOOPS_ROOT_PATH.'/modules/'.$this->getVar('dirname').'/templates/blocks/'.$template;
        } else {
            $path = XOOPS_ROOT_PATH.'/modules/'.$this->getVar('dirname').'/templates/'.$template;
        }
        if (!file_exists($path)) {
            return false;
        } else {
            $lines = file($path);
        }
        if (!$lines) {
            return false;
        }
        $ret = '';
        $count = count($lines);
        for ($i = 0; $i < $count; $i++) {
            $ret .= str_replace("\n", "\r\n", str_replace("\r\n", "\n", $lines[$i]));
        }
        return $ret;
    }

    /**
    * Insert blocks from module into the database
    *
    * @return void
    */
    function insertBlocks() {
        global $xoopsConfig, $xoopsUser;
        $block_handler =& xoops_gethandler('block');
        $blocks = $this->getInfo('blocks');
        $this->setMessage('Building blocks...');
        if ($blocks != false) {
            $count = count($blocks);
            $showfuncs = array();
            $funcfiles = array();
            foreach (array_keys($blocks) as $i ) {
                if (isset($blocks[$i]['show_func']) && $blocks[$i]['show_func'] != '' && isset($blocks[$i]['file']) && $blocks[$i]['file'] != '') {
                    $editfunc = isset($blocks[$i]['edit_func']) ? $blocks[$i]['edit_func'] : '';
                    $showfuncs[] = $blocks[$i]['show_func'];
                    $funcfiles[] = $blocks[$i]['file'];
                    $template = '';
                    if ((isset($blocks[$i]['template']) && trim($blocks[$i]['template']) != '')) {
                        $content =& $this->gettemplate($blocks[$i]['template'], true);
                    }
                    if (!isset($content) || !$content) {
                        $content = '';
                    } else {
                        $template = $blocks[$i]['template'];
                    }
                    $options = '';
                    if (!empty($blocks[$i]['options'])) {
                        $options = $blocks[$i]['options'];
                    }

                    $myts =& MyTextSanitizer::getInstance();

                    $criteria = new CriteriaCompo(new Criteria('mid', $this->getVar('mid')));
                    $criteria->add(new Criteria('show_func', $myts->addSlashes($blocks[$i]['show_func'])));
                    $criteria->add(new Criteria('func_file', $myts->addSlashes($blocks[$i]['file'])));
                    if ((isset($blocks[$i]['template']) && trim($blocks[$i]['template']) != '')) {
                        $criteria->add(new Criteria('template', $myts->addSlashes($blocks[$i]['template'])));
                    }
                    $block = $block_handler->getObjects($criteria);
                    if (isset($block[0])) {
                        $block = $block[0];
                        $this->setMessage("Updating existing block");
                    }
                    else {
                        $this->setMessage("Creating new block");
                        $block =& $block_handler->create();
                        $block->setVar('mid', $this->getVar('mid'));
                        $block->setVar('dirname', $this->getVar('dirname'));
                        $block->setVar('show_func', $blocks[$i]['show_func'], true);
                        $block->setVar('func_file', $blocks[$i]['file'], true);
                        $block->setVar('isactive', 1);
                    }

                    $block->setVar('name', $blocks[$i]['name'], true);
                    $block->setVar('edit_func', $editfunc, true);
                    $block->setVar('options', explode('|', $options), true);
                    $block->setVar('template', $template, true);
                    $block->setVar('last_modified', time());

                    if (!$block_handler->insert($block)) {
                        $this->setMessage('&nbsp;&nbsp;ERROR: Could not insert '.$blocks[$i]['name']);
                    } else {
                        $this->setMessage('&nbsp;&nbsp;Block <b>'.$blocks[$i]['name'].'</b> inserted. Block ID: <b>'.$block->getVar('bid').'</b>');
                        if ($template != '') {
                            //Update or insert template
                            $tplfile_handler =& xoops_gethandler('tplfile');
                            $tplfile =& $tplfile_handler->find('default', 'block', $block->getVar('bid'));
                            if (count($tplfile) == 0) {
                                $tplfile_new =& $tplfile_handler->create();
                                $tplfile_new->setVar('tpl_module', $this->getVar('dirname'));
                                $tplfile_new->setVar('tpl_refid', $block->getVar('bid'));
                                $tplfile_new->setVar('tpl_tplset', 'default');
                                $tplfile_new->setVar('tpl_file', $template, true);
                                $tplfile_new->setVar('tpl_type', 'block');
                            }
                            else {
                                $tplfile_new = $tplfile[0];
                            }
                            $tplfile_new->setVar('tpl_source', $content, true);
                            $tplfile_new->setVar('tpl_desc', $blocks[$i]['description'], true);
                            $tplfile_new->setVar('tpl_lastmodified', time());
                            $tplfile_new->setVar('tpl_lastimported', 0);
                            if (!$tplfile_handler->insert($tplfile_new)) {
                                $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert template <b>'.$blocks[$i]['template'].'</b>.</span>');
                            } else {
                                $this->setMessage('&nbsp;&nbsp;Template <b>'.$blocks[$i]['template'].'</b> inserted.');
                                if (isset($xoopsConfig['template_set']) && $xoopsConfig['template_set'] == 'default') {
                                    if (!xoops_template_touch($tplfile_new)) {
                                        $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not compile template <b>'.$blocks[$i]['template'].'</b>.</span>');
                                    } else {
                                        $this->setMessage('&nbsp;&nbsp;Template <b>'.$blocks[$i]['template'].'</b> compiled.');
                                    }
                                }

                            }
                        }
                    }
                    $blockids[] = $block->getVar('bid');
                }
            }
        }
        $block_arr = $block_handler->getByModule($this->getVar('mid'));
        if (count($block_arr) > 0) {
            foreach ($block_arr as $block) {
                if (!in_array($block->getVar('bid'), $blockids) ) {
                    if (!$block_handler->delete($block)) {
                        $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete block <b>'.$block->getVar('name').'</b>. Block ID: <b>'.$block->getVar('bid').'</b><br />'.implode('<br />', $block->getErrors()).'</span>');
                    } else {
                        $this->setMessage('&nbsp;&nbsp;Block <b>'.$block->getVar('name').' deleted. Block ID: <b>'.$block->getVar('bid').'</b>');
                    }
                }
            }
        }
    }

    /**
    * Insert configuration categories
    *
    * @return void
    */
    function insertConfigCategories() {
        //If configuration items exist
        if ($this->getVar('hascomments') != 0 || $this->getVar('hasnotification') != 0 || $this->getInfo('config') != false) {
            $configcat_handler =& xoops_gethandler('configcategory');
            $oldcats =& $configcat_handler->getCatByModule($this->getVar('mid'));

            $cats = $this->getInfo('configcat');

            if ($cats != false && count($cats) > 0) {
                foreach (array_keys($cats) as $i) {
                    $newcats[$cats[$i]['nameid']] = $cats[$i];
                    $newcats[$cats[$i]['nameid']]['order'] = $i;
                }
            }
            if ($this->getVar('hascomments') != 0 || $this->getVar('hasnotification') != 0 || $cats == false) {
                //make default category - should only be if there are config items without category
                //it is assumed that if people use configuration categories, they will use configuration categories for ALL items
                $newcats["xoops_default"] = array('nameid' => 'xoops_default',
                'name' => '_MD_AM_MODULEPREF',
                'description' => '',
                'order' => 0);
            }

            $old_nameids = array();
            $configcatcount = count($oldcats);
            $configcat_delng = array();
            if ($configcatcount > 0) {
                $this->setMessage('Updating module config category data...');
                foreach (array_keys($oldcats) as $i) {
                    $nameid = $oldcats[$i]->getVar('confcat_nameid');
                    if (!in_array($nameid, array_keys($newcats))) {
                        if (!$configcat_handler->delete($oldcats[$i])) {
                            $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete config category data from the database. Config category ID: <b>'.$oldcats[$i]->getvar('confcat_id').'</b></span>');
                        } else {
                            $this->setMessage('&nbsp;&nbsp;Config category data deleted from the database. Config category ID: <b>'.$oldcats[$i]->getVar('confcat_id').'</b>');
                        }
                    }
                    else {
                        if ($newcats[$nameid]['name'] != $oldcats[$i]->getVar('confcat_name') || $newcats[$nameid]['description'] != $oldcats[$i]->getVar('confcat_description') || $newcats[$nameid]['order'] != $oldcats[$i]->getVar('confcat_order')) {
                            $oldid = $oldcats[$i]->getVar('confcat_id');
                            $oldcats[$i]->setVar('confcat_name', $newcats[$nameid]['name']);
                            $oldcats[$i]->setVar('confcat_order', $newcats[$nameid]['order']);
                            $oldcats[$i]->setVar('confcat_description', $newcats[$nameid]['description']);
                            if (!$configcat_handler->insert($oldcats[$i])) {
                                $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not update config category data. Config category ID: <b>'.$oldcats[$i]->getvar('confcat_id').'</b></span>');
                            } else {
                                //update category ID
                                $catcriteria =  new CriteriaCompo(new Criteria('confcat_id', $oldid));
                                $catcriteria->add(new Criteria('confcat_modid', $this->getVar('mid')));
                                if ($configcat_handler->updateAll('confcat_id', $newcats[$nameid]['order'], $catcriteria)) {
                                    $this->setMessage('&nbsp;&nbsp;Config category data updated. Config category ID: <b>'.$oldcats[$i]->getVar('confcat_id').'</b>');
                                }
                                else {
                                    $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not update config category ID. Config Category: <b>'.$nameid.'</b></span>');
                                }
                            }
                        }
                    }
                    $old_nameids[] = $nameid;
                }
            }
            else {

            }
            // now insert the new ones
            if ($newcats != false) {
                $this->setMessage('Adding module config category data...');
                foreach ($newcats as $nameid => $configcat) {
                    // only insert ones that do not exist
                    if (!in_array($nameid, $old_nameids)) {
                        $confcatobj =& $configcat_handler->create();
                        $confcatobj->setVar('confcat_id', $configcat['order']); //set order as ID, so it can be used with a getConfigsByCat() call
                        $confcatobj->setVar('confcat_modid', $this->getVar('mid'));
                        $confcatobj->setVar('confcat_name', $configcat['name']);
                        $confcatobj->setVar('confcat_nameid', $configcat['nameid']);
                        $confcatobj->setVar('confcat_description', $configcat['description']);
                        $confcatobj->setVar('confcat_order', $configcat['order']);
                        $confcatop_msgs = '';
                        if (false != $configcat_handler->insert($confcatobj)) {
                            $this->setMessage('&nbsp;&nbsp;Config category <b>'.$configcat['nameid'].'</b> added to the database.');
                        } else {
                            $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert config category <b>'.$configcat['nameid'].'</b> to the database.</span>');
                        }
                        unset($confcatobj);
                    }
                }
                unset($newcats);
            }
        }
    }

    /**
    * Insert configuration items
    *
    * @return void
    */
    function insertConfig() {
        $configs = $this->getInfo('config');
        if ($configs == false) {
            $configs = array();
        }
        if ($this->getVar('hascomments') != 0) {
            include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
            array_push($configs, array('name' => 'com_rule',
            'title' => '_CM_COMRULES',
            'description' => '', 'formtype' =>
            'select', 'valuetype' => 'int',
            'default' => 1,
            'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE,
            '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL,
            '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER,
            '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)));
            array_push($configs, array('name' => 'com_anonpost',
            'title' => '_CM_COMANONPOST',
            'description' => '',
            'formtype' => 'yesno',
            'valuetype' => 'int',
            'default' => 0));
        }
        // RMV-NOTIFY
        if ($this->getVar('hasnotification') != 0) {
            if (empty($configs)) {
                $configs = array();
            }
            // Main notification options
            include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
            include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
            $options = array();
            $options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
            $options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
            $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
            $options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;

            $configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLE', 'description' => '_NOT_CONFIG_ENABLEDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => XOOPS_NOTIFICATION_ENABLEBOTH, 'options' => $options);
            // Event-specific notification options
            // FIXME: doesn't work when update module... can't read back the array of options properly...  " changing to &quot;
            $options = array();
            $categories = notificationCategoryInfo('',$this->getVar('mid'));
            foreach ($categories as $category) {
                $events = notificationEvents ($category['name'], false, $this->getVar('mid'));
                foreach ($events as $event) {
                    if (!empty($event['invisible'])) {
                        continue;
                    }
                    $option_name = $category['title'] . ' : ' . $event['title'];
                    $option_value = $category['name'] . '-' . $event['name'];
                    $options[$option_name] = $option_value;
                }
            }
            $configs[] = array ('name' => 'notification_events',
            'title' => '_NOT_CONFIG_EVENTS',
            'description' => '_NOT_CONFIG_EVENTSDSC',
            'formtype' => 'select_multi',
            'valuetype' => 'array',
            'default' => array_values($options),
            'options' => $options);
        }

        if ($configs != false) {
            $confcat_handler =& xoops_gethandler('configcategory');
            /* @var $confcat_handler XoopsConfigCategoryHandler */

            $confcats = $confcat_handler->getCatByModule($this->getVar('mid'));
            if (count($confcats) > 0) {
                foreach (array_keys($confcats) as $i) {
                    //get categories by confcat_nameid
                    $cats[$confcats[$i]->getVar('confcat_nameid')] = $confcats[$i]->getVar('confcat_id');
                }
            }
            $this->setMessage('Adding module config data...');
            $config_handler =& xoops_gethandler('config');
            $configcriteria = new Criteria('conf_modid', $this->getVar('mid'));
            $old_configs =& $config_handler->getConfigs($configcriteria, false, true);
            unset($configcriteria);

            foreach ($configs as $config) {
	            $configs_name[] = $config["name"];
            }

            foreach (array_keys($old_configs) as $i) {
	            if(!in_array($old_configs[$i]->getVar('conf_name'), $configs_name)){
		            $config_handler->deleteConfig($old_configs[$i]);
		            continue;
	            }
                $conf_arr[$old_configs[$i]->getVar('conf_name')] =& $old_configs[$i];
            }

            $order = 0;
            foreach ($configs as $config) {
                if (isset($conf_arr[$config['name']])) {
                    $confobj =& $conf_arr[$config['name']];
                }
                else {
                    $confobj =& $config_handler->createConfig();
                }
                $confcat_nameid = isset($config['category']) ? $config['category'] : 'xoops_default';
                $confobj->setVar('conf_modid', $this->getVar('mid'));
                $confobj->setVar('conf_catid', $cats[$confcat_nameid]);
                $confobj->setVar('conf_name', $config['name']);
                $confobj->setVar('conf_title', $config['title'], true);
                $confobj->setVar('conf_desc', $config['description'], true);
                $confobj->setVar('conf_formtype', $config['formtype']);
                $confobj->setVar('conf_valuetype', $config['valuetype']);
                //$confobj->setVar('conf_value', $config['default'], true);
                $confobj->setVar('conf_order', $order);
                if ($confobj->isNew()) {
                    // Only set configuration value to default on new configs
                    $confobj->setConfValueForInput($config['default'], true);
                }
                $confop_msgs = '';
                if (!isset($config['options']) || !is_array($config['options'])) {
	                $config['options'] = array();
                }
                    $options =& $confobj->getConfOptions();
                    if (count($options) > 0) {
                        foreach (array_keys($options) as $i) {
                            $existing_options[$options[$i]->getVar('confop_name')] = $options[$i]->getVar('confop_value');
                        }
                        $newoptions = xoops_array_diff_assoc($config['options'], $existing_options);
                        $removedoptions = xoops_array_diff_assoc($existing_options, $config['options']);
                        unset($existing_options);
                    }
                    else {
                        $newoptions = $config['options'];
                        $removedoptions = array();
                    }
                    //clear config options from config items
                    $confobj->clearConfOptions();
                    if (count($newoptions) > 0) {
                        foreach ($newoptions as $key => $value) {
                            $confop =& $config_handler->createConfigOption();
                            $confop->setVar('confop_name', $key, true);
                            $confop->setVar('confop_value', $value, true);
                            $confobj->setConfOptions($confop);
                            $confop_msgs .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;Config option added. Name: <b>'.$key.'</b> Value: <b>'.$value.'</b>';
                            unset($confop);
                        }
                    }
                    if (count($removedoptions) > 0) {
                        foreach ($removedoptions as $key => $value) {
                            $deletecriteria = new CriteriaCompo(new Criteria('confop_name', $key));
                            $deletecriteria->add(new Criteria('confop_value', $value));
                            $deletecriteria->add(new Criteria('conf_id', $confobj->getVar('conf_id')));
                            $config_handler->deleteConfigOption($deletecriteria);
                            unset($deletecriteria);
                            $confop_msgs .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;Config option removed. Name: <b>'.$key.'</b> Value: <b>'.$value.'</b>';
                        }
                    }
                $order++;
                if ($config_handler->insertConfig($confobj) != false) {
                    $this->setMessage('&nbsp;&nbsp;Config <b>'.$config['name'].'</b> added to the database.'.$confop_msgs);
                } else {
                    $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert config <b>'.$config['name'].'</b> to the database.</span>');
                }
                unset($confobj);
            }
            unset($configs);
        }
    }

    /**
    * Insert or update profile fields
    *
    * @return void
    */
    function insertProfileFields() {
        // Add profile fields
        if ($this->getInfo('hasProfile') != 0) {
            $this->setMessage('Adding user profile fields...');
            $profile_handler =& xoops_gethandler('profile');
            $profile_info = $this->getInfo('profile');
            $fields = isset($profile_info['field']) ? $profile_info['field'] : array();

            $existing_fields = $profile_handler->loadFields();
            $existing_fields_arr = array();
            if (count($existing_fields) > 0) {
                foreach (array_keys($existing_fields) as $i) {
                    if ($existing_fields[$i]->getVar('field_moduleid') == $this->getVar('mid')) {
                        $existing_fields_arr[$existing_fields[$i]->getVar('field_name')] = $existing_fields[$i];
                    }
                }
            }

            $fieldnames = array();
            if (count($fields) > 0) {
                foreach (array_keys($fields) as $i) {
                    if (!isset($fields[$i]['name']) || !isset($fields[$i]['valuetype'])) {
                        $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Malformed profile field <b>Name or Valuetype unspecified #'.$i.'</b></span>');
                        continue;
                    }
                    $fieldnames[] = $fields[$i]['name'];
                    // Only add new fields - don't overwrite existing to avoid overwriting customisations
                    if (!in_array($fields[$i]['name'], array_keys($existing_fields_arr))) {
                        $this->setMessage($profile_handler->saveField($fields[$i], $this->getVar('mid'), $i));
                    }
                    else {
                        $this->setMessage('&nbsp;&nbsp;NOTICE: <b>'.$fields[$i]['name'].' exists - skipped</b>');
                    }
                }
            }

//            if (count($existing_fields) > 0) {
//                $removed_fields = array_diff(array_keys($existing_fields_arr), $fieldnames);
//                if (count($removed_fields) > 0) {
//                    foreach ($removed_fields as $fieldname) {
//                        if ($profile_handler->deleteField($existing_fields_arr[$fieldname])) {
//                            $this->setMessage('&nbsp;&nbsp;Field <b>'.$existing_fields_arr[$fieldname]->getVar('field_name').'</b> removed from the database');
//                        }
//                        else {
//                            $this->setMessage('&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not remove field <b>'.$existing_fields_arr[$fieldname]->getVar('field_name').'</b> from the database. '.implode(' ', $existing_fields_arr[$fieldname]->getErrors()).'</span>');
//                        }
//                    }
//                }
//            }

            unset($profile_handler);
            unset($profile_info);
            unset($fields);
            unset($fieldnames);
            unset($existing_fields);
            unset($existing_fields_arr);
        }
        else {
            echo "No Profile fields found";
        }
    }

    /**
    * Execute module script
    *
    * @param string $type Type of script to run - onUpdate, onInstall or onUninstall
    * @param int $state 1 = pre-process script, 2 = post-process script
    *
    * @return bool
    */
    function executeScript($type, $state = 2) {
        $state = $state == 1 ? "pre_" : "";
        // execute module specific script if any
        $script = $this->getInfo($type);
        if (false != $script && trim($script) != '') {
            switch ($type) {
                case "onInstall":
                $functype = "install";
                break;

                default:
                case "onUpdate":
                $functype = "update";
                break;

                case "onUninstall":
                $functype = "uninstall";
                break;
            }

            include_once XOOPS_ROOT_PATH.'/modules/'.$this->getVar('dirname').'/'.trim($script);
            if (function_exists('xoops_module_'.$state.$functype.'_'.$this->getVar('dirname'))) {
                $func = 'xoops_module_'.$state.$functype.'_'.$this->getVar('dirname');
                if (!$func($this, $this->getVar('version'))) { //need to send version number to onUpdate function
                    $this->setMessage('Failed to execute '.$func.'<br />'.implode('<br />', $this->getErrors()));
                    return false;
                } else {
                    $this->setMessage('<b>'.$func.'</b> executed successfully.');
                }
            }
        }
        return true;
    }

    /**
    * @param array $groups array of group ids to add permission to
    * @param string $type either "admin" or "access" for which permission to grant
    *
    * @return bool
    */
    function insertGroupPermissions($groups, $type) {
        if (count($groups) > 0) {
            $ret = true;
            $gperm_name = $type == "admin" ? "module_admin" : "module_read";
            $groupperm_handler =& xoops_gethandler('groupperm');
            foreach ($groups as $groupid) {
                if (!$groupperm_handler->addRight($gperm_name, $this->getVar('mid'), $groupid)) {
                    $ret = false;
                }
            }
            return $ret;
        }
        return false;
    }
}


/**
 * XOOPS module handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS module class objects.
 *
 * @package		kernel
 *
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsModuleHandler extends XoopsPersistableObjectHandler
{
    /**
	 * holds an array of cached module references, indexed by module id
	 *
	 * @var    array
	 * @access private
	 */
    var $_cachedModule_mid = array();

    /**
	 * holds an array of cached module references, indexed by module dirname
	 *
	 * @var    array
	 * @access private
	 */
    var $_cachedModule_dirname = array();

    function XoopsModuleHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'modules', 'XoopsModule', 'mid', 'name');
    }

    /**
     * Load a module from the database
     *
     * @param	int     $id     ID of the module
     *
     * @return	object  FALSE on fail
     */
    function &get($id)
    {
        static $_cachedModule_dirname;
        static $_cachedModule_mid;
        $id = intval($id);
        if ($id > 0) {
            if (!empty($_cachedModule_mid[$id])) {
                return $_cachedModule_mid[$id];
            } else {
                $sql = 'SELECT * FROM '.$this->db->prefix('modules').' WHERE mid = '.$id;
                if (!$result = $this->db->query($sql)) {
                    return false;
                }
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                    $module = new XoopsModule();
                    $myrow = $this->db->fetchArray($result);
                    $module->assignVars($myrow);
                    $_cachedModule_mid[$id] =& $module;
                    $_cachedModule_dirname[$module->getVar('dirname')] =& $module;
                    return $module;
                }
            }
        }
        return false;
    }

    /**
     * Load a module by its dirname
     *
     * @param	string  $dirname
     *
     * @return	object  FALSE on fail
     */
    function &getByDirname($dirname)
    {
        static $_cachedModule_mid;
        static $_cachedModule_dirname;
        if (!empty($_cachedModule_dirname[$dirname])) {
            return $_cachedModule_dirname[$dirname];
        } else {
            $sql = "SELECT * FROM ".$this->db->prefix('modules')." WHERE dirname = '".trim($dirname)."'";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $module =& new XoopsModule();
                $myrow = $this->db->fetchArray($result);
                $module->assignVars($myrow);
                $_cachedModule_dirname[$dirname] =& $module;
                $_cachedModule_mid[$module->getVar('mid')] =& $module;
                return $module;
            }
            return false;
        }
    }

    /**
     * Write a module to the database
     *
     * @param   object  &$module reference to a {@link XoopsModule}
     * @return  bool
     **/
    function insert(&$module)
    {
        if (!parent::insert($module)) {
            return false;
        }
        if (!empty($this->_cachedModule_dirname[$module->getVar('dirname')])) {
            unset ($this->_cachedModule_dirname[$module->getVar('dirname')]);
        }
        if (!empty($this->_cachedModule_mid[$module->getVar('mid')])) {
            unset ($this->_cachedModule_mid[$module->getVar('mid')]);
        }
        return true;
    }

    /**
     * Delete a module from the database
     *
     * @param   object  &$module
     * @return  bool
     **/
    function delete(&$module)
    {
        if (strtolower(get_class($module)) != 'xoopsmodule') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE mid = %u", $this->db->prefix('modules'), $module->getVar('mid'));
        if ( !$result = $this->db->query($sql) ) {
            return false;
        }
        // delete admin permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_admin' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);
        // delete read permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_read' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);

        $sql = sprintf("SELECT block_id FROM %s WHERE module_id = %u", $this->db->prefix('block_module_link'), $module->getVar('mid'));
        if ($result = $this->db->query($sql)) {
            $block_id_arr = array();
            while ($myrow = $this->db->fetchArray($result))
            {
                array_push($block_id_arr, $myrow['block_id']);
            }
        }
        // loop through block_id_arr
        if (isset($block_id_arr)) {
            foreach ($block_id_arr as $i) {
                $sql = sprintf("SELECT block_id FROM %s WHERE module_id != %u AND block_id = %u", $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                if ($result2 = $this->db->query($sql)) {
                    if (0 < $this->db->getRowsNum($result2)) {
                        // this block has other entries, so delete the entry for this module
                        $sql = sprintf("DELETE FROM %s WHERE (module_id = %u) AND (block_id = %u)", $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                        $this->db->query($sql);
                    } else {
                        // this block doesnt have other entries, so disable the block and let it show on top page only. otherwise, this block will not display anymore on block admin page!
                        $sql = sprintf("UPDATE %s SET visible = 0 WHERE bid = %u", $this->db->prefix('newblocks'), $i);
                        $this->db->query($sql);
                        $sql = sprintf("UPDATE %s SET module_id = -1 WHERE module_id = %u", $this->db->prefix('block_module_link'), $module->getVar('mid'));
                        $this->db->query($sql);
                    }
                }
            }
        }

        if (!empty($this->_cachedModule_dirname[$module->getVar('dirname')])) {
            unset ($this->_cachedModule_dirname[$module->getVar('dirname')]);
        }
        if (!empty($this->_cachedModule_mid[$module->getVar('mid')])) {
            unset ($this->_cachedModule_mid[$module->getVar('mid')]);
        }
        return true;
    }

    /**
     * returns an array of module names
     *
     * @param   bool    $criteria
     * @param   boolean $dirname_as_key
     *      if true, array keys will be module directory names
     *      if false, array keys will be module id
     * @return  array
     **/
    function getList($criteria = null, $dirname_as_key = false)
    {
        $ret = array();
        $modules = $this->getObjects($criteria, true);
        foreach (array_keys($modules) as $i) {
            if (!$dirname_as_key) {
                $ret[$i] =& $modules[$i]->getVar('name');
            } else {
                $ret[$modules[$i]->getVar('dirname')] =& $modules[$i]->getVar('name');
            }
        }
        return $ret;
    }

    /**
    * loads the {@link XoopsModule} object from requested url
    *
    * @return object
    */
    function loadModule() {
        $url_arr = explode('/',strstr($_SERVER['REQUEST_URI'],'/modules/'));
        if (isset($url_arr[2])) {
            $xoopsModule =& $this->getByDirname($url_arr[2]);
        }
        else {
            $xoopsModule =& $this->getByDirname('system');
        }
        unset($url_arr);
        return $xoopsModule;
    }
}
?>