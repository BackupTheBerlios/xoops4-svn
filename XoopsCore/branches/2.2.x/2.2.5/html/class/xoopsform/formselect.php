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
	die("XOOPS root path not defined");
}
/**
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A select field
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormSelect extends XoopsFormElement {

	/**
     * Options
	 * @var array   
	 * @access	private
	 */
	var $_options = array();

	/**
     * Allow multiple selections?
	 * @var	bool    
	 * @access	private
	 */
	var $_multiple = false;

	/**
     * Number of rows. "1" makes a dropdown list.
	 * @var	int 
	 * @access	private
	 */
	var $_size;

	/**
     * Pre-selected values
	 * @var	array   
	 * @access	private
	 */
	var $_value = array();
	
	/**
     * Disabled values
	 * @var	array   
	 * @access	private
	 */
	var $_disabled = array();

	/**
	 * Constructor
	 * 
	 * @param	string	$caption	Caption
	 * @param	string	$name       "name" attribute
	 * @param	mixed	$value	    Pre-selected value (or array of them).
	 * @param	int		$size	    Number or rows. "1" makes a drop-down-list
     * @param	bool    $multiple   Allow multiple selections?
	 */
	function XoopsFormSelect($caption, $name, $value=null, $size=1, $multiple=false, $id=""){
		$this->setCaption($caption);
		$this->setName($name);
		$this->_multiple = $multiple;
		$this->_size = intval($size);
		if (isset($value)) {
			$this->setValue($value);
		}
		$this->setId($id);
	}

	/**
	 * Are multiple selections allowed?
	 * 
     * @return	bool
	 */
	function isMultiple(){
		return $this->_multiple;
	}

	/**
	 * Get the size
	 * 
     * @return	int
	 */
	function getSize(){
		return $this->_size;
	}

	/**
	 * Get an array of pre-selected values
	 * 
     * @return	array
	 */
	function getValue(){
		return $this->_value;
	}

	/**
	 * Set pre-selected values
	 * 
     * @param	$value	mixed
	 */
	function setValue($value){
		if (is_array($value)) {
			foreach ($value as $v) {
				$this->_value[] = $v;
			}
		} else {
			$this->_value[] = $value;
		}
	}

	/**
	 * Add an option
     * 
	 * @param	string  $value  "value" attribute
     * @param	string  $name   "name" attribute
     * @param   bool    $disabled   whether the value should be disabled in the selection
	 */
	function addOption($value, $name="", $disabled = false){
		if ( $name != "" ) {
			$this->_options[$value] = $name;
		} else {
			$this->_options[$value] = $value;
		}
		$this->_disabled[$value] = $disabled;
	}

	/**
	 * Add multiple options
	 * 
     * @param	array   $options    Associative array of value->name pairs
     * @param   array   $disabled   array of values that should be disabled
	 */
	function addOptionArray($options, $disabled = array()){
		if ( is_array($options) ) {
			foreach ( $options as $k=>$v ) {
			    if ($disabled != array()) {
			        $disabled = in_array($k, $disabled);
			    }
				$this->addOption($k, $v, $disabled);
			}
		}
	}

	/**
	 * Get all options
	 * 
     * @return	array   Associative array of value->name pairs
	 */
	function getOptions(){
		return $this->_options;
	}
	
	/**
	* Get disabled values
	*
	* @return  array
	*/
	function getDisabled() {
	    return $this->_disabled;
	}

	/**
	 * Prepare HTML for output
	 * 
     * @return	string  HTML
	 */
	function render(){
	    $ret = "<select  size='".$this->getSize()."'".$this->getExtra()."";
	    if ($this->isMultiple() != false) {
	        $ret .= " name='".$this->getName()."[]' id='".$this->getId()."' multiple='multiple'>\n";
	    } else {
	        $ret .= " name='".$this->getName()."' id='".$this->getId()."'>\n";
	    }
	    $disabled = $this->getDisabled();
	    foreach ( $this->getOptions() as $value => $name ) {
	        $ret .= "<option value='".htmlspecialchars($value, ENT_QUOTES)."'";
	        if (count($this->getValue()) > 0 && in_array($value, $this->getValue())) {
	            $ret .= " selected='selected'";
	        }
	        if ($disabled[$value]) {
	            $ret .= " disabled='disabled'";
	        }
	        $ret .= ">".$name."</option>\n";
	    }
	    $ret .= "</select>";
	    return $ret;
	}
}
?>