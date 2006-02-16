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
 * Class for users
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsUser extends XoopsObject
{

    /**
     * Array of groups that user belongs to
     * @var array
	 * @access private
     */
    var $_groups = array();
    /**
     * @var bool is the user admin?
	 * @access private
     */
    var $_isAdmin = null;
    /**
     * @var string user's rank
	 * @access private
     */
    var $_rank = null;
    /**
     * @var bool is the user online?
     * @access private
     */
    var $_isOnline = null;

    /**
    * @var object reference to a {@link XoopsProfile} object
    * @access private
    */
    var $_profile;

    /**
     * constructor
     * @param array $id Array of key-value-pairs to be assigned to the user. (for backward compatibility only)
     * @param int $id ID of the user to be loaded from the database.
     */
    function XoopsUser($id = null)
    {
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('loginname', XOBJ_DTYPE_TXTBOX, null, true, 25);
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 55);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, "", false, 75);
        $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, true, 60);
        $this->initVar('pass', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('rank', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('level', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('user_avatar', XOBJ_DTYPE_TXTBOX, "blank.gif", false, 30);
        // for backward compatibility
        if (!is_null($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $member_handler =& xoops_gethandler('member');
                $user =& $member_handler->getUser($id);
                foreach ($user->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                $this->_profile =& $user->getProfile();
            }
        }
        else {
            $profile_handler =& xoops_gethandler('profile');
            $this->_profile =& $profile_handler->create();
            $this->_profile->setNew();
            $this->_profile->init($profile_handler->loadFields());
        }
    }

    /**
    * Assign values to attributes
    *
    * @param array $vars key => value pair array of values
    *
    * @return void
    **/
    function assignVars($vars) {
        $uservars = array_keys($this->getVars());
        foreach ($vars as $key => $value) {
            if (in_array($key, $uservars)) {
                $this->assignVar($key, $value);
            }
            /*
            * Dynamic user profiles - Mith
            */
            else {
                $this->_profile->assignVar($key, $value);
            }
            /*
            * Dynamic user profiles end
            */
        }
    }

    /**
    * Get user profile
    *
    * @return object
    */
    function getProfile() {
        return $this->_profile;
    }

    /**
    * returns a specific variable for the object in a proper format
    *
    * @access public
    * @param string $key key of the object's variable to be returned
    * @param string $format format to use for the output
    * @return mixed formatted value of the variable
    */
    function getVar($key, $format = "s") {
        $uservars = array_keys($this->getVars());
        if (!in_array($key, $uservars)) {
            return $this->_profile->getVar($key, $format);
        }
        return parent::getVar($key, $format);
    }

    /**
    * assign a value to a variable
    *
    * @access public
    * @param string $key name of the variable to assign
    * @param mixed $value value to assign
    * @param bool $not_gpc
    */
    function setVar($key, $value, $not_gpc = false) {
        $uservars = array_keys($this->getVars());
        if (!in_array($key, $uservars)) {
            return $this->_profile->setVar($key, $value, $not_gpc);
        }
        return parent::setVar($key, $value, $not_gpc);
    }


    /**
	 * check if the user is a guest user
     *
     * @return bool returns false
     *
     */
    function isGuest()
    {
        return false;
    }


    /**
     * Updated by Catzwolf 11 Jan 2004
	 * find the username for a given ID
	 *
	 * @param int $userid ID of the user to find
	 * @param int $usereal switch for usename or realname
	 * @return string name of the user. name for "anonymous" if not found.
     */
    function getUnameFromId( $userid, $usereal = 0 )
    {
        $userid = intval($userid);
        $usereal = intval($usereal);
        if ($userid > 0) {
            $member_handler =& xoops_gethandler('member');
            $user =& $member_handler->getUser($userid);
            if (is_object($user)) {
                $ts =& MyTextSanitizer::getInstance();
                if ( $usereal ) {
                    return $ts->htmlSpecialChars($user->getVar('name'));
                } else {
                    return $ts->htmlSpecialChars($user->getVar('uname'));
                }
            }
        }
        return $GLOBALS['xoopsConfig']['anonymous'];
    }
    /**
     * increase the number of posts for the user
     *
	 * @deprecated
     */
    function incrementPost(){
        $member_handler =& xoops_gethandler('member');
        return $member_handler->updateUserByField($this, 'posts', $this->getVar('posts') + 1);
    }
    /**
	 * set the groups for the user
	 *
	 * @param array $groupsArr Array of groups that user belongs to
	 */
    function setGroups($groupsArr)
    {
        if (is_array($groupsArr)) {
            $this->_groups =& $groupsArr;
        }
    }
    /**
     * get the groups that the user belongs to
	 *
	 * @return array array of groups
     */
    function &getGroups()
    {
        if (empty($this->_groups)) {
            $member_handler =& xoops_gethandler('member');
            $this->_groups = $member_handler->getGroupsByUser($this->getVar('uid'));
        }
        return $this->_groups;
    }
    /**
	 * alias for {@link getGroups()}
	 * @see getGroups()
	 * @return array array of groups
	 * @deprecated
	 */
    function &groups()
    {
        $return =& $this->getGroups();
        return $return;
    }
    /**
     * Is the user admin ?
     *
     * This method will return true if this user has admin rights for the specified module.<br />
     * - If you don't specify any module ID, the current module will be checked.<br />
     * - If you set the module_id to -1, it will return true if the user has admin rights for at least one module
     *
     * @param int $module_id check if user is admin of this module
	 * @return bool is the user admin of that module?
     */
    function isAdmin( $module_id = null ) {
        if ( is_null( $module_id ) ) {
            $module_id = isset($GLOBALS['xoopsModule']) ? $GLOBALS['xoopsModule']->getVar( 'mid', 'n' ) : 1;
        } elseif ( intval($module_id) < 1 ) {
            $module_id = 0;
        }
        if (is_null($this->_isAdmin) || !isset($this->_isAdmin[$module_id])) {
            $moduleperm_handler =& xoops_gethandler('groupperm');
            $groups = $this->getGroups();
            if (count($groups) > 0 ) {
                $this->_isAdmin[$module_id] = $moduleperm_handler->checkRight('module_admin', $module_id, $groups);
            }
        }
        return $this->_isAdmin[$module_id];
    }
    /**
     * get the user's rank
	 * @return array array of rank ID and title
     */
    function rank()
    {
        if (!isset($this->_rank)) {
            $this->_rank = xoops_getrank($this->getVar('rank'), $this->getVar('posts'));
        }
        return $this->_rank;
    }
    /**
     * is the user activated?
     * @return bool
     */
    function isActive()
    {
        return ($this->getVar('level') > 0);
    }

    /**
     * is the user disabled?
     * @return bool
     */
    function isDisabled()
    {
        return ($this->getVar('level') == -1);
    }

    /**
     * is the user currently logged in?
     * @return bool
     */
    function isOnline()
    {
        if (!isset($this->_isOnline)) {
            $onlinehandler =& xoops_gethandler('online');
            $this->_isOnline = ($onlinehandler->getCount(new Criteria('online_uid', $this->getVar('uid'))) > 0) ? true : false;
        }
        return $this->_isOnline;
    }
    /**#@+
    * specialized wrapper for {@link XoopsObject::getVar()}
    *
    * kept for compatibility reasons.
    *
    * @see XoopsObject::getVar()
    * @deprecated
    */
    /**
     * get the users UID
     * @return int
     */
    function uid()
    {
        return $this->getVar("uid");
    }

    /**
     * get the users name
	 * @param string $format format for the output, see {@link XoopsObject::getVar()}
	 * @return string
     */
    function name($format="S")
    {
        return $this->getVar("name", $format);
    }

    /**
     * get the user's uname
	 * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    function uname($format="S")
    {
        return $this->getVar("uname", $format);
    }

    /**
     * get the user's email
	 *
	 * @param string $format format for the output, see {@link XoopsObject::getVar()}
	 * @return string
     */
    function email($format="S")
    {
        return $this->getVar("email", $format);
    }

    function url($format="S")
    {
        return $this->getVar("url", $format);
    }

    function user_avatar($format="S")
    {
        return $this->getVar("user_avatar");
    }

    function user_regdate()
    {
        return $this->getVar("user_regdate");
    }

    function user_icq($format="S")
    {
        return $this->getVar("user_icq", $format);
    }

    function user_from($format="S")
    {
        return $this->getVar("user_from", $format);
    }
    function user_sig($format="S")
    {
        return $this->getVar("user_sig", $format);
    }

    function user_viewemail()
    {
        return $this->getVar("user_viewemail");
    }

    function actkey()
    {
        return $this->getVar("actkey");
    }

    function user_aim($format="S")
    {
        return $this->getVar("user_aim", $format);
    }

    function user_yim($format="S")
    {
        return $this->getVar("user_yim", $format);
    }

    function user_msnm($format="S")
    {
        return $this->getVar("user_msnm", $format);
    }

    function pass()
    {
        return $this->getVar("pass");
    }

    function posts()
    {
        return $this->getVar("posts");
    }

    function attachsig()
    {
        return $this->getVar("attachsig");
    }

    function level()
    {
        return $this->getVar("level");
    }

    function theme()
    {
        return $this->getVar("theme");
    }

    function timezone()
    {
        return $this->getVar("timezone_offset");
    }

    function umode()
    {
        return $this->getVar("umode");
    }

    function uorder()
    {
        return $this->getVar("uorder");
    }

    // RMV-NOTIFY
    function notify_method()
    {
        return $this->getVar("notify_method");
    }

    function notify_mode()
    {
        return $this->getVar("notify_mode");
    }

    function user_occ($format="S")
    {
        return $this->getVar("user_occ", $format);
    }

    function bio($format="S")
    {
        return $this->getVar("bio", $format);
    }

    function user_intrest($format="S")
    {
        return $this->getVar("user_intrest", $format);
    }

    function last_login()
    {
        return $this->getVar("last_login");
    }
    /**#@-*/
}

/**
 * Class that represents a guest user
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsGuestUser extends XoopsUser
{
    /**
	 * check if the user is a guest user
     *
     * @return bool returns true
     *
     */
    function isGuest()
    {
        return true;
    }
}


/**
* XOOPS user handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
*/
class XoopsUserHandler extends XoopsPersistableObjectHandler
{
    /**
    * Reference to the {@link XoopsProfileHandler} instance
    */
    var $_pHandler;
    /**
    * Constructor
    **/
    function XoopsUserHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'users', 'XoopsUser', 'uid', 'uname');
        $this->_pHandler =& xoops_gethandler('profile');
    }

    /**
     * insert a new user in the database
     *
     * @param object $user reference to the {@link XoopsUser} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    function insert(&$user, $force = false)
    {
        if (!parent::insert($user, $force)) {
            return false;
        }
        $profile = $user->getProfile();
        $profile->setVar('profileid', $user->getVar('uid'));

        // save profile
        if (!$this->_pHandler->insert($profile, $force)) {
            foreach ($profile->getErrors() as $error) {
                $user->setErrors($error);
            }
            return false;
        }
        return true;
    }

    /**
     * delete a user from the database
     *
     * @param object $user reference to the user to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function delete(&$user, $force = false)
    {

        if (!parent::delete($user, $force)) {
            return false;
        }
        // delete user profile
        $profile =& $user->getProfile();
        return $this->_pHandler->delete($profile, $force);
    }

    /**
     * retrieve users from the database
     *
     * @param object $criteria {@link CriteriaElement} conditions to be met
     * @param bool $id_as_key use the UID as key for the array?
     * @param bool $as_object return array of objects or array of arrays?
     * @return array array of {@link XoopsUser} objects
     */
    function getObjects($criteria = null, $id_as_key = false, $as_object = true)
    {
        if (is_null($criteria)) {
            $criteria = new Criteria('uid', 0, '!=');
        }
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('users').' u, '.$this->_pHandler->table.' p WHERE u.'.$this->keyName.' = p.'.$this->_pHandler->keyName;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $clause = $criteria->render();
            if ($clause != "") {
                $sql .= ' AND '.$clause;
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        return $this->convertResultSet($result, $id_as_key, $as_object);
    }

    /**
     * Convert a database resultset to a returnable array
     *
     * @param object $result database resultset
     * @param bool $id_as_key
     * @param bool $as_object
     *
     * @return array
     */
    function convertResultSet($result, $id_as_key = false, $as_object = true) {
        $ret = array();
        $fields = $this->_pHandler->loadFields();
        while ($myrow = $this->db->fetchArray($result)) {
            $obj =& $this->create(false);
            $obj->_profile =& $this->_pHandler->create(false);
            $obj->_profile->init($fields);
            $obj->assignVars($myrow);
            if (!$id_as_key) {
                if ($as_object) {
                    $ret[] =& $obj;
                }
                else {
                    $ret[] = $obj->toArray();
                }
            } else {
                if ($as_object) {
                    $ret[$myrow[$this->keyName]] =& $obj;
                }
                else {
                    $ret[$myrow[$this->keyName]] = $obj->toArray();
                }
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * count users matching a condition
     *
     * @param object $criteria {@link CriteriaElement} to match
     * @return int count of users
     */
    function getCount($criteria = null)
    {
        $profile_handler =& xoops_gethandler('profile');
        $sql = 'SELECT COUNT(*) FROM '.$this->table.' u, '.$profile_handler->table.' p WHERE u.'.$this->keyName.' = p.'.$this->_pHandler->keyName;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $clause = $criteria->render();
            if ($clause != "") {
                $sql .= ' AND '.$clause;
            }
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }

    /**
     * get a list of usernames and their IDs
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return array associative array of user-IDs and names
     */
    function getList($criteria = null, $limit = 0, $start = 0) {
        $ret = array();
        $profile_handler =& xoops_gethandler('profile');
        $sql = 'SELECT '.$this->keyName.', '.$this->identifierName.' FROM '.$this->table.' u, '.$profile_handler->table.' p WHERE u.'.$this->keyName.' = p.'.$this->_pHandler->keyName;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $clause = $criteria->render();
            if ($clause != "") {
                $sql .= ' AND '.$clause;
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            else {
                $sql .= ' ORDER BY '.$this->identifierName;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }

        $myts =& MyTextSanitizer::getInstance();
        while ($myrow = $this->db->fetchArray($result)) {
            //identifiers should be textboxes, so sanitize them like that
            $ret[$myrow[$this->keyName]] = $myts->htmlSpecialChars($myrow[$this->identifierName]);
        }
        return $ret;
    }

    /**
     * delete users matching a set of conditions
     *
     * @param object $criteria {@link CriteriaElement}
     * @return bool FALSE if deletion failed
     */
    function deleteAll($criteria = null)
    {
        $users = $this->getList($criteria);
        $newcriteria = new Criteria('uid', "(".implode(',', array_keys($users)).")", "IN");
        if (parent::deleteAll($newcriteria)) {
            unset($newcriteria);
            $newcriteria = new Criteria('profileid', "(".implode(',', array_keys($users)).")", "IN");
            return $this->_pHandler->deleteAll($newcriteria);
        }
        return false;
    }

    /**
     * Change a value for users with a certain criteria
     *
     * @param   string  $fieldname  Name of the field
     * @param   string  $fieldvalue Value to write
     * @param   object  $criteria   {@link CriteriaElement}
     *
     * @return  bool
     **/
    function updateAll($fieldname, $fieldvalue, $criteria = null)
    {
        $profile_fields = array_keys($this->_pHandler->loadFields());
        if (!in_array($fieldname, $profile_fields)) {
            return parent::updateAll($fieldname, $fieldvalue, $criteria);
        }
        else {
            return $this->_pHandler->updateAll($fieldname, $fieldvalue, $criteria);
        }
    }

    /**
     * log in a user - we need this instead of getObjects since the profile table may not be there, in the case of upgrading
     *
     * @param string $uname username as entered in the login form
     * @param string $pwd password entered in the login form
     * @param bool $md5 whether the password is already md5'ed
     *
     * @return object XoopsUser reference to the logged in user. FALSE if failed to log in
     */
    function &loginUser($uname, $pwd, $md5 = false) {
        if (!$md5) {
            $pwd = md5($pwd);
        }
        $criteria = new CriteriaCompo(new Criteria('loginname', $uname));
        $criteria->add(new Criteria('pass', $pwd));

        //First try with profile
        $user = $this->getObjects($criteria);
        if ($user != array() && is_object($user[0]) ) {
            return $user[0];
        }
        //But profile will not work, when you are upgrading from XOOPS 2.0.x
        //so we need to have this extra stuff or an admin would not be able to
        //log in to upgrade the system.
        //It will add another query to wrong logins, but that's not a big problem
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " ".$criteria->renderWhere();
        $result = $this->db->query($sql);
        $user = $this->convertResultSet($result, false);
        if (!$user || count($user) != 1) {
            $return = false;
            return $return;
        }
        return $user[0];
    }

    /**
    * Update a user by field
    *
    * @param    string  $fieldName  name of field to change
    * @param    mixed   $fieldValue Value to set field to
    * @param    int     $uid        ID of user to modify
    *
    * @return   bool
    */
    function updateUserByField($fieldName, $fieldValue, $uid) {
        $fields = $this->_pHandler->loadFields();
        if (in_array($fieldName, array_keys($fields))) {
            $keyName = 'profileid';
        }
        else {
            $keyName = 'uid';
        }
        $criteria = new Criteria($keyName, $uid);
        return $this->updateAll($fieldName, $fieldValue, $criteria);
    }
}
?>