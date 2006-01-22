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
// Author: XOOPS Foundation                                                  //
// URL: http://www.xoops.org/                                                //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //


/**
 * @package kernel
 * @copyright copyright &copy; 2000 XOOPS.org
 */
class XoopsProfile extends XoopsObject {
    function XoopsProfile() {
        $this->initVar('profileid', XOBJ_DTYPE_INT, null, true);
    }
    
    /**
    * Initiate variables
    * @param array $fields field information array of {@link XoopsProfileField} objects
    */
    function init($fields) {
        if (is_array($fields) && count($fields) > 0) {
            foreach (array_keys($fields) as $key) {
                $this->initVar($key, $fields[$key]->getVar('field_valuetype'), $fields[$key]->getVar('field_default', 'n'), $fields[$key]->getVar('field_required'), $fields[$key]->getVar('field_maxlength'));
            }
        }
    }
}
/**
 * @package kernel
 * @copyright copyright &copy; 2000 XOOPS.org
 */
class XoopsProfileHandler extends XoopsPersistableObjectHandler {
    /**
    * holds reference to {@link XoopsProfileFieldHandler} object
    */
    var $_fHandler;
    
    /**
    * Array of {@link XoopsProfileField} objects
    * @var array
    */
    var $_fields = array();

    function XoopsProfileHandler(&$db) {
        $this->XoopsPersistableObjectHandler($db, 'user_profile', "XoopsProfile", "profileid");
        $this->_fHandler =& xoops_gethandler('ProfileField');
    }
    
    /**
    * Create new {@link XoopsProfileField} object
    * 
    * @param bool $isNew
    *
    * @return object
    */
    function &createField($isNew = true) {
        $return =& $this->_fHandler->create($isNew);
        return $return;
    }
    
    /**
    * Load field information
    *
    * @return array
    */
    function loadFields() {
        if (count($this->_fields) == 0) {
            $this->_fields =& $this->_fHandler->loadFields();
        }
        return $this->_fields;
    }
    
    /**
    * Fetch fields
    *
    * @param object $criteria {@link CriteriaElement} object
    * @param bool $id_as_key return array with field IDs as key?
    * @param bool $as_object return array of objects?
    *
    * @return array
    **/
    function getFields($criteria, $id_as_key = true, $as_object = true) {
        return $this->_fHandler->getObjects($criteria, $id_as_key, $as_object);
    }
    
    /**
    * Insert a field in the database
    *
    * @param object $field
    * @param bool $force
    *
    * @return bool
    */
    function insertField(&$field, $force = false) {
        return $this->_fHandler->insert($field, $force);
    }
    
    /**
    * Delete a field from the database
    *
    * @param object $field
    * @param bool $force
    *
    * @return bool
    */
    function deleteField(&$field, $force = false) {
        return $this->_fHandler->delete($field, $force);
    }
    
    /**
    * Save a new field in the database
    *
    * @param array $vars array of variables, taken from $module->loadInfo('profile')['field']
    * @param int $categoryid ID of the category to add it to
    * @param int $type valuetype of the field
    * @param int $moduleid ID of the module, this field belongs to
    * @param int $weight
    *
    * @return string
    **/
    function saveField($vars, $moduleid, $weight = 0) {
        $field =& $this->createField();
        $field->setVar('field_name', $vars['name']);
        $field->setVar('field_moduleid', $moduleid);
        $field->setVar('field_valuetype', $vars['valuetype']);
        $field->setVar('field_type', $vars['type']);
        $field->setVar('field_weight', $weight);
        if (isset($vars['title'])) {
            $field->setVar('field_title', $vars['title']);
        }
        if (isset($vars['description'])) {
            $field->setVar('field_description', $vars['description']);
        }
        if (isset($vars['required'])) {
            $field->setVar('field_required', $vars['required']); //0 = no, 1 = yes
        }
        if (isset($vars['maxlength'])) {
            $field->setVar('field_maxlength', $vars['maxlength']);
        }
        if (isset($vars['default'])) {
            $field->setVar('field_default', $vars['default']);
        }
        if (isset($vars['notnull'])) {
            $field->setVar('field_notnull', $vars['notnull']);
        }
        if (isset($vars['show'])) {
            $field->setVar('field_show', $vars['show']);
        }
        if (isset($vars['edit'])) {
            $field->setVar('field_edit', $vars['edit']);
        }
        if (isset($vars['config'])) {
            $field->setVar('field_config', $vars['config']);
        }
        if (isset($vars['options'])) {
            $field->setVar('field_options', $vars['options']);
        }
        else {
            $field->setVar('field_options', array());
        }
        if ($this->insertField($field)) {
            $msg = '&nbsp;&nbsp;Field <b>'.$vars['name'].'</b> added to the database';
        }
        else {
            $msg = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert field <b>'.$vars['name'].'</b> into the database. '.implode(' ', $field->getErrors()).$this->db->error().'</span>';
        }
        unset($field);
        return $msg;
    }
        
    /**
    * Update cached storage of profile field information
    *
    * @return bool
    **/
    function updateCache() {
        return $this->_fHandler->updateCache();
    }
}
?>