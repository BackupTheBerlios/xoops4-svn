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
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**#@+
 * Config type
 */
define('XOOPS_CONF', 1);
define('XOOPS_CONF_METAFOOTER', 3);
define('XOOPS_CONF_CENSOR', 4);
define('XOOPS_CONF_SEARCH', 5);
define('XOOPS_CONF_MAILER', 6);
define('XOOPS_CONF_AUTH', 7);
/**#@-*/

/**
 * 
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsConfigItem extends XoopsObject
{

    /**
     * Config options
     * 
     * @var	array
     * @access	private
     */
    var $_confOptions = array();

    /**
     * Constructor
     */
    function XoopsConfigItem()
    {
        $this->initVar('conf_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_modid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_catid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_name', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('conf_value', XOBJ_DTYPE_TXTAREA, "");
        $this->initVar('conf_desc', XOBJ_DTYPE_OTHER, "");
        $this->initVar('conf_formtype', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_valuetype', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_order', XOBJ_DTYPE_INT, 0);
    }

    /**
     * Get a config value in a format ready for output
     * 
     * @return	string
     */
    function getConfValueForOutput()
    {
        switch ( $this->getVar('conf_valuetype') ) {
        case 'int':
            return intval( $this->getVar('conf_value', 'N') );
            break;
        case 'array':
            $value = $this->getVar( 'conf_value', 'N' );
            if ( $value != "" ) {
                $value = @unserialize($value);
            }
            return ( is_array($value) ? $value : array() );
        case 'float':
            $value = $this->getVar( 'conf_value', 'N' );
            return (float)$value;
            break;
        case 'textarea':
            return $this->getVar('conf_value');
        }
		return $this->getVar( 'conf_value', 'N' );

    }

    /**
     * Set a config value
     * 
     * @param	mixed   &$value Value
     * @param	bool    $force_slash
     */
    function setConfValueForInput(&$value, $force_slash = false)
    {
        switch($this->getVar('conf_valuetype')) {
        case 'array':
            if (!is_array($value)) {
                $value = explode('|', trim($value));
            }
            $this->setVar('conf_value', serialize($value), $force_slash);
            break;
        case 'text':
            $this->setVar('conf_value', trim($value), $force_slash);
            break;
        default:
            $this->setVar('conf_value', $value, $force_slash);
            break;
        }
    }

    /**
     * Assign one or more {@link XoopsConfigItemOption}s 
     * 
     * @param	mixed   $option either a {@link XoopsConfigItemOption} object or an array of them
     */
    function setConfOptions($option)
    {
        if (is_array($option)) {
            $count = count($option);
            for ($i = 0; $i < $count; $i++) {
                $this->setConfOptions($option[$i]);
            }
        } else {
            if(is_object($option)) {
                $this->_confOptions[] =& $option;
            }
        }
    }

    /**
     * Get the {@link XoopsConfigItemOption}s of this Config
     * 
     * @return	array   array of {@link XoopsConfigItemOption} 
     */
    function &getConfOptions()
    {
        return $this->_confOptions;
    }
    
    /**
    * Clear options from this item
    *
    * @return void
    **/
    function clearConfOptions() {
        $this->_confOptions = array();
    }
}


/**
* XOOPS configuration handler class.  
* 
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS configuration class objects.
*
* @author       Kazumi Ono <onokazu@xoops.org>
* @copyright    copyright (c) 2000-2003 XOOPS.org
*/
class XoopsConfigItemHandler extends XoopsPersistableObjectHandler
{
    function XoopsConfigItemHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'config', 'XoopsConfigItem', 'conf_id', 'conf_title');
    }
    
    /**
     * Get configs from the database
     * 
     * @param	object  $criteria   {@link CriteriaElement}
     * @param	bool    $id_as_key  return the config's id as key?
     *
     * @return	array   Array of {@link XoopsConfigItem} objects
     */
    function getObjects($criteria = null, $id_as_key = false) {
        if ($criteria == null) {
            $criteria = new CriteriaCompo();
        }
        if ($criteria->getSort() == "") {
            $criteria->setSort('conf_order');
        }
        return parent::getObjects($criteria, $id_as_key);
    }
}

?>