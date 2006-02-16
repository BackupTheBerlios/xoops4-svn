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
 * a group of users
 * 
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @author Kazumi Ono <onokazu@xoops.org> 
 * @package kernel
 */
class XoopsGroup extends XoopsObject
{
    /**
     * constructor 
     */
    function XoopsGroup()
    {
        $this->XoopsObject();
        $this->initVar('groupid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('group_type', XOBJ_DTYPE_OTHER, '', false);
    }
}


/**
* XOOPS group handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS group class objects.
*
* @author Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
* @subpackage member
*/
class XoopsGroupHandler extends XoopsPersistableObjectHandler
{
    function XoopsGroupHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'groups', 'XoopsGroup', 'groupid', 'name');
    }
}

/**
 * membership of a user in a group
 * 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsMembership extends XoopsObject
{
    /**
     * constructor 
     */
    function XoopsMembership()
    {
        $this->XoopsObject();
        $this->initVar('linkid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('groupid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
    }
}

/**
* XOOPS membership handler class. (Singleton)
* 
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS group membership class objects.
*
* @author Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
*/
class XoopsMembershipHandler extends XoopsPersistableObjectHandler
{

    function XoopsMembershipHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'groups_users_link', 'XoopsMembership', 'linkid');
    }

    /**
     * retrieve groups for a user
     * 
     * @param int $uid ID of the user
     * @param bool $asobject should the groups be returned as {@link XoopsGroup}
     * objects? FALSE returns associative array.
     * @return array array of groups the user belongs to
     */
    function getGroupsByUser($uid)
    {
        $ret = array();
        $sql = 'SELECT groupid FROM '.$this->db->prefix('groups_users_link').' WHERE uid='.intval($uid);
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow['groupid'];
        }
        return $ret;
    }

    /**
     * retrieve users belonging to a group
     * 
     * @param int $groupid ID of the group
     * @param bool $asobject return users as {@link XoopsUser} objects?
     * FALSE will return arrays
     * @param int $limit number of entries to return
     * @param int $start offset of first entry to return
     * @return array array of users belonging to the group
     */
    function getUsersByGroup($groupid, $limit=0, $start=0)
    {
        $ret = array();
        $sql = 'SELECT uid FROM '.$this->db->prefix('groups_users_link').' WHERE groupid='.intval($groupid);
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow['uid'];
        }
        return $ret;
    }
}
?>