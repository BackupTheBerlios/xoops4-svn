<?php
/**
 * xoops_logos_PageBuilder component class file
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package      xoops_logos
 * @subpackage   xoops_logos_PageBuilder
 * @version		$Id$
 * @author       Skalpa Keo <skalpa@xoops.org>
 * @since        2.3.0
 */
/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) )	exit();

include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

/**
 * xoops_logos_PageBuilder main class
 *
 * @package     xoops_logos
 * @subpackage  xoops_logos_PageBuilder
 * @author 		Skalpa Keo
 * @since       2.3.0
 */
class xoops_logos_PageBuilder {
	
	var $blocks = array();	

	function retrieveBlocks() {
		global $xoopsUser, $xoopsModule, $xoopsConfig;
		
		if ( @is_object( $xoopsModule ) ) {
			list( $mid, $dirname ) = array( $xoopsModule->getVar('mid'), $xoopsModule->getVar('dirname') );
		} else {
			list( $mid, $dirname ) = array( 0, 'system' );
		}
		$startMod = ( $xoopsConfig['startpage'] == '--' ) ? 'system' : $xoopsConfig['startpage'];
		$isStart = ( substr( $_SERVER['SCRIPT_NAME'], -9 ) == 'index.php' && $startMod == $dirname );
		
		$groups = @is_object( $xoopsUser ) ? $xoopsUser->getGroups() : array( XOOPS_GROUP_ANONYMOUS );
		
		$oldzones = array(
        	XOOPS_SIDEBLOCK_LEFT		=> 'canvas_left',
        	XOOPS_SIDEBLOCK_RIGHT		=> 'canvas_right',
        	XOOPS_CENTERBLOCK_CENTER	=> 'page_top',
        	XOOPS_CENTERBLOCK_LEFT		=> 'page_topleft',
        	XOOPS_CENTERBLOCK_RIGHT		=> 'page_topright',
		);

		$xoopsblock = new XoopsBlock();
    	$block_arr = array();
	    $block_arr =& $xoopsblock->getAllByGroupModule( $groups, $mid, $isStart, XOOPS_BLOCK_VISIBLE);
	    foreach ( $block_arr as $block ) {
	    	$side = $oldzones[ $block->getVar('side') ];
	    	if ( $var = $this->buildBlock( $block ) ) {
	    		$this->blocks[$side][] = $var;
	    	}
	    }
	}
	
	function buildBlock( $xobject ) {
		// The lame type workaround will change
		$block = array(
			'module'	=> $xobject->getVar( 'dirname' ),
			'title'		=> $xobject->getVar( 'title' ),
			'name'		=> strtolower( preg_replace( '/[^0-9a-zA-Z_]/', '', str_replace( ' ', '_', $xobject->getVar( 'name' ) ) ) ),
			'weight'	=> $xobject->getVar( 'weight' ),
			'lastmod'	=> $xobject->getVar( 'last_modified' ),
		);

		global $xoopsTpl, $xoopsLogger;
		
		$bcachetime = intval( $xobject->getVar('bcachetime') );
        if (empty($bcachetime)) {
            $xoopsTpl->caching = 0;
        } else {
            $xoopsTpl->caching = 2;
            $xoopsTpl->cache_lifetime = $bcachetime;
        }
		if ( '' != ( $tplName = $xobject->getVar('template') ) ) {
			$tplName = "xotpl:modules/{$block['module']}/templates/blocks/$tplName";
		} else {
			$tplName = "xotpl:modules/system/templates/blocks/system_block_dummy.html";
		}
		$cacheid = 'blk_' . $xobject->getVar('bid') . ':' . md5( serialize( $xobject->getVar( 'options', 'n' ) ) );
             
        if ( !$bcachetime || !$xoopsTpl->is_cached( $tplName, $cacheid ) ) {
            $xoopsLogger->addBlock( $xobject->getVar('name') );
            if ( ! ( $bresult = $xobject->buildBlock() ) ) {
                return false;
            }
			$xoopsTpl->assign( 'block', $bresult );
            $block['content'] = $xoopsTpl->fetch( $tplName, $cacheid );
            $xoopsTpl->clear_assign('block');
        } else {
            $xoopsLogger->addBlock( $xobject->getVar('name'), true, $bcachetime );
            $block['content'] = $xoopsTpl->fetch( $tplName, $cacheid );
        }
        return $block;
	}
	
	function assignVars( &$template ) {
		$template->assign( 'xoBlocks', $this->blocks );
	}
	
	
}