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
if (!defined("XOOPS_ROOT_PATH")) {
    die("XOOPS root path not defined");
}
/**
 * @package kernel
 * @copyright copyright &copy; 2000 XOOPS.org
 */
class ProfileFieldCategory extends XoopsObject {
    function ProfileFieldCategory() {
        $this->initVar('fieldid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('catid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('field_weight', XOBJ_DTYPE_INT, 1);
    }
}

/**
 * @package kernel
 * @copyright copyright &copy; 2000 XOOPS.org
 */
class ProfileFieldCategoryHandler extends XoopsPersistableObjectHandler {
    function ProfileFieldCategoryHandler(&$db) {
        //A field can be in only one category, so fieldid can be used to uniquely identify a row
        $this->XoopsPersistableObjectHandler($db, 'profile_fieldcategory', "ProfileFieldCategory", "fieldid");
    }
    
    /**
    * Get array of categories with at least one field in them
    *
    * @return array
    **/
    function getActiveCats() {
        $ret = array();
        $sql = "SELECT DISTINCT catid FROM ".$this->table;
        $result = $this->db->query($sql);
        while (list($catid) = $this->db->fetchRow($result)) {
            $ret[] = $catid;
        }
        return $ret;
    }
}
?>