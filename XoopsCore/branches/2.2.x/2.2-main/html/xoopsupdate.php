<?php
include "mainfile.php";
if (isset($_POST['submit'])) {
    //Check for duplicate configuration items:
    $sql = "SELECT c1.conf_id FROM ".$xoopsDB->prefix('config')." c1, ".$xoopsDB->prefix('config')." c2 WHERE
                c1.conf_modid=c2.conf_modid AND c1.conf_name=c2.conf_name AND c1.conf_id > c2.conf_id";
    $result = $xoopsDB->query($sql);
    while (list($id) = $xoopsDB->fetchRow($result)) {
        $ids[] = $id;
    }
    if (count($ids) > 0) {
        $criteria = new Criteria('conf_id', "(".implode(',', $ids).")", "IN");
        $config_handler = xoops_gethandler('config');
        $configs = $config_handler->getConfigs($criteria);
        foreach (array_keys($configs) as $i) {
            $config_handler->deleteConfig($configs[$i]);
        }
    }

    //Update system
    include XOOPS_ROOT_PATH."/modules/system/include/update.php";
    $module_handler = xoops_gethandler('module');
    $module = $module_handler->getByDirname("system");

    include XOOPS_ROOT_PATH."/modules/system/language/english/admin/modulesadmin.php";
    $module->update();

    $criteria = new CriteriaCompo(new Criteria("hasconfig", 1));
    $criteria->add(new Criteria('dirname', "system", "!="));
    $modules_with_config = $module_handler->getObjects($criteria);
    unset($criteria);
    if (count($modules_with_config) > 0) {
        $msgs = array();
        foreach (array_keys($modules_with_config) as $i) {
            $modules_with_config[$i]->insertConfigCategories();
            $modules_with_config[$i]->insertConfig();
            $msgs = array_merge($msgs, $modules_with_config[$i]->getMessages());
        }
        echo implode('<br />', $msgs);
    }

}
else {
    $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix('config')." SET conf_modid=1 WHERE conf_modid=0"); //Shouldn't really be run several times, but it doesn't hurt anything since there will be no configs with conf_modid=0
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $form = new XoopsThemeForm('Update XOOPS', 'form', 'xoopsupdate.php');
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->display();
}
include "footer.php";
?>