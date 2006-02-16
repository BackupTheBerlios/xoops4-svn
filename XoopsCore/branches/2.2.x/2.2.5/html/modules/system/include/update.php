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
function xoops_module_update_system(&$module) {
    $oldversion = $module->getVar('version');
    /* @var $module XoopsModule */
    $config_handler =& xoops_gethandler('config');
    /* @var $config_handler XoopsConfigHandler */
    global $xoopsDB;
    if ($oldversion < 201) {
        $criteria = new Criteria('conf_name', 'debug_mode');

        $config =& $config_handler->getConfigs($criteria);
        $value = array($config[0]->getConfValueForOutput());
        $config[0]->setVar('conf_formtype', 'select_multi');
        $config[0]->setVar('conf_valuetype', 'array');
        $config[0]->setConfValueForInput($value);
        if (!$config_handler->insertConfig($config[0])) {
            $module->setMessage("Could not insert debug mode config");
        }
        unset($config);
    }
    if ($oldversion < 202) {
        $sql = "ALTER TABLE ".$GLOBALS['xoopsDB']->prefix('config')." CHANGE `conf_id` `conf_id` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage("Could not modify config table");
        }
    }

    if ($oldversion < 203) {

        $profile_handler =& xoops_gethandler('profile');
        $user_handler =& xoops_gethandler('user');
        $sql = "INSERT INTO ".$profile_handler->table."
                (profileid, user_icq, user_from, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_occ, url, bio, user_intrest, umode, uorder, notify_method, notify_mode, user_regdate, posts, attachsig, timezone_offset, user_mailok, theme, actkey, last_login)
                SELECT uid, user_icq, user_from, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_occ, url, bio, user_intrest, umode, uorder, notify_method, notify_mode, user_regdate, posts, attachsig, timezone_offset, user_mailok, theme, actkey, last_login FROM ".$user_handler->table;
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
            $module->setMessage($sql);
        }
        else {
            $sql = "ALTER TABLE ".$user_handler->table."
                  DROP `user_icq`,
                  DROP `user_from`,
                  DROP `user_sig`,
                  DROP `user_viewemail`,
                  DROP `user_aim`,
                  DROP `user_yim`,
                  DROP `user_msnm`,
                  DROP `user_occ`,
                  DROP `url`,
                  DROP `bio`,
                  DROP `user_intrest`,
                  DROP `umode`,
                  DROP `uorder`,
                  DROP `notify_method`,
                  DROP `notify_mode`,
                  DROP `user_regdate`,
                  DROP `posts`,
                  DROP `attachsig`,
                  DROP `timezone_offset`,
                  DROP `user_mailok`,
                  DROP `theme`,
                  DROP `actkey`,
                  DROP `last_login`";
            if (!$xoopsDB->query($sql)) {
                $module->setMessage($xoopsDB->error());
            }
        }
        $sql = "ALTER TABLE ".$xoopsDB->prefix("block_module_link")." DROP INDEX  `block_id` ";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
        $sql = "ALTER TABLE ".$xoopsDB->prefix("block_module_link")." DROP INDEX  `module_id` ";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
    }
    if ($oldversion < 209) {
        $sql = "ALTER TABLE ".$xoopsDB->prefix("block_module_link")." DROP PRIMARY KEY ";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
        $sql = "ALTER TABLE ".$xoopsDB->prefix("block_module_link")." ADD pageid smallint(5) NOT NULL ";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
        $sql = "ALTER TABLE ".$xoopsDB->prefix("block_module_link")." ADD PRIMARY KEY ( `block_id` , `module_id` , `pageid` )";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
    }

    if ($oldversion < 210) {
        $sql = "ALTER TABLE ".$xoopsDB->prefix("users")." CHANGE `level` `level` TINYINT( 3 ) NOT NULL";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
    }
    if ($oldversion < 211) {
        $sql = "ALTER TABLE ".$xoopsDB->prefix("config")." CHANGE `conf_desc` `conf_desc` VARCHAR( 50 ) NOT NULL";
        if (!$xoopsDB->query($sql)) {
            $module->setMessage($xoopsDB->error());
        }
    }
    if ($oldversion < 212) {
        $result=$xoopsDB->queryF("SHOW COLUMNS FROM	".$xoopsDB->prefix("users")." LIKE 'loginname'");
        if ($xoopsDB->getRowsNum($result) == 0) {
            $sql = "ALTER TABLE ".$xoopsDB->prefix("users")." ADD `loginname` varchar(25) NOT NULL DEFAULT '' AFTER `uname`";
            if (!$xoopsDB->query($sql)) {
                $module->setMessage($xoopsDB->error());
            }else{
                $module->setMessage("loginname field added");
            }
            $sql = "ALTER TABLE ".$xoopsDB->prefix("users")." CHANGE `uname` `uname` varchar(55) NOT NULL DEFAULT ''";
            if (!$xoopsDB->query($sql)) {
                $module->setMessage($xoopsDB->error());
            }else{
                $module->setMessage("uname field updated");
            }
            $sql = "UPDATE ".$xoopsDB->prefix('users')." SET loginname=uname WHERE loginname=''";
            if (!$xoopsDB->query($sql)) {
                $module->setMessage($xoopsDB->error());
            }else{
                $module->setMessage("loginname data updated");
            }
        }
    }
    // Remove blocks and templates with invalid module ID references - should this always be run or just once?
    // We'll do it on every System update now - just in case.
    $module_handler =& xoops_gethandler('module');
    $sql = "SELECT mid, dirname FROM ".$module_handler->table;
    if ($result = $xoopsDB->query($sql)) {
        $available_mids = array();
        while (list($mid, $dirname) = $xoopsDB->fetchRow($result)) {
            $available_mids[] = $mid;
            $available_dirnames[] = $dirname;
        }
        if (count($available_mids) > 0) {
            $block_handler =& xoops_gethandler('block');
            $blocks_with_invalid_mid = $block_handler->getObjects(new Criteria('mid NOT', "(".implode(',', $available_mids).")", "IN"));
            if (count($blocks_with_invalid_mid) > 0) {
                foreach (array_keys($blocks_with_invalid_mid) as $i) {
                    if (!$block_handler->delete($blocks_with_invalid_mid[$i])) {
                        $module->setMessage("Could not delete ".$blocks_with_invalid_mid[$i]->getVar('name'));
                    }
                    else {
                        $module->setMessage($blocks_with_invalid_mid[$i]->getVar('name'). " Deleted");
                    }
                }
            }
            $tplfile_handler =& xoops_gethandler('tplfile');
            $tplfiles_with_invalid_mid = $tplfile_handler->getObjects(new Criteria('tpl_module NOT', "(".implode(',', array_map(array($xoopsDB, "quoteString"), $available_dirnames)).")", "IN"));
            if (count($tplfiles_with_invalid_mid) > 0) {
                foreach (array_keys($tplfiles_with_invalid_mid) as $i) {
                    if (!$tplfile_handler->delete($tplfiles_with_invalid_mid[$i])) {
                        $module->setMessage("Could not delete ".$tplfiles_with_invalid_mid[$i]->getVar('tpl_file'));
                    }
                    else {
                        $module->setMessage($tplfiles_with_invalid_mid[$i]->getVar('tpl_file'). " Deleted");
                    }
                }
            }
        }
    }

    /**
    * update default value for some profile fields according to system preferences
    * comments mode and order, default timezone and theme is set to the same values as in general settings
    */
    $module->setMessage('Updating user profile field default value...');
    $profile_handler =& xoops_gethandler('profile');
    $fields = $profile_handler->loadFields();
    $fields_update = array("umode"=>"com_mode", "uorder"=>"com_order", "timezone_offset"=>"default_TZ");
    $count = 0;
    foreach (array_keys($fields) as $i) {
        if ($fields[$i]->getVar('field_moduleid') == $module->getVar('mid')){
         	$field_name = $fields[$i]->getVar('field_name');
         	if (!in_array($field_name, array_keys($fields_update))) {
         	    continue;
         	}
        	$count++;
	        $default = $GLOBALS["xoopsConfig"][$fields_update[$field_name]];
	        if ($default == $fields[$i]->getVar("field_default")) {
	            continue;
	        }
	        $fields[$i]->setVar("field_default", $default);
        	if ($profile_handler->insertField($fields[$i])) {
        	    $module->setMessage("&nbsp;&nbsp;Field <strong>".$field_name."</strong> default value updated: ".$default);
        	}
        }
        if ($count>=count($fields_update)) {
            break;
        }
    }
    unset($fields);

    return true;
}

function xoops_module_pre_update_system(&$module) {
    $oldversion = $module->getVar('version');
    if ($oldversion < 206) {
        $sql = "ALTER TABLE ".$GLOBALS['xoopsDB']->prefix("session")." CHANGE sess_data sess_data MEDIUMBLOB NOT NULL";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not update session data field in session table');
        }
        $sql = "ALTER TABLE ".$GLOBALS['xoopsDB']->prefix("configcategory")." ADD `confcat_nameid` VARCHAR( 50 ) NOT NULL";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not add nameid field in configcategory table');
        }
        $sql = "ALTER TABLE ".$GLOBALS['xoopsDB']->prefix("configcategory")." ADD `confcat_description` VARCHAR( 255 ) NOT NULL";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not add description field in configcategory table');
        }
        $sql = "ALTER TABLE ".$GLOBALS['xoopsDB']->prefix("configcategory")." ADD `confcat_modid` smallint( 5 ) NOT NULL";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not add module id field in configcategory table');
        }
    }
    if ($oldversion < 208) {
        $configcat_handler =& xoops_gethandler('configcategory');
        $sql = "ALTER TABLE ".$configcat_handler->table." CHANGE `confcat_id` `confcat_id` SMALLINT( 5 ) UNSIGNED NOT NULL";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not change confcat_id properties');
        }
        $sql = "ALTER TABLE ".$configcat_handler->table." DROP PRIMARY KEY";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not drop confcat primary key');
        }
        $sql = "ALTER TABLE ".$configcat_handler->table." ADD PRIMARY KEY ( `confcat_id` , `confcat_modid` )";
        if (!$GLOBALS['xoopsDB']->query($sql)) {
            $module->setMessage('Could not add new primary key');
        }
    }
    if ($oldversion < 203) {
        global $xoopsDB;
        //add profile tables by running upgrade.sql
        $xoopsDB->queryFromFile(XOOPS_ROOT_PATH."/modules/system/sql/upgrade.sql");

        //make sure that the profilefields.tmp cache file is NOT there
        $profile_handler =& xoops_gethandler('profile');
        $profile_handler->updateCache();

        //insert profile module
        $module_handler =& xoops_gethandler('module');
        $profile_module =& $module_handler->create();
        $profile_module->setVar('dirname', 'profile');
        echo $profile_module->install(array(XOOPS_GROUP_ADMIN), array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
    }
    if ($oldversion < 205) {
        //create block instance table
        $GLOBALS['xoopsDB']->queryFromFile(XOOPS_ROOT_PATH."/modules/system/sql/upgrade205.sql");
        //Upgrade block options to serialized array
        $instance_handler =& xoops_gethandler('blockinstance');
        $block_handler =& xoops_gethandler('block');

        //Need to use a direct query because we need to access outdated columns
        $sql = "SELECT * FROM ".$block_handler->table;
        $result = $GLOBALS['xoopsDB']->query($sql);

        while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
            $block =& $block_handler->create(false);
            //Create block instance
            $instance =& $instance_handler->create();
            //Set block id value for the block object
            $block->setVar('bid', $row['bid']);
            if ($row["block_type"] != "C" ) {
                $block->setVar('options', explode('|', $row['options']));
                $block->setVar('name', $row['name']);
                $block->setVar('mid', $row['mid']);
                $block->setVar('func_file', $row['func_file']);
                $block->setVar('show_func', $row['show_func']);
                $block->setVar('edit_func', $row['edit_func']);
                $block->setVar('template', $row['template']);
                $block->setVar('dirname', $row['dirname']);
                $block->setVar('isactive', $row['isactive']);
                $block->setVar('last_modified', time());
                //Set instance's block id
                $instance->setVar('bid', $block->getVar('bid'));
            }
            else {
                if (!isset($customblockid)) {
                    $customblockid = $row['bid'];
                }
                //Set instance's block id to the custom block's id
                $instance->setVar('bid', $customblockid);

                $block->setVar('options', array(0 => addslashes($row['content']), 1 => $row['c_type']));
                $block->setVar('name', _MI_SYSTEM_BNAME14);
                $block->setVar('mid', $module->getVar('mid'));
                $block->setVar('func_file', 'system_blocks.php');
                $block->setVar('show_func', 'b_system_custom_show');
                $block->setVar('edit_func', 'b_system_custom_edit');
                $block->setVar('template', 'system_block_dummy.html');
                $block->setVar('dirname', 'system');
                $block->setVar('isactive', 1);
                $block->setVar('last_modified', time());
            }
            $block_handler->insert($block);

            $instance->setVar('instanceid', $block->getVar('bid')); //to keep group permissions
            $instance->setVar('options', $block->getVar('options'));
            $instance->setVar('title', $row['title']);

            $side = $row['visible'] ? $row['side'] : 0;
            $instance->setVar('side', $side);
            $instance->setVar('weight', $row['weight']);
            $instance->setVar('visible', $row['visible']);
            $instance->setVar('bcachetime', $row['bcachetime']);
            $instance_handler->insert($instance);
            unset($instance);
            unset($block);
        }

        //Remove custom block types that are no longer used
        $criteria = new CriteriaCompo(new Criteria('block_type', "C"));
        if (isset($customblockid)) {
            $criteria->add(new Criteria("bid", $customblockid, "!="));
        }
        $block_handler->deleteAll($criteria);
        unset($criteria);

        //Remove duplicate blocks
        $sql = "SELECT b.bid AS newid, b2.bid AS oldid FROM ".$block_handler->table." b, ".$block_handler->table." b2
                WHERE b.show_func = b2.show_func AND b.edit_func = b2.edit_func AND b.bid != b2.bid";
        $result = $GLOBALS['xoopsDB']->query($sql);
        $ids = array();
        while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
            if (!isset($ids[$row['oldid']])) { //if "old id" is not set as the new joint id
                $ids[$row['newid']][] = $row['oldid'];
            }
        }

        if (count($ids) > 0) {
            foreach ($ids as $newid => $oldids) {
                $criteria = new Criteria('bid', "(".implode(',', $oldids).")", 'IN');
                $instance_handler->updateAll('bid', $newid, $criteria); //set bid for instances to the new joint id
                $block_handler->deleteAll($criteria); //remove old blocks
                unset($criteria);
            }
        }

        //Remove unneeded columns from newblocks table
        $GLOBALS['xoopsDB']->query("ALTER TABLE ".$block_handler->table." DROP INDEX  `isactive_visible_mid` ");
        $GLOBALS['xoopsDB']->query("ALTER TABLE ".$block_handler->table." DROP INDEX  `visible` ");
        $GLOBALS['xoopsDB']->query("ALTER TABLE ".$block_handler->table." DROP INDEX  `mid_funcnum` ");
        $sql = "ALTER TABLE ".$block_handler->table."
                DROP `func_num`,
                DROP `title`,
                DROP `content`,
                DROP `side`,
                DROP `weight`,
                DROP `visible`,
                DROP `block_type`,
                DROP `c_type`,
                DROP `bcachetime`";
        $GLOBALS['xoopsDB']->query($sql);
        $GLOBALS['xoopsDB']->query("ALTER TABLE ".$block_handler->table." ADD INDEX `active` (`bid`, `isactive`) ");
    }

    if ($oldversion < 207) {
        $configcat_handler =& xoops_gethandler('configcategory');
        $GLOBALS['xoopsDB']->query("UPDATE ".$configcat_handler->table." SET confcat_modid=1 WHERE confcat_modid=0 ");
        $userconfigcategory =& $configcat_handler->get(array(2, 1));
        if (!$configcat_handler->delete($userconfigcategory)) {
            $module->setMessage("Could not delete user configuration category");
        }
    }
    return true;
}
?>