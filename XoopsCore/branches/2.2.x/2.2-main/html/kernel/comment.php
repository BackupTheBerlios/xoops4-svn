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
// URL: http://www.xoops.org/ http://jp.xoops.org/  http://www.myweb.ne.jp/  //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
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
 * A Comment
 * 
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsComment extends XoopsObject
{

    /**
     * Constructor
     **/
    function XoopsComment()
    {
        $this->XoopsObject();
        $this->initVar('com_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('com_pid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_modid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('com_icon', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('com_title', XOBJ_DTYPE_TXTBOX, null, true, 255, true);
        $this->initVar('com_text', XOBJ_DTYPE_TXTAREA, null, true, null, true);
        $this->initVar('com_created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_modified', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('com_ip', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('com_sig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_itemid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_rootid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_status', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_exparams', XOBJ_DTYPE_OTHER, "", false, 255);
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 0, false);
    }

	/**
	 * Is this comment on the root level?
	 * 
	 * @return  bool
	 **/
	function isRoot()
    {
        return ($this->getVar('com_id') == $this->getVar('com_rootid'));
    }
}

/**
 * XOOPS comment handler class.  
 * 
 * This class is responsible for providing data access mechanisms to the data source 
 * of XOOPS comment class objects.
 *
 * 
 * @package     kernel
 * @subpackage  comment
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsCommentHandler extends XoopsPersistableObjectHandler
{
    function XoopsCommentHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'xoopscomments', 'XoopsComment', 'com_id', 'com_title');
    }

    /**
     * Retrieves comments for an item
     * 
     * @param   int     $module_id  Module ID
     * @param   int     $item_id    Item ID
     * @param   string  $order      Sort order
     * @param   int     $status     Status of the comment
     * @param   int     $limit      Max num of comments to retrieve
     * @param   int     $start      Start offset
     * 
     * @return  array   Array of {@link XoopsComment} objects
     **/
    function getByItemId($module_id, $item_id, $order = null, $status = null, $limit = null, $start = 0)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', intval($module_id)));
        $criteria->add(new Criteria('com_itemid', intval($item_id)));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', intval($status)));
        }
        if (isset($order)) {
            $criteria->setSort("com_created");
            $criteria->setOrder($order);
        }
        if (isset($limit)) {
            $criteria->setLimit($limit);
			$criteria->setStart($start);
        }
        return $this->getObjects($criteria);
    }

    /**
     * Gets total number of comments for an item
     * 
     * @param   int     $module_id  Module ID
     * @param   int     $item_id    Item ID
     * @param   int     $status     Status of the comment
     * 
     * @return  array   Array of {@link XoopsComment} objects
     **/
    function getCountByItemId($module_id, $item_id, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', intval($module_id)));
        $criteria->add(new Criteria('com_itemid', intval($item_id)));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', intval($status)));
        }
        return $this->getCount($criteria);
    }


    /**
     * Get the top {@link XoopsComment}s 
     * 
     * @param   int     $module_id
     * @param   int     $item_id
     * @param   strint  $order
     * @param   int     $status
     * 
     * @return  array   Array of {@link XoopsComment} objects
     **/
    function getTopComments($module_id, $item_id, $order, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', intval($module_id)));
        $criteria->add(new Criteria('com_itemid', intval($item_id)));
        $criteria->add(new Criteria('com_pid', 0));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', intval($status)));
        }
        $criteria->setOrder($order);
        return $this->getObjects($criteria);
    }

    /**
     * Retrieve a whole thread
     * 
     * @param   int     $comment_rootid
     * @param   int     $comment_id
     * @param   int     $status
     * 
     * @return  array   Array of {@link XoopsComment} objects
     **/
    function getThread($comment_rootid, $comment_id, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_rootid', intval($comment_rootid)));
        $criteria->add(new Criteria('com_id', intval($comment_id), '>='));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', intval($status)));
        }
        return $this->getObjects($criteria);
    }

    /**
     * Update
     * 
     * @param   object  &$comment       {@link XoopsComment} object
     * @param   string  $field_name     Name of the field
     * @param   mixed   $field_value    Value to write
     * 
     * @return  bool
     **/
    function updateByField(&$comment, $field_name, $field_value)
    {
        $comment->unsetNew();
        $comment->setVar($field_name, $field_value);
        return $this->insert($comment);
    }

    /**
     * Delete all comments for one whole module
     * 
     * @param   int $module_id  ID of the module
     * @return  bool
     **/
    function deleteByModule($module_id)
    {
        return $this->deleteAll(new Criteria('com_modid', intval($module_id)));
    }
}
?>