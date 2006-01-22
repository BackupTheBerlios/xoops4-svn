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
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000 XOOPS.org
 **/

/**
 * A block
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000 XOOPS.org
 *
 * @package kernel
 **/
class XoopsBlock extends XoopsObject
{

    /**
     * constructor
	 *
     * @param mixed $id
     **/
    function XoopsBlock($id = null)
    {
        $this->initVar('bid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('options', XOBJ_DTYPE_ARRAY, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        $this->initVar('isactive', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dirname', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('func_file', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('show_func', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('edit_func', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('template', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('last_modified', XOBJ_DTYPE_INT, 0, false);

    }

    /**
     * (HTML-) form for setting the options of the block
	 *
	 * @return string HTML for the form, FALSE if not defined for this block
     **/
    function getOptions() {
        if ( !( $edit_func = $this->getVar('edit_func') ) ) {
            return false;
        }
        list( $dirname, $func, $lang ) = array( $this->getVar('dirname'), $this->getVar('func_file'), $GLOBALS['xoopsConfig']['language'] );

        if ( file_exists( XOOPS_ROOT_PATH . "/modules/$dirname/blocks/$func" ) ) {
            if ( file_exists( XOOPS_ROOT_PATH . "/modules/$dirname/language/$lang/blocks.php") ) {
                include_once( XOOPS_ROOT_PATH . "/modules/$dirname/language/$lang/blocks.php" );
            } elseif ( file_exists( XOOPS_ROOT_PATH . "/modules/$dirname/language/english/blocks.php" ) ) {
                include_once( XOOPS_ROOT_PATH . "/modules/$dirname/language/english/blocks.php" );
            }
            include_once( XOOPS_ROOT_PATH . "/modules/$dirname/blocks/$func" );
            return $edit_func( $this->getVar('options') );
        }
		return false;
    }
}


/**
 * XOOPS block handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000 XOOPS.org
 * @package kernel
 * @subpackage block
*/
class XoopsBlockHandler extends XoopsPersistableObjectHandler
{

    function XoopsBlockHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'newblocks', 'XoopsBlock', 'bid', 'name');
    }

    /**
     * delete a block from the database
     *
	 * @param object XoopsBlock $block reference to the block to delete
	 * @param bool $force
	 *
	 * @return bool TRUE if succesful
     **/
    function delete(&$block, $force = false)
    {
        $instance_handler =& xoops_gethandler('blockinstance');
        $instances = $instance_handler->getObjects(new Criteria('bid', $block->getVar('bid')));
        if (count($instances) > 0) {
            foreach (array_keys($instances) as $i) {
                if (!$instance_handler->delete($instances[$i], $force)) {
                    //@TODO: LOCALIZE!
                    $block->setErrors("could not delete block instance ".$instances[$i]->getVar('title')."<br />".implode('<br />', $instances[$i]->getErrors()));
                    return false;
                }
            }
        }
        if ($block->getVar('template') != '') {
            $tplfile_handler =& xoops_gethandler('tplfile');
            $btemplate = $tplfile_handler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $block->getVar('bid'));
            if (count($btemplate) > 0) {
                $tplfile_handler->delete($btemplate[0]);
            }
        }
        return parent::delete($block, $force);
    }

    /**
     * get a list of blocks matchich certain conditions
	 *
	 * @param string $criteria conditions to match
	 *
	 * @return array array of blocks matching the conditions
     **/
    function getList($criteria = null)
    {
        $blocks = $this->getObjects($criteria, true);
        $ret = array();
        foreach ( array_keys($blocks) as $i ) {
            $ret[$i] = $blocks[$i]->getVar( 'name' );
        }
        return $ret;
    }

    function getByModule($moduleid, $asobject=true, $id_as_key = false) {
        return $this->getObjects(new Criteria('mid', $moduleid), $id_as_key, $asobject);
    }

    function getAllByGroupModule($groupid, $module_id=0, $toponlyblock=false, $visible=null, $orderby='i.weight,i.instanceid', $isactive=1) {
        $instance_handler =& xoops_gethandler( 'blockinstance' );
        return $instance_handler->getLinkedObjects( $groupid, $module_id, $toponlyblock, $visible, $orderby, $isactive );
    }

    function getAdminBlocks($groupid, $orderby='i.weight,i.instanceid') {
        $instance_handler =& xoops_gethandler('blockinstance');
        return $instance_handler->getLinkedObjects($groupid, -2, false, 1);
    }

    function assignBlocks() {
        global $xoopsTpl, $xoopsLogger, $xoopsUser, $xoopsModule, $xoopsConfig, $xoopsOption;

        $modulepage = $xoopsModule->getCurrentPage();

        if ($modulepage == array() ) {
            return;
        }

        $groups = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $block_arr = $this->getAllByGroupModule($groups, $modulepage["mid"], $modulepage["page"], XOOPS_BLOCK_VISIBLE);

        $old_context = $xoopsLogger->context;
        $xoopsLogger->context = "block";
        $sides = array(XOOPS_SIDEBLOCK_LEFT => 'l',
                            XOOPS_CENTERBLOCK_LEFT => 'cl',
                            XOOPS_CENTERBLOCK_RIGHT => 'cr',
                            XOOPS_CENTERBLOCK_CENTER => 'cc',
                            XOOPS_SIDEBLOCK_RIGHT => 'r');

        foreach (array_keys($block_arr) as $i) {
            $bcachetime = $block_arr[$i]->getVar('bcachetime');
            if (empty($bcachetime)) {
                $xoopsTpl->xoops_setCaching(0);
            } else {
                $xoopsTpl->xoops_setCaching(2);
                $xoopsTpl->xoops_setCacheTime($bcachetime);
            }
            $btpl = $block_arr[$i]->block->getVar('template') != '' ? $block_arr[$i]->block->getVar('template') : "system_block_dummy.html";

            list( $side, $instanceid ) = array( $block_arr[$i]->getVar('side'), $block_arr[$i]->getVar('instanceid') );
            if (empty($bcachetime) || !$xoopsTpl->is_cached('db:'.$btpl, 'blk_'.$instanceid)) {
            	//@TODO: This should definitely not be here !! (have to put this in the logger class somewhere, if needed)
            	if ( !isset( $xoopsLogger->queries[$xoopsLogger->context] ) ) {
            		$xoopsLogger->queries[$xoopsLogger->context] = array();
            	}
                $querycountbefore = count( $xoopsLogger->queries[$xoopsLogger->context] );
                if ( !( $bresult = $block_arr[$i]->buildBlock() ) ) {
                    continue;
                }
                $xoopsTpl->assign_by_ref('block', $bresult);

                $bcontent = $xoopsTpl->fetch( "db:$btpl", "blk_$instanceid" );
                $xoopsTpl->clear_assign('block');
                $querycount = count( $xoopsLogger->queries[$xoopsLogger->context] ) - $querycountbefore;
                $xoopsLogger->addBlock( $block_arr[$i]->block->getVar('name'), false, 0, $querycount );
            } else {
                $xoopsLogger->addBlock( $block_arr[$i]->block->getVar('name'), true, $bcachetime );
                $bcontent = $xoopsTpl->fetch( "db:$btpl", "blk_$instanceid" );
            }

            $block_info = array(
                'id'		=> $instanceid,
                'typeid'	=> $block_arr[$i]->block->getVar('bid'),
            	'title'		=> $block_arr[$i]->getVar('title'),
                'content'	=> $bcontent,
                'weight'	=> $block_arr[$i]->getVar('weight'),
			);
			if ( !isset($show_cblock) && in_array( $side, array(XOOPS_CENTERBLOCK_CENTER, XOOPS_CENTERBLOCK_LEFT, XOOPS_CENTERBLOCK_RIGHT) ) ) {
                //backwards compatibility fix
                $show_cblock = 1;
                $xoopsTpl->assign('xoops_showcblock', 1);
            } elseif ( !isset( ${"show_".$sides[$side]."block"}) ) {
                ${"show_".$sides[$side]."block"} = 1;
                $xoopsTpl->assign( 'xoops_show' . $sides[$side] . 'block', 1);
            }
            $xoopsTpl->append( 'xoops_' . $sides[$side] . 'blocks' , $block_info );
            unset($bcontent);
        }
        $xoopsLogger->context = $old_context;
    }
}
?>