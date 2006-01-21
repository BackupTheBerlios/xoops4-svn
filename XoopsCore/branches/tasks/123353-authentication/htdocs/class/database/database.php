<?php
// $Id$
// database.php - defines abstract database wrapper class 
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
/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_ROOT_PATH' ) ) exit();


/**
 * ---------------------------------------
 * DONT USE THESE !
 * ---------------------------------------
 * Database instances should be created with XOS::create( 'xoops_db_Database' ),
 * if you need a database object implementing the XOOPS 2.0.x database class
 * methods, you can get a reference to the legacydb service using $xoops->loadService()
 */


/**
 * make sure this is only included once!
 */
if ( !defined("XOOPS_C_DATABASE_INCLUDED") ) {
	define("XOOPS_C_DATABASE_INCLUDED",1);

class XoopsDatabase {
	/**
	 * constructor
     * 
     * will always fail, because this is an abstract class!
	 */
	function XoopsDatabase()
	{
		// exit("Cannot instantiate this class directly");
	}

	/**
	 * assign a {@link XoopsLogger} object to the database
	 * 
     * @see XoopsLogger
     * @param object $logger reference to a {@link XoopsLogger} object
	 */
	function setLogger(&$logger)
	{
		$this->logger =& $logger;
	}

	/**
	 * set the prefix for tables in the database
	 * 
     * @param string $value table prefix
	 */
	function setPrefix($value)
	{
		$this->prefix = $value;
	}
	
	/**
	 * attach the prefix.'_' to a given tablename
     * 
     * if tablename is empty, only prefix will be returned
	 * 
     * @param string $tablename tablename
     * @return string prefixed tablename, just prefix if tablename is empty
	 */
	function prefix($tablename='')
	{
		if ( $tablename != '' ) {
			return $this->prefix .'_'. $tablename;
		} else {
			return $this->prefix;
		}
	}
}

class XoopsDatabaseFactory {
	/**
	 * Get a reference to the only instance of database class and connects to DB
	 * @deprecated
	 */
	function &getDatabaseConnection() {
		return $GLOBALS['xoops']->loadService( 'legacydb' );
	}
	/**
	 * Gets a reference to the only instance of database class.
	 * @deprecated
	 */
	function &getDatabase() {
		static $database;
		if (!isset($database)) {
			$file = XOOPS_ROOT_PATH.'/class/database/'.XOOPS_DB_TYPE.'database.php';
			require_once $file;
			if (!defined('XOOPS_DB_PROXY')) {
				$class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseSafe';
			} else {
				$class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseProxy';
			}
			$database =& new $class();
		}
		return $database;
	}
}


/**
 * @deprecated
 */
class Database {
	function &getInstance() {
		return $GLOBALS['xoops']->loadService( 'legacydb' );
	}
}


}

?>