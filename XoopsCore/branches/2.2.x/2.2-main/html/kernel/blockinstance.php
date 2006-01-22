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
require_once XOOPS_ROOT_PATH."/kernel/object.php";

class XoopsBlockInstance extends XoopsObject
{
    var $block;
    
    function XoopsBlockInstance()
    {
        $this->initVar('instanceid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('bid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('options', XOBJ_DTYPE_ARRAY, null, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 150);
        
        $this->initVar('side', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('visible', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('bcachetime', XOBJ_DTYPE_INT, 0, false);
    }
    
    function setBlock(&$block) {
        $this->block =& $block;
    }

    /**
     * (HTML-) form for setting the options of the block
	 * 
	 * @return string HTML for the form, FALSE if not defined for this block 
     **/
    function getOptions()
    {
        $edit_func = $this->block->getVar('edit_func');
        if (!$edit_func) {
            return false;
        }
        if (file_exists(XOOPS_ROOT_PATH.'/modules/'.$this->block->getVar('dirname').'/blocks/'.$this->block->getVar('func_file'))) {
            if (file_exists(XOOPS_ROOT_PATH.'/modules/'.$this->block->getVar('dirname').'/language/'.$GLOBALS['xoopsConfig']['language'].'/blocks.php')) {
                include_once XOOPS_ROOT_PATH.'/modules/'.$this->block->getVar('dirname').'/language/'.$GLOBALS['xoopsConfig']['language'].'/blocks.php';
            } elseif (file_exists(XOOPS_ROOT_PATH.'/modules/'.$this->block->getVar('dirname').'/language/english/blocks.php')) {
                include_once XOOPS_ROOT_PATH.'/modules/'.$this->block->getVar('dirname').'/language/english/blocks.php';
            }
            include_once XOOPS_ROOT_PATH.'/modules/'.$this->block->getVar('dirname').'/blocks/'.$this->block->getVar('func_file');
            $options = $this->getVar('options');
            $edit_form = $edit_func($options);
            if (!$edit_form) {
                return false;
            }
            return $edit_form;
        } else {
            return false;
        }
    }
    
    function &buildBlock()
    {
        global $xoopsConfig, $xoopsOption;
        $block = array();

        // get block display function
        $show_func = $this->block->getVar('show_func');
        if ( !$show_func ) {
            return false;
        }
        // must get lang files b4 execution of the function
        if ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->block->getVar('dirname')."/blocks/".$this->block->getVar('func_file')) ) {
            if ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->block->getVar('dirname')."/language/".$xoopsConfig['language']."/blocks.php") ) {
                include_once XOOPS_ROOT_PATH."/modules/".$this->block->getVar('dirname')."/language/".$xoopsConfig['language']."/blocks.php";
            } elseif ( file_exists(XOOPS_ROOT_PATH."/modules/".$this->block->getVar('dirname')."/language/english/blocks.php") ) {
                include_once XOOPS_ROOT_PATH."/modules/".$this->block->getVar('dirname')."/language/english/blocks.php";
            }
            include_once XOOPS_ROOT_PATH."/modules/".$this->block->getVar('dirname')."/blocks/".$this->block->getVar('func_file');
            $options = $this->getVar("options");
//            var_dump($this->getVar('title'), $this);
            if ( function_exists($show_func) ) {
                // execute the function
                $block = $show_func($options);
                if ( !$block ) {
                    return $block;
                }
            } else {
                return $block;
            }
        } else {
            return $block;
        }

        return $block;
    }
    /**
    * Get module IDs this instance is visible in
    *
    * @return array
    **/
    function getVisibleIn() {
        $ret = array();
        if (!$this->isNew()) {
            $sql = "SELECT module_id, pageid FROM ".$GLOBALS['xoopsDB']->prefix('block_module_link')." WHERE block_id=".$this->getVar('instanceid');
            $result = $GLOBALS['xoopsDB']->query($sql);
            while (list($mid, $pageid) = $GLOBALS['xoopsDB']->fetchRow($result)) {
                $ret[] = $mid."-".$pageid;
            }
        }
        return $ret;
    }
    
    function getVisibleGroups() {
        $groupperm_handler =& xoops_gethandler('groupperm');
        return $groupperm_handler->getGroupIds('block_read', $this->getVar('instanceid'));
    }
}

class XoopsBlockInstanceHandler extends XoopsPersistableObjectHandler {
    function XoopsBlockInstanceHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'block_instance', 'XoopsBlockInstance', 'instanceid', 'title');
    }
    
    function delete(&$obj, $force = false) {
        $sql = sprintf("DELETE FROM %s WHERE block_id = %u", $this->db->prefix('block_module_link'), $obj->getVar('instanceid'));
        if ($this->db->query($sql)) {
            $groupperm_handler =& xoops_gethandler('groupperm');
            $criteria = new CriteriaCompo(new Criteria('gperm_modid', 1));
            $criteria->add(new Criteria('gperm_name', 'block_read'));
            $criteria->add(new Criteria('gperm_itemid', $obj->getVar('instanceid')));
            if ($groupperm_handler->deleteAll($criteria)) {
                return parent::delete($obj, $force);
            }
        }
        //@TODO: LOCALIZE
        $obj->setErrors("Could not delete instance link");
        return false;
    }
    
    function getLinkedObjects($groupid, $module_id=0, $pageid=0, $visible=null, $orderby='i.weight,i.instanceid', $isactive=1) {
        $ret = array();
        $groupperm_handler =& xoops_gethandler('groupperm');
        $instanceids = $groupperm_handler->getItemIds('block_read', $groupid);
        if (!empty($instanceids)) {
            $block_handler =& xoops_gethandler('block');
            $sql = 'SELECT DISTINCT i.instanceid, b.*, i.* FROM '.$this->table.' i, '.$block_handler->table.' b, '.$this->db->prefix('block_module_link').' m WHERE m.block_id=i.instanceid';
            $sql .= ' AND i.instanceid IN ('.implode(',', $instanceids).')';
            if (isset($visible)) {
                $sql .= ' AND i.visible='.intval($visible);
            }
            $sql .= ' AND i.bid=b.bid';
            $sql .= ' AND b.isactive='.$isactive;
            if (!empty($module_id) && $module_id != 0) {
                $sql .= ' AND ( (m.module_id=0 AND m.pageid=0) OR (m.module_id='.intval($module_id).' AND m.pageid IN (0,'.intval($pageid).')) )';
            } else {
                if ($pageid == 2) {
                    //adminside blocks
                    $sql .= ' AND m.module_id=0 AND m.pageid=2';
                }
                else {
                    $sql .= ' AND m.module_id=0 AND m.pageid IN (0, '.intval($pageid).')';
                }
            }
            $sql .= ' ORDER BY '.$orderby;
            $result = $this->db->query($sql);
            while ( $myrow = $this->db->fetchArray($result) ) {
                $newblock = false;
                if (!isset($blocks[$myrow['bid']])) {
                    $blocks[$myrow['bid']] =& $block_handler->create(false);
                    $newblock = true;
                }
                
                $instance =& $this->create(false);
                $instance_vars = array_keys($instance->getVars());
                foreach ($myrow as $key => $value) {
                    if ($newblock && !in_array($key, $instance_vars)) {
                        $blocks[$myrow['bid']]->assignVar($key, $value);
                    }
                    else {
                        $instance->assignVar($key, $value);
                    }
                }
                $blocks[$myrow['bid']]->assignVar('bid', $myrow['bid']); 
                $instance->setBlock($blocks[$myrow['bid']]);
                $ret[] = $instance;
                unset($instance);
            }
        }
        return $ret;
    }
}
?>