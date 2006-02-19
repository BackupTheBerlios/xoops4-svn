<?php
/**
 * XoopsTpl class definition (XOOPS 2.0 compatibility layer)
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since       2.0
 * @package		mod_xoops20
 * @version		$Id$
 * @author      Kazumi Ono (AKA onokazu)
 */
/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) )	die();


XOS::import( 'xoops_opal_Smarty' );

/**
 * Template engine
 *
 * DO NOT USE ANY OF THIS CLASS METHODS !!!! (Access the corresponding public property)
 * 
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 * @deprecated
 */
class XoopsTpl extends xoops_opal_Smarty {

	function XoopsTpl() {
		global $xoopsConfig, $xoopsUser;

		$this->xoops_opal_Smarty();
		$this->assign( array(
			'xoops_url' => XOOPS_URL,
			'xoops_rootpath'	=> XOOPS_ROOT_PATH,
			'xoops_langcode'	=> _LANGCODE,
			'xoops_charset'		=> _CHARSET,
			'xoops_version'		=> XOOPS_VERSION,
			'xoops_upload_url'	=> XOOPS_UPLOAD_URL,
		) );
		if ( $xoopsUser ) {
			$this->assign( 'xoops_isadmin', $xoopsUser->isAdmin() );
		}
	}

	function xoops_setTemplateDir($dirname) {
		$this->template_dir = $dirname;
	}
	function xoops_getTemplateDir() {
		return $this->template_dir;
	}
	function xoops_setDebugging($flag=false) {
		$this->debugging = is_bool($flag) ? $flag : false;
	}
	function xoops_setCaching( $num = 0 ) {
		$this->caching = (int)$num;
	}
	function xoops_setCacheTime( $num = 0 ) {
		if ( ( $num = (int)$num ) <= 0) {
			$this->caching = 0;
		} else {
			$this->cache_lifetime = $num;
		}
	}
	function xoops_setCompileDir($dirname) {
		$this->compile_dir = $dirname;
	}
	function xoops_setCacheDir($dirname) {
		$this->cache_dir = $dirname;
	}
	function xoops_canUpdateFromFile() {
		return $this->_canUpdateFromFile;
	}
	function xoops_fetchFromData( $data ) {
		return $this->fetchFromData( $data );
	}

}

/**
 * Those have been removed. Please tell us if your module use them
 **/
function xoops_template_create ($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj) {
	trigger_error( "Function removed", E_USER_ERROR );
}

/**
 * function to update compiled template file in templates_c folder
 * 
 * @param   string  $tpl_id
 * @param   boolean $clear_old
 * @return  boolean
 **/
function xoops_template_touch($tpl_id, $clear_old = true) {
	trigger_error( "Function removed", E_USER_ERROR );
}

/**
 * Clear the module cache
 * 
 * @param   int $mid    Module ID
 * @return 
 **/
function xoops_template_clear_module_cache($mid) {
	$block_arr =& XoopsBlock::getByModule($mid);
	$count = count($block_arr);
	if ($count > 0) {
		$xoopsTpl = new XoopsTpl();	
		$xoopsTpl->xoops_setCaching(2);
		for ($i = 0; $i < $count; $i++) {
			if ($block_arr[$i]->getVar('template') != '') {
				$xoopsTpl->clear_cache('db:'.$block_arr[$i]->getVar('template'), 'blk_'.$block_arr[$i]->getVar('bid'));
			}
		}
	}
}
?>