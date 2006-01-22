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
 * 
 * 
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A group permission
 * 
 * These permissions are managed through a {@link XoopsGroupPermHandler} object
 * 
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsGroupPerm extends XoopsObject
{

    /**
     * Constructor
     * 
     */
    function XoopsGroupPerm()
    {
        $this->XoopsObject();
        $this->initVar('gperm_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gperm_groupid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gperm_itemid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gperm_modid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('gperm_name', XOBJ_DTYPE_OTHER, null, false);
    }
}


/**
* XOOPS group permission handler class.
* 
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS group permission class objects. 
* This class is an abstract class to be implemented by child group permission classes.
*
* @see          XoopsGroupPerm
* @author       Kazumi Ono  <onokazu@xoops.org>
* @copyright	copyright (c) 2000-2003 XOOPS.org
*/
class XoopsGroupPermHandler extends XoopsPersistableObjectHandler
{

    function XoopsGroupPermHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'group_permission', 'XoopsGroupPerm', 'gperm_id');
    }

    /**
     * Delete all module specific permissions assigned for a group
     * 
     * @param	int  $gperm_groupid ID of a group
     * @param	int  $gperm_modid ID of a module
     * 
     * @return	bool TRUE on success
     */
    function deleteByGroup($gperm_groupid, $gperm_modid = null)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_groupid', intval($gperm_groupid)));
		if (isset($gperm_modid)) {
            $criteria->add(new Criteria('gperm_modid', intval($gperm_modid)));
        }
        return $this->deleteAll($criteria);
    }

    /**
     * Delete all module specific permissions
     * 
     * @param	int  $gperm_modid ID of a module
     * @param	string  $gperm_name Name of a module permission
     * @param	int  $gperm_itemid ID of a module item
     * 
     * @return	bool TRUE on success
     */
    function deleteByModule($gperm_modid, $gperm_name = null, $gperm_itemid = null)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_modid', intval($gperm_modid)));
		if (isset($gperm_name)) {
			$criteria->add(new Criteria('gperm_name', $gperm_name));
			if (isset($gperm_itemid)) {
				$criteria->add(new Criteria('gperm_itemid', intval($gperm_itemid)));
			}
		}
        return $this->deleteAll($criteria);
    }
    /**#@-*/

    /**
     * Check permission
     * 
     * @param	string    $gperm_name       Name of permission
     * @param	int       $gperm_itemid     ID of an item
     * @param	int/array $gperm_groupid    A group ID or an array of group IDs
     * @param	int       $gperm_modid      ID of a module
     * 
     * @return	bool    TRUE if permission is enabled
     */
    function checkRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_modid', $gperm_modid));
        $criteria->add(new Criteria('gperm_name', $gperm_name));
        
        if (is_array($gperm_groupid)) {
			if (in_array(XOOPS_GROUP_ADMIN, $gperm_groupid)) {
                return true;
            }
            $criteria->add(new Criteria('gperm_groupid', "(".implode(',', $gperm_groupid).")", "IN"));
        } else {
            if (XOOPS_GROUP_ADMIN == $gperm_groupid) {
                return true;
            }
            $criteria->add(new Criteria('gperm_groupid', $gperm_groupid));
        }
        $gperm_itemid = intval($gperm_itemid);
        if ($gperm_itemid > 0) {
            $criteria->add(new Criteria('gperm_itemid', $gperm_itemid));
        }
        if ($this->getCount($criteria) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Add a permission
     * 
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_groupid    ID of a group
     * @param	int     $gperm_modid      ID of a module
     *
     * @return	bool    TRUE jf success
     */
    function addRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $perm =& $this->create();
        $perm->setVar('gperm_name', $gperm_name);
        $perm->setVar('gperm_groupid', $gperm_groupid);
        $perm->setVar('gperm_itemid', $gperm_itemid);
        $perm->setVar('gperm_modid', $gperm_modid);
        return $this->insert($perm);
    }
    
    /**
     * Remove a permission
     * 
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_groupid    ID of a group
     * @param	int     $gperm_modid      ID of a module
     *
     * @return	bool    TRUE jf success
     */
    function deleteRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_name', $gperm_name));
        $criteria->add(new Criteria('gperm_groupid', $gperm_groupid));
        $criteria->add(new Criteria('gperm_itemid', $gperm_itemid));
        $criteria->add(new Criteria('gperm_modid', $gperm_modid));
        $perm = $this->getObjects($criteria);
        if (isset($perm[0]) && is_object($perm[0])) {
            return $this->delete($perm[0]);
        }
        return false;
    }

    /**
     * Get all item IDs that a group is assigned a specific permission
     * 
     * @param	string    $gperm_name       Name of permission
     * @param	int/array $gperm_groupid    A group ID or an array of group IDs
     * @param	int       $gperm_modid      ID of a module
     *
     * @return  array     array of item IDs
     */
	function getItemIds($gperm_name, $gperm_groupid, $gperm_modid = 1)
	{
		$ret = array();
		$criteria = new CriteriaCompo(new Criteria('gperm_modid', intval($gperm_modid)));
		$criteria->add(new Criteria('gperm_name', $gperm_name));
		if (is_array($gperm_groupid)) {
            $criteria->add(new Criteria('gperm_groupid', "(".implode(',', $gperm_groupid).")", "IN"));
		} else {
			$criteria->add(new Criteria('gperm_groupid', intval($gperm_groupid)));
		}
		$perms =& $this->getObjects($criteria, true);
		foreach (array_keys($perms) as $i) {
			$ret[] = $perms[$i]->getVar('gperm_itemid');
		}
		return array_unique($ret);
	}

    /**
     * Get all group IDs assigned a specific permission for a particular item
     * 
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_modid      ID of a module
     *
     * @return  array   array of group IDs
     */
	function getGroupIds($gperm_name, $gperm_itemid, $gperm_modid = 1)
	{
		$ret = array();
		$criteria = new CriteriaCompo(new Criteria('gperm_modid', intval($gperm_modid)));
		$criteria->add(new Criteria('gperm_name', $gperm_name));
		$criteria->add(new Criteria('gperm_itemid', intval($gperm_itemid)));
		$perms =& $this->getObjects($criteria, true);
		foreach (array_keys($perms) as $i) {
			$ret[] = $perms[$i]->getVar('gperm_groupid');
		}
		return $ret;
	}
}
?>