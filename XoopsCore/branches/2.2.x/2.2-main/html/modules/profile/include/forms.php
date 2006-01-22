<?php
/**
    * Get {@link XoopsThemeForm} for adding/editing fields
    *
    * @param object $field {@link XoopsProfileField} object to get edit form for
    * @param mixed $action URL to submit to - or false for $_SERVER['REQUEST_URI']
    *
    * @return object
    */
function getFieldForm(&$field, $action = false) {
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    $title = $field->isNew() ? sprintf(_PROFILE_AM_ADD, _PROFILE_AM_FIELD) : sprintf(_PROFILE_AM_EDIT, _PROFILE_AM_FIELD);

    include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
    $form = new XoopsThemeForm($title, 'form', $action, 'post', true);

    $form->addElement(new XoopsFormText(sprintf(_PROFILE_AM_TITLE, _PROFILE_AM_FIELD), 'field_title', 35, 255, $field->getVar('field_title', 'e')));
    $form->addElement(new XoopsFormTextArea(_PROFILE_AM_DESCRIPTION, 'field_description', $field->getVar('field_description', 'e')));

    if (!$field->isNew()) {
        $fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
        $fieldcat =& $fieldcat_handler->get($field->getVar('fieldid'));
        $fieldcatid = $fieldcat->getVar('catid');
    }
    else {
        $fieldcatid = 0;
    }
    $category_handler =& xoops_getmodulehandler('category');
    $cat_select = new XoopsFormSelect(_PROFILE_AM_CATEGORY, 'field_category', $fieldcatid);
    $cat_select->addOption(0, _PROFILE_AM_DEFAULT);
    $cat_select->addOptionArray($category_handler->getList());
    $form->addElement($cat_select);
    if ($field->getVar('field_config') || $field->isNew()) {
        if (!$field->isNew()) {
            $form->addElement(new XoopsFormLabel(sprintf(_PROFILE_AM_NAME, _PROFILE_AM_FIELD), $field->getVar('field_name')));
            $form->addElement(new XoopsFormHidden('id', $field->getVar('fieldid')));
        }
        else {
            $form->addElement(new XoopsFormText(sprintf(_PROFILE_AM_NAME, _PROFILE_AM_FIELD), 'field_name', 35, 255, $field->getVar('field_name', 'e')));
        }

        //autotext and theme left out of this one as fields of that type should never be changed (valid assumption, I think)
        $fieldtypes = array('checkbox' => _PROFILE_AM_CHECKBOX,
                            'date' => _PROFILE_AM_DATE,
                            'datetime' => _PROFILE_AM_DATETIME,
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
                            'yesno' => _PROFILE_AM_YESNO);

        $element_select = new XoopsFormSelect(_PROFILE_AM_TYPE, 'field_type', $field->getVar('field_type', 'e'));
        $element_select->addOptionArray($fieldtypes);

        $form->addElement($type_select);
        $form->addElement($element_select);

        switch ($field->getVar('field_type')) {
            case "textbox":
            $valuetypes = array(XOBJ_DTYPE_ARRAY => _PROFILE_AM_ARRAY,
                        XOBJ_DTYPE_EMAIL => _PROFILE_AM_EMAIL,
                        XOBJ_DTYPE_INT => _PROFILE_AM_INT,
                        XOBJ_DTYPE_TXTAREA => _PROFILE_AM_TXTAREA,
                        XOBJ_DTYPE_TXTBOX => _PROFILE_AM_TXTBOX,
                        XOBJ_DTYPE_URL => _PROFILE_AM_URL,
                        XOBJ_DTYPE_OTHER => _PROFILE_AM_OTHER);
            $type_select = new XoopsFormSelect(_PROFILE_AM_VALUETYPE, 'field_valuetype', $field->getVar('field_valuetype', 'e'));
            $type_select->addOptionArray($valuetypes);
            $form->addElement($valuetypes);
            break;

            case "select":
            case "radio":
            $valuetypes = array(XOBJ_DTYPE_ARRAY => _PROFILE_AM_ARRAY,
                        XOBJ_DTYPE_EMAIL => _PROFILE_AM_EMAIL,
                        XOBJ_DTYPE_INT => _PROFILE_AM_INT,
                        XOBJ_DTYPE_TXTAREA => _PROFILE_AM_TXTAREA,
                        XOBJ_DTYPE_TXTBOX => _PROFILE_AM_TXTBOX,
                        XOBJ_DTYPE_URL => _PROFILE_AM_URL,
                        XOBJ_DTYPE_OTHER => _PROFILE_AM_OTHER);
            $type_select = new XoopsFormSelect(_PROFILE_AM_VALUETYPE, 'field_valuetype', $field->getVar('field_valuetype', 'e'));
            $type_select->addOptionArray($valuetypes);
            $form->addElement($valuetypes);
            break;


        }

        //$form->addElement(new XoopsFormRadioYN(_PROFILE_AM_NOTNULL, 'field_notnull', $field->getVar('field_notnull', 'e')));

        if ($field->getVar('field_type') == "select" || $field->getVar('field_type') == "select_multi" || $field->getVar('field_type') == "radio" || $field->getVar('field_type') == "checkbox") {
            if (count($field->getVar('field_options')) > 0) {
                $remove_options = new XoopsFormCheckBox(_PROFILE_AM_REMOVEOPTIONS, 'removeOptions');
                $options = $field->getVar('field_options');
                asort($options);
                $remove_options->addOptionArray($options);
                $form->addElement($remove_options);
            }

            $option_tray = new XoopsFormElementTray(_PROFILE_AM_ADDOPTION);
            $option_tray->addElement(new XoopsFormText(_PROFILE_AM_KEY, 'addOption[key]', 15, 35));
            $option_tray->addElement(new XoopsFormText(_PROFILE_AM_VALUE, 'addOption[value]', 35, 255));
            $form->addElement($option_tray);
        }
    }

    if ($field->getVar('field_edit')) {
        switch ($field->getVar('field_type')) {
            case "textbox":
            //proceed to next cases
            case "textarea":
            case "dhtml":
            $form->addElement(new XoopsFormText(_PROFILE_AM_MAXLENGTH, 'field_maxlength', 35, 35, $field->getVar('field_maxlength', 'e')));
            $form->addElement(new XoopsFormTextArea(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
            break;

            case "checkbox":
            case "select_multi":
            $def_value = $field->getVar('field_default', 'e') != null ? unserialize($field->getVar('field_default', 'n')) : null;
            $element = new XoopsFormSelect(_PROFILE_AM_DEFAULT, 'field_default', $def_value, 8, true);
            $options = $field->getVar('field_options');
            asort($options);
            $element->addOptionArray($options);
            $form->addElement($element);
            break;

            case "select":
            case "radio":
            $def_value = $field->getVar('field_default', 'e') != null ? $field->getVar('field_default') : null;
            $element = new XoopsFormSelect(_PROFILE_AM_DEFAULT, 'field_default', $def_value);
            $options = $field->getVar('field_options');
            asort($options);
            $element->addOptionArray($options);
            $form->addElement($element);
            break;

            case "date":
            $form->addElement(new XoopsFormTextDateSelect(_PROFILE_AM_DEFAULT, 'field_default', 15, $field->getVar('field_default', 'e')));
            break;

            case "datetime":
            $form->addElement(new XoopsFormDateTime(_PROFILE_AM_DEFAULT, 'field_default', 15, $field->getVar('field_default', 'e')));
            break;

            case "yesno":
            $form->addElement(new XoopsFormRadioYN(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
            break;

            case "timezone":
            $form->addElement(new XoopsFormSelectTimezone(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
            break;

            case "language":
            $form->addElement(new XoopsFormSelectLang(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
            break;

            case "group":
            $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_DEFAULT, 'field_default', true, $field->getVar('field_default', 'e')));
            break;

            case "group_multi":
            $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_DEFAULT, 'field_default', true, $field->getVar('field_default', 'e'), 5, true));
            break;

            case "theme":
            $form->addElement(new XoopsFormSelectTheme(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
            break;

            case "autotext":
            $form->addElement(new XoopsFormTextArea(_PROFILE_AM_DEFAULT, 'field_default', $field->getVar('field_default', 'e')));
            break;
        }
    }

    $groupperm_handler =& xoops_gethandler('groupperm');
    $searchable_types = array('textbox',
                            'select',
                            'radio',
                            'yesno',
                            'date',
                            'datetime',
                            'timezone',
                            'language');
    if (in_array($field->getVar('field_type'), $searchable_types)) {
        $search_groups = $groupperm_handler->getGroupIds('profile_search', $field->getVar('fieldid'), $GLOBALS['xoopsModule']->getVar('mid'));
        $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_PROF_SEARCH, 'profile_search', true, $search_groups, 5, true));
    }
    if ($field->getVar('field_show') || $field->getVar('field_edit')) {
        //$form->addElement(new XoopsFormText(_PROFILE_AM_FIELD." "._PROFILE_AM_WEIGHT, 'field_weight', 35, 35, $field->getVar('field_weight', 'e')));
        if (!$field->isNew()) {
            //Load groups
            $show_groups = $groupperm_handler->getGroupIds('profile_show', $field->getVar('fieldid'), $GLOBALS['xoopsModule']->getVar('mid'));
            $editable_groups = $groupperm_handler->getGroupIds('profile_edit', $field->getVar('fieldid'), $GLOBALS['xoopsModule']->getVar('mid'));
            $visible_groups = $groupperm_handler->getGroupIds('profile_visible', $field->getVar('fieldid'), $GLOBALS['xoopsModule']->getVar('mid'));

        }
        else {
            $visible_groups = array();
            $show_groups = array();
            $editable_groups = array();
        }
        if ($field->getVar('field_show')) {
//            Leave out categories for now
//            $cat_select = new XoopsFormSelect(_PROFILE_AM_CATEGORY, 'catid', $field->getVar('catid'));
//            $category_handler =& xoops_getmodulehandler('category');
//            $categories =& $category_handler->getObjects(null, true);
//            foreach (array_keys($categories) as $i) {
//                $cat_select->addOption($i, $categories[$i]->getVar('cat_title'));
//            }
//            $form->addElement($cat_select);

            $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_PROF_VISIBLE_ON, 'profile_show', false, $show_groups, 5, true));
            $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_PROF_VISIBLE_FOR, 'profile_visible', true, $visible_groups, 5, true));

        }
        if ($field->getVar('field_edit')) {
            $form->addElement(new XoopsFormSelectGroup(_PROFILE_AM_PROF_EDITABLE, 'profile_edit', false, $editable_groups, 5, true));
            $form->addElement(new XoopsFormRadioYN(_PROFILE_AM_REQUIRED, 'field_required', $field->getVar('field_required', 'e')));
            $form->addElement(new XoopsFormRadioYN(_PROFILE_AM_PROF_REGISTER, 'field_register', $field->getVar('field_register', 'e')));
        }
    }
    $form->addElement(new XoopsFormHidden('op', 'save'));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

    return $form;
}
/**
* Get {@link XoopsThemeForm} for registering new users
*
* @param object $user {@link XoopsUser} to register
* @param mixed $action URL to submit to or false for $_SERVER['REQUEST_URI']
*
* @return object
*/
function getRegisterForm(&$user, $action = false) {
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    global $xoopsModuleConfig, $xoopsConfig;
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

    $reg_form = new XoopsThemeForm(_PROFILE_MA_USERREG, "userinfo", $action, "post", true);

    $elements[0][] = array('element' => new XoopsFormText(_PROFILE_MA_EMAIL, "email", 25, 60, $user->getVar('email', 'e')), 'required' => true);
    $weights[0][] = 0;

    $uname_size = $xoopsModuleConfig['max_uname'] < 35 ? $xoopsModuleConfig['max_uname'] : 35;
    $elements[0][] = array('element' => new XoopsFormText(_PROFILE_MA_NICKNAME, "loginname", $uname_size, $uname_size, $user->getVar('loginname', 'e')), 'required' => true);
    $weights[0][] = 0;

    $elements[0][] = array('element' => new XoopsFormText(_PROFILE_MA_DISPLAYNAME, "uname", $uname_size, 75, $user->getVar('uname', 'e')), 'required' => true);
    $weights[0][] = 0;

    // Dynamic fields
    $profile_handler =& xoops_gethandler('profile');
    // Get fields
    $fields =& $profile_handler->loadFields();

    $profile_fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
    /* @var $profile_fieldcat_handler ProfileFieldCategoryHandler */
    $profile_cat_handler =& xoops_getmodulehandler('category');
    /* @var $profile_cat_handler ProfileCategoryHandler */

    $fieldcats =& $profile_fieldcat_handler->getObjects(null, true);
    if (count($fieldcats) > 0) {
        foreach (array_keys($fieldcats) as $i) {
            $catids[] = $fieldcats[$i]->getVar('catid');
        }
        $categories =& $profile_cat_handler->getObjects(new Criteria('catid', "(".implode(',', array_unique($catids)).")", "IN"), true, false);
    }
    $fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
    $fieldcats =& $fieldcat_handler->getObjects(null, true);

    foreach (array_keys($fields) as $i) {
        if ($fields[$i]->getVar('field_register')) {
            $fieldinfo['element'] = $fields[$i]->getEditElement($user);
            $fieldinfo['required'] = $fields[$i]->getVar('field_required');

            if (isset($fieldcats[$fields[$i]->getVar('fieldid')])) {
                $key = $fieldcats[$fields[$i]->getVar('fieldid')]->getVar('catid');
            }
            else {
                $key = 0;
            }
            $elements[$key][] = $fieldinfo;
            $weights[$key][] = isset($fieldcats[$fields[$i]->getVar('fieldid')]) ? intval($fieldcats[$fields[$i]->getVar('fieldid')]->getVar('field_weight')) : 1;
        }
    }
    ksort($elements);
    foreach (array_keys($elements) as $k) {
        array_multisort($weights[$k], SORT_ASC, array_keys($elements[$k]), SORT_ASC, $elements[$k]);
        $title = isset($categories[$k]) ? $categories[$k]['cat_title'] : _PROFILE_MA_DEFAULT;
        $reg_form->insertBreak($title, 'head');
        foreach (array_keys($elements[$k]) as $i) {
            $reg_form->addElement($elements[$k][$i]['element'], $elements[$k][$i]['required']);
        }
    }
    //end of Dynamic User fields
    $reg_form->addElement(new XoopsFormPassword(_PROFILE_MA_PASSWORD, "pass", 10, 32, ""), true);
    $reg_form->addElement(new XoopsFormPassword(_PROFILE_MA_VERIFYPASS, "vpass", 10, 32, ""), true);

    if ($xoopsModuleConfig['display_disclaimer'] != 0 && $xoopsModuleConfig['disclaimer'] != '') {
        $disc_tray = new XoopsFormElementTray(_PROFILE_MA_DISCLAIMER, '<br />');
        $disc_text = new XoopsFormLabel("", "<div style=\"padding: 5px;\">".$GLOBALS["myts"]->displayTarea($xoopsModuleConfig['disclaimer'],1)."</div>");
        // Should we define a div.xoopsStatement class in style.css?
        $disc_tray->addElement($disc_text);
        $agree_chk = new XoopsFormCheckBox('', 'agree_disc');
        $agree_chk->addOption(1, _PROFILE_MA_IAGREE);
        $disc_tray->addElement($agree_chk);
        $reg_form->addElement($disc_tray);
    }
    $reg_form->addElement(new XoopsFormHidden("op", "newuser"));
    $reg_form->addElement(new XoopsFormButton("", "submit", _PROFILE_MA_SUBMIT, "submit"));
    return $reg_form;
}

/**
* Get {@link XoopsSimpleForm} for finishing registration
*
* @param object $user {@link XoopsUser} object to finish registering
* @param string $vpass Password verification field
* @param mixed $action URL to submit to or false for $_SERVER['REQUEST_URI']
*
* @return object
*/
function getFinishForm(&$user, $vpass, $action = false) {
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

    $form = new XoopsSimpleForm(_PROFILE_MA_USERREG, "userinfo", $action, "post", true);
    $profile = $user->getProfile();
    $array = array_merge(array_keys($user->getVars()), array_keys($profile->getVars()));
    foreach ($array as $field) {
        $value = $user->getVar($field, 'e');
        if (is_array($value)) {
            foreach ($value as $thisvalue) {
                $form->addElement(new XoopsFormHidden($field."[]", $thisvalue));
            }
        }
        else {
            $form->addElement(new XoopsFormHidden($field, $value));
        }
    }
    $myts =& MyTextSanitizer::getInstance();
    $form->addElement(new XoopsFormHidden('vpass', $myts->htmlSpecialChars($vpass)));
    $form->addElement(new XoopsFormHidden('op', 'finish'));
    $form->addElement(new XoopsFormButton('', 'submit', _PROFILE_MA_FINISH, 'submit'));
    return $form;
}

/**
* Get {@link XoopsThemeForm} for editing a user
*
* @param object $user {@link XoopsUser} to edit
*
* @return object
*/
function getUserForm(&$user, $action = false) {
    global $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsUser;
    if ($action === false) {
        $action = $_SERVER['REQUEST_URI'];
    }
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $title = $user->isNew() ? _PROFILE_AM_ADDUSER : _PROFILE_MA_EDITPROFILE;

    $form = new XoopsThemeForm($title, 'userinfo', $action, 'post', true);
    if ($user->isNew() || $xoopsUser->isAdmin()) {
        $elements[0][] = array('element' => new XoopsFormText(_PROFILE_MA_NICKNAME, 'loginname', 25, 255, $user->getVar('loginname', 'e')), 'required' => 1);
    }
    else {
        $elements[0][] = array('element' => new XoopsFormLabel(_PROFILE_MA_NICKNAME, $user->getVar('loginname')), 'required' => 0);
    }
    $weights[0][] = 0;

    $elements[0][] = array('element' => new XoopsFormText(_PROFILE_MA_DISPLAYNAME, 'uname', 25, 75, $user->getVar('uname', 'e')), 'required' => 1);
    $weights[0][] = 0;

    $elements[0][] = array('element' => new XoopsFormText(_PROFILE_MA_REALNAME, 'name', 25, 75, $user->getVar('name', 'e')), 'required' => 0);
    $weights[0][] = 0;

    $email_tray = new XoopsFormElementTray(_PROFILE_MA_EMAIL, '<br />');
    if ($user->isNew() || $xoopsUser->isAdmin()) {
        $email_text = new XoopsFormText('', 'email', 30, 60, $user->getVar('email'));
    } else {
        $email_text = new XoopsFormLabel('', $user->getVar('email'));
    }
    $email_tray->addElement($email_text, ($user->isNew() || $xoopsModuleConfig['allow_chgmail'] == 1));
    $elements[0][] = array('element' => $email_tray, 'required' => 0);
    $weights[0][] = 0;

    if ($xoopsUser->isAdmin() && $user->getVar('uid') != $xoopsUser->getVar('uid')) {
        //If the user is an admin and is editing someone else
        $pwd_text = new XoopsFormPassword('', 'password', 10, 32);
        $pwd_text2 = new XoopsFormPassword('', 'vpass', 10, 32);
        $pwd_tray = new XoopsFormElementTray(_PROFILE_MA_PASSWORD.'<br />'._PROFILE_MA_TYPEPASSTWICE);
        $pwd_tray->addElement($pwd_text);
        $pwd_tray->addElement($pwd_text2);
        $elements[0][] = array('element' => $pwd_tray, 'required' => 0); //cannot set an element tray required
        $weights[0][] = 0;

        $level_radio = new XoopsFormRadio(_PROFILE_MA_ACTIVEUSER, 'level', $user->getVar('level'));
        $level_radio->addOption(1, _PROFILE_MA_ACTIVE);
        $level_radio->addOption(0, _PROFILE_MA_INACTIVE);
        $level_radio->addOption(-1, _PROFILE_MA_DISABLED);
        $elements[0][] = array('element' => $level_radio, 'required' => 0);
        $weights[0][] = 0;
    }

    $elements[0][] = array('element' => new XoopsFormHidden('uid', $user->getVar('uid')), 'required' => 0);
    $weights[0][] = 0;
    $elements[0][] = array('element' => new XoopsFormHidden('op', 'save'), 'required' => 0);
    $weights[0][] = 0;

    if ($xoopsUser && $xoopsUser->isAdmin()) {
        $xoopsModule->loadLanguage("admin");
        $rank_select = new XoopsFormSelect(_PROFILE_AM_RANK, "rank", $user->getVar('rank'));
        $ranklist = XoopsLists::getUserRankList();
        if ( count($ranklist) > 0 ) {
            $rank_select->addOption(0, _PROFILE_AM_NSRA);
            $rank_select->addOption(0, "--------------");
            $rank_select->addOptionArray($ranklist);
        } else {
            $rank_select->addOption(0, _PROFILE_AM_NSRID);
        }
        $elements[0][] = array('element' => $rank_select, 'required' => 0);
        $weights[0][] = 0;
        $gperm_handler =& xoops_gethandler('groupperm');
        //If user has admin rights on groups
        include_once XOOPS_ROOT_PATH."/modules/system/constants.php";
        if ($gperm_handler->checkRight("system_admin", XOOPS_SYSTEM_GROUP, $xoopsUser->getGroups(), 1)) {
            //add group selection
            $group_select = new XoopsFormSelectGroup(_PROFILE_AM_GROUP, 'groups', false, $user->getGroups(), 5, true);
            $elements[0][] = array('element' => $group_select, 'required' => 0);
            $weights[0][] = 0;
        }
    }


    // Dynamic fields
    $profile_handler =& xoops_gethandler('profile');
    // Get fields
    $fields =& $profile_handler->loadFields();
    // Get ids of fields that can be edited
    $gperm_handler =& xoops_gethandler('groupperm');
    $editable_fields =& $gperm_handler->getItemIds('profile_edit', $xoopsUser->getGroups(), $xoopsModule->getVar('mid'));

    $profile_fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
    /* @var $profile_fieldcat_handler ProfileFieldCategoryHandler */
    $profile_cat_handler =& xoops_getmodulehandler('category');
    /* @var $profile_cat_handler ProfileCategoryHandler */

    $fieldcats =& $profile_fieldcat_handler->getObjects(null, true);
    if (count($fieldcats) > 0) {
        foreach (array_keys($fieldcats) as $i) {
            $catids[] = $fieldcats[$i]->getVar('catid');
        }
        $categories =& $profile_cat_handler->getObjects(new Criteria('catid', "(".implode(',', array_unique($catids)).")", "IN"), true, false);
    }
    $fieldcat_handler =& xoops_getmodulehandler('fieldcategory');
    $fieldcats =& $fieldcat_handler->getObjects(null, true);

    foreach (array_keys($fields) as $i) {
        if (in_array($fields[$i]->getVar('fieldid'), $editable_fields)) {
            $fieldinfo['element'] = $fields[$i]->getEditElement($user);
            $fieldinfo['required'] = $fields[$i]->getVar('field_required');

            if (isset($fieldcats[$fields[$i]->getVar('fieldid')])) {
                $key = $fieldcats[$fields[$i]->getVar('fieldid')]->getVar('catid');
            }
            else {
                $key = 0;
            }
            $elements[$key][] = $fieldinfo;
            $weights[$key][] = isset($fieldcats[$fields[$i]->getVar('fieldid')]) ? intval($fieldcats[$fields[$i]->getVar('fieldid')]->getVar('field_weight')) : 1;
        }
    }
    ksort($elements);
    foreach (array_keys($elements) as $k) {
        array_multisort($weights[$k], SORT_ASC, array_keys($elements[$k]), SORT_ASC, $elements[$k]);
        $title = isset($categories[$k]) ? $categories[$k]['cat_title'] : _PROFILE_MA_DEFAULT;
        $form->insertBreak($title, 'head');
        foreach (array_keys($elements[$k]) as $i) {
            $form->addElement($elements[$k][$i]['element'], $elements[$k][$i]['required']);
        }
    }

    $form->addElement(new XoopsFormButton('', 'submit', _PROFILE_MA_SAVECHANGES, 'submit'));
    return $form;
}
?>