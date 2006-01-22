<?php
// Pre treatment: detect module status
function xoops_module_pre_install_pm(&$module) {
	$PM_tables = $module->getInfo("tables");
	$PM_table = $PM_tables[0];
	
	$sql = "SHOW COLUMNS FROM ".$GLOBALS["xoopsDB"]->prefix($PM_table);
	$level = ($result = $GLOBALS["xoopsDB"]->queryF($sql)) ? 1 : 0;
	if ($level) {
	    $fields = array();
	    while (list($field) = $GLOBALS["xoopsDB"]->fetchRow($result)) {
	        if (in_array($field, array("from_delete", "from_save", "to_delete", "to_save"))) {
		        $level = 2; // XOOPS 2.2+, no need for table change
		        break;
	        }
	    }
	}
	
	if($level==0){ // fresh install
		$module->setInfo("sqlfile", array('mysql' => "sql/mysql.sql"));
	}
	$module->setInfo("level", $level);
	
	return true;	
}

function xoops_module_install_pm(&$module) 
{
    $sql = "UPDATE ".$GLOBALS['xoopsDB']->prefix('user_profile')." SET pm_link=".$GLOBALS['xoopsDB']->quoteString(
    "<a href=\"javascript:openWithSelfMain('{X_URL}/modules/pm/pmlite.php?send2=1&to_userid={X_UID}', 'pmlite', 550, 450);\" title=\""._PM_MI_MESSAGE." {X_UNAME}\"><img src=\"{X_URL}/modules/pm/images/pm.gif\" alt=\""._PM_MI_MESSAGE." {X_UNAME}\" /></a>"
    )." WHERE pm_link=''";
    if (!$GLOBALS['xoopsDB']->query($sql)) {
        $module->setMessage($GLOBALS['xoopsDB']->error());
    }else{
        $module->setMessage("pm_link data updated");
    }
	// Post treatment: upgrade table if pre 2.2	
	$level = $module->getInfo("level");
	$PM_tables = $module->getInfo("tables");
	$PM_table = $PM_tables[0];
	if($level==1){
	    $sql = "ALTER TABLE ".$GLOBALS['xoopsDB']->prefix($PM_table)." ADD from_delete TINYINT( 1 ) UNSIGNED NOT NULL default '0' , ADD to_delete TINYINT( 1 ) UNSIGNED NOT NULL default '0', ADD to_save TINYINT( 1 ) UNSIGNED NOT NULL default '0', ADD from_save TINYINT( 1 ) UNSIGNED NOT NULL default '0';";
	    return $GLOBALS['xoopsDB']->query($sql);
	}else{
		return true;
	}
}
?>