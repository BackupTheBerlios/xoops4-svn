<?php
// $Id$
// auth_ldap.php - LDAP authentification class
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
if (!defined("XOOPS_ROOT_PATH")) {
    die("XOOPS root path not defined");
}
/**
 * @package     kernel
 * @subpackage  auth
 * 
 * @author	    Pierre-Eric MENUET	<pemphp@free.fr>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsAuthLdap extends XoopsAuth {

    var $ldap_server;
    var $ldap_port = '389';
    var $ldap_version = '3';
    var $ldap_base_dn;
    var $ldap_uid_asdn;
    var $ldap_uid_attr;
    var $ldap_mail_attr;
    var $ldap_name_attr;
    var $ldap_surname_attr;
    var $ldap_givenname_attr;
    var $ldap_manager_dn;
    var $ldap_manager_pass;

    /**
	 * Authentication Service constructor
	 */
    function XoopsAuthLdap ($dao) {
        $this->_dao = $dao;
        //The config handler object allows us to look at the configuration options that are stored in the database
        $config_handler =& xoops_gethandler('config');    
        $config =& $config_handler->getConfigsByCat(XOOPS_CONF_AUTH);
        $confcount = count($config);
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
	 *  Authenticate  user again LDAP directory (Bind)
	 *  2 options : 
	 * 		Authenticate directly with uname in the DN
	 * 		Authenticate with manager, search the dn
	 *
	 * @param string $uname Username
	 * @param string $pwd Password
	 *
	 * @return bool
	 */	
    function authenticate($uname, $pwd = null) {
        $authenticated = false;
        if (!extension_loaded('ldap')) {
            $this->setErrors(0, 'ldap extension not loaded');
            return $authenticated;
        }
        $ds = ldap_connect($this->ldap_server, $this->ldap_port);
        if ($ds) {
            @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
            // If the uid is not in the DN we proceed to a search
            // The uid is not always in the dn
            if (!$this->ldap_uid_asdn) {
                // Bind with the manager
                if (!ldap_bind($ds, $this->ldap_manager_dn, stripslashes($this->ldap_manager_pass))) {
                    $this->setErrors(ldap_errno($ds), $this->ldap_manager_dn);
                    return $authenticated;
                }
                $sr = ldap_search($ds, $this->ldap_base_dn,$this->ldap_uid_attr."=".$uname,Array($this->ldap_mail_attr,$this->ldap_name_attr,$this->ldap_surname_attr,$this->ldap_givenname_attr));
                $info = ldap_get_entries($ds, $sr);
                if ($info["count"] > 0) {
                    $userDN = $info[0]['dn'];
                }
            }
            else {
                $userDN = $this->ldap_uid_attr."=".$uname.",".$this->ldap_base_dn;
            }
            // We bind as user
            $ldapbind = ldap_bind($ds, $userDN, stripslashes($pwd));
            if ($ldapbind) {
                $authenticated = true;
            }
            if (!$authenticated) {
                $this->setErrors(ldap_errno($ds), $userDN);
            }
            @ldap_close($ds);
        }
        else {
            $this->setErrors(0, "Could not connect to LDAP server.");
        }
        return $authenticated;
    }

} // end class

?>