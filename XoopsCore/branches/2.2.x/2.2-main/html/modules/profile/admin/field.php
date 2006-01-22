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
include '../../../include/cp_header.php';
xoops_cp_header();
$xTheme->loadModuleAdminMenu(3, _PROFILE_MI_FIELDS);
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : (isset($_REQUEST['id']) ? "edit" : 'list');

$profilefield_handler =& xoops_gethandler('profilefield');

switch($op) {
    default:
    case "list":
    $fields =& $profilefield_handler->getObjects(null, true, false);

    $module_handler =& xoops_gethandler('module');
    $modules =& $module_handler->getObjects(null, true);

    $fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
    $fieldcats =& $fieldcat_handler->getObjects(null, true);

    $cat_handler =& xoops_getmodulehandler('category');
    $criteria = new CriteriaCompo();
    $criteria->setSort('cat_weight');
    $cats =& $cat_handler->getObjects($criteria, true);
    unset($criteria);

    $categories[0] = _PROFILE_AM_DEFAULT;
    if (count($cats) > 0) {
        foreach (array_keys($cats) as $i) {
            $categories[$cats[$i]->getVar('catid')] = $cats[$i]->getVar('cat_title');
        }
    }
    $xoopsTpl->assign('categories', $categories);
    unset($categories);
    $valuetypes = array(XOBJ_DTYPE_ARRAY => _PROFILE_AM_ARRAY,
                        XOBJ_DTYPE_EMAIL => _PROFILE_AM_EMAIL,
                        XOBJ_DTYPE_INT => _PROFILE_AM_INT,
                        XOBJ_DTYPE_TXTAREA => _PROFILE_AM_TXTAREA,
                        XOBJ_DTYPE_TXTBOX => _PROFILE_AM_TXTBOX,
                        XOBJ_DTYPE_URL => _PROFILE_AM_URL,
                        XOBJ_DTYPE_OTHER => _PROFILE_AM_OTHER,
                        XOBJ_DTYPE_MTIME => _PROFILE_AM_DATE);

    $fieldtypes = array('checkbox' => _PROFILE_AM_CHECKBOX,
                            'group' => _PROFILE_AM_GROUP,
                            'group_multi' => _PROFILE_AM_GROUPMULTI,
                            'language' => _PROFILE_AM_LANGUAGE,
                            'radio' => _PROFILE_AM_RADIO,
                            'select' => _PROFILE_AM_SELECT,
                            'select_multi' => _PROFILE_AM_SELECTMULTI,
                            'textarea' => _PROFILE_AM_TEXTAREA,
                            'dhtml' => _PROFILE_AM_DHTMLTEXTAREA,
                            'textbox' => _PROFILE_AM_TEXTBOX,
                            'timezone' => _PROFILE_AM_TIMEZONE,
                            'yesno' => _PROFILE_AM_YESNO,
                            'date' => _PROFILE_AM_DATE,
                            'datetime' => _PROFILE_AM_DATETIME,
                            'theme' => _PROFILE_AM_THEME,
                            'autotext' => _PROFILE_AM_AUTOTEXT);

    foreach (array_keys($fields) as $i) {
        $fields[$i]['canEdit'] = $fields[$i]['field_config'] || $fields[$i]['field_show'] || $fields[$i]['field_edit'];
        $fields[$i]['canDelete'] = $fields[$i]['field_config'];
        $fields[$i]['module'] = $modules[$fields[$i]['field_moduleid']]->getVar('name');
        $fields[$i]['fieldtype'] = $fieldtypes[$fields[$i]['field_type']];
        $fields[$i]['valuetype'] = $valuetypes[$fields[$i]['field_valuetype']];
        $fields[$i]['catid'] = isset($fieldcats[$fields[$i]['fieldid']]) ? $fieldcats[$fields[$i]['fieldid']]->getVar('catid') : 0;
        $fields[$i]['field_weight'] = isset($fieldcats[$fields[$i]['fieldid']]) ? intval($fieldcats[$fields[$i]['fieldid']]->getVar('field_weight')) : 1;
        $categories[$fields[$i]['catid']][] = $fields[$i];
        $weights[$fields[$i]['catid']][] = $fields[$i]['field_weight'];
    }
    //sort fields order in categories
    foreach (array_keys($categories) as $i) {
        array_multisort($weights[$i], SORT_ASC, array_keys($categories[$i]), SORT_ASC, $categories[$i]);
    }
    ksort($categories);
    $xoopsTpl->assign('fieldcategories', $categories);
    $xoopsTpl->assign('token', $GLOBALS['xoopsSecurity']->getTokenHTML());
    $xoopsOption['template_main'] = "profile_admin_fieldlist.html";
    break;

    case "new":
    include_once('../include/forms.php');
    $obj =& $profilefield_handler->create();
    $form =& getFieldForm($obj);
    $form->display();
    break;

    case "edit":
    $obj =& $profilefield_handler->get($_REQUEST['id']);
    if (!$obj->getVar('field_config') && !$obj->getVar('field_show') && !$obj->getVar('field_edit')) { //If no configs exist
        redirect_header('field.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
    }
    include_once('../include/forms.php');
    $form =& getFieldForm($obj);
    $form->display();
    break;

    case "reorder":
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    if (isset($_POST['fieldids']) && count($_POST['fieldids']) > 0) {
        $oldweight = $_POST['oldweight'];
        $oldcat = $_POST['oldcat'];
        $category = $_POST['category'];
        $weight = $_POST['weight'];
        $ids = array();
        foreach ($_POST['fieldids'] as $fieldid) {
            if ($oldweight[$fieldid] != $weight[$fieldid] || $oldcat[$fieldid] != $category[$fieldid]) {
                //if field has changed
                $ids[] = intval($fieldid);
            }
        }
        if (count($ids) > 0) {
            $errors = array();
            //if there are changed fields, fetch the fieldcategory objects
            $fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
            $fieldcats =& $fieldcat_handler->getObjects(new Criteria('fieldid', "(".implode(',', array_values($ids)).")", 'IN'), true);
            foreach ($ids as $i) {
                if (!isset($fieldcats[$i])) {
                    $fieldcats[$i] =& $fieldcat_handler->create();
                    $fieldcats[$i]->setVar('fieldid', $i);
                }
                $fieldcats[$i]->setVar('field_weight', intval($weight[$i]));
                $fieldcats[$i]->setVar('catid', intval($category[$i]));
                if (!$fieldcat_handler->insert($fieldcats[$i])) {
                    $errors = array_merge($errors, $fieldcats[$i]->getErrors());
                }
            }
            if (count($errors) == 0) {
                //no errors
                redirect_header('field.php', 2, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_FIELDS));
            }
            else {
                redirect_header('field.php', 3, implode('<br />', $errors));
            }
        }
    }
    break;

    case "save":
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $redirect_to_edit = false;
    if (isset($_REQUEST['id'])) {
        $obj =& $profilefield_handler->get($_REQUEST['id']);
        if (!$obj->getVar('field_config') && !$obj->getVar('field_show') && !$obj->getVar('field_edit')) { //If no configs exist
            redirect_header('admin.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
        }
    }
    else {
        $obj =& $profilefield_handler->create();
        $obj->setVar('field_name', $_REQUEST['field_name']);
        $obj->setVar('field_moduleid', $xoopsModule->getVar('mid'));
        $obj->setVar('field_show', 1);
        $obj->setVar('field_edit', 1);
        $obj->setVar('field_config', 1);
        $redirect_to_edit = true;
    }
    $obj->setVar('field_title', $_REQUEST['field_title']);
    $obj->setVar('field_description', $_REQUEST['field_description']);
    if ($obj->getVar('field_config')) {
        $obj->setVar('field_type', $_REQUEST['field_type']);
        if (isset($_REQUEST['field_valuetype'])) {
            $obj->setVar('field_valuetype', $_REQUEST['field_valuetype']);
        }
        $options = $obj->getVar('field_options');
        if (isset($_REQUEST['addOption']) && $_REQUEST['addOption']['value'] != "") {
            if ($_REQUEST['addOption']['key'] == "") {
                $_REQUEST['addOption']['key'] = $_REQUEST['addOption']['value'];
            }
            $options[$_REQUEST['addOption']['key']] = $_REQUEST['addOption']['value'];
            $redirect_to_edit = true;
        }
        if (isset($_REQUEST['removeOptions']) && is_array($_REQUEST['removeOptions'])) {
            foreach ($_REQUEST['removeOptions'] as $index) {
                unset($options[$index]);
            }
            $redirect_to_edit = true;
        }
        $obj->setVar('field_options', $options);
    }
    if ($obj->getVar('field_edit')) {
        $required = isset($_REQUEST['field_required']) ? $_REQUEST['field_required'] : 0;
        $obj->setVar('field_required', $required); //0 = no, 1 = yes
        if (isset($_REQUEST['field_maxlength'])) {
            $obj->setVar('field_maxlength', $_REQUEST['field_maxlength']);
        }
        if (isset($_REQUEST['field_default'])) {
            //Check for multiple selections
            if (is_array($_REQUEST['field_default'])) {
                $obj->setVar('field_default', serialize($_REQUEST['field_default']));
            }
            else {
                $obj->setVar('field_default', $_REQUEST['field_default']);
            }
        }
    }

    if ($obj->getVar('field_show')) {
        //$obj->setVar('field_weight', $_REQUEST['field_weight']);
        //Add field to category
        //$obj->setVar('catid', $_REQUEST['catid']);
    }
    if ($obj->getVar('field_edit') && isset($_REQUEST['field_register'])) {
        $obj->setVar('field_register', $_REQUEST['field_register']);
    }
    if ($profilefield_handler->insert($obj)) {
        $fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
        $fieldcat =& $fieldcat_handler->get($obj->getVar('fieldid'));
        $fieldcat->setVar('fieldid', $obj->getVar('fieldid'));
        $fieldcat->setVar('catid', intval($_REQUEST['field_category']));
        $fieldcat_handler->insert($fieldcat);

        $groupperm_handler =& xoops_gethandler('groupperm');

        $perm_arr = array();
        if ($obj->getVar('field_show')) {
            $perm_arr[] = 'profile_show';
            $perm_arr[] = 'profile_visible';
        }
        if ($obj->getVar('field_edit')) {
            $perm_arr[] = 'profile_edit';
        }
        if ($obj->getVar('field_edit') || $obj->getVar('field_show')) {
            $perm_arr[] = 'profile_search';
        }
        if (count($perm_arr) > 0) {
            foreach ($perm_arr as $perm) {
                $criteria = new CriteriaCompo(new Criteria('gperm_name', $perm));
                $criteria->add(new Criteria('gperm_itemid', intval($obj->getVar('fieldid'))));
                $criteria->add(new Criteria('gperm_modid', intval($xoopsModule->getVar('mid'))));
                if (isset($_REQUEST[$perm]) && is_array($_REQUEST[$perm])) {
                    $perms =& $groupperm_handler->getObjects($criteria);
                    if (count($perms) > 0) {
                        foreach (array_keys($perms) as $i) {
                            $groups[$perms[$i]->getVar('gperm_groupid')] =& $perms[$i];
                        }
                    }
                    else {
                        $groups = array();
                    }
                    foreach ($_REQUEST[$perm] as $groupid) {
                        $groupid = intval($groupid);
                        if (!isset($groups[$groupid])) {
                            $perm_obj =& $groupperm_handler->create();
                            $perm_obj->setVar('gperm_name', $perm);
                            $perm_obj->setVar('gperm_itemid', intval($obj->getVar('fieldid')));
                            $perm_obj->setVar('gperm_modid', $xoopsModule->getVar('mid'));
                            $perm_obj->setVar('gperm_groupid', $groupid);
                            $groupperm_handler->insert($perm_obj);
                            unset($perm_obj);
                        }
                    }
                    $removed_groups = array_diff(array_keys($groups), $_REQUEST[$perm]);
                    if (count($removed_groups) > 0) {
                        $criteria->add(new Criteria('gperm_groupid', "(".implode(',', $removed_groups).")", "IN"));
                        $groupperm_handler->deleteAll($criteria);
                    }
                    unset($groups);

                }
                else {
                    $groupperm_handler->deleteAll($criteria);
                }
                unset($criteria);
            }
        }
        $url = $redirect_to_edit ? 'field.php?op=edit&amp;id='.$obj->getVar('fieldid') : 'field.php';
        redirect_header($url, 3, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_FIELD));
    }
    include_once('../include/forms.php');
    echo $obj->getHtmlErrors();
    $form =& getFieldForm($obj);
    $form->display();
    break;

    case "delete":
    $obj =& $profilefield_handler->get($_REQUEST['id']);
    if (!$obj->getVar('field_config')) {
        redirect_header('index.php', 2, _PROFILE_AM_FIELDNOTCONFIGURABLE);
    }
    if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('field.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if ($profilefield_handler->delete($obj)) {
        	$fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
            $criteria = new CriteriaCompo(new Criteria('fieldid', $obj->getVar('fieldid')));
            if($fieldcat_handler->deleteAll($criteria)){
            	redirect_header('field.php', 3, sprintf(_PROFILE_AM_DELETEDSUCCESS, _PROFILE_AM_FIELD));
        	}else{
	        	// Any error message?
        	}
        }
        else {
            echo $obj->getHtmlErrors();
        }
    }
    else {
        xoops_confirm(array('ok' => 1, 'id' => $_REQUEST['id'], 'op' => 'delete'), $_SERVER['REQUEST_URI'], sprintf(_PROFILE_AM_RUSUREDEL, $obj->getVar('field_title')));
    }
    break;
}

xoops_cp_footer();
?>