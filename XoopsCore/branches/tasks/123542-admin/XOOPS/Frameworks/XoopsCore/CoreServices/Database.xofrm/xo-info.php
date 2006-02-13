<?php
/**
 * xoops_db framework bundle information file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_db
 * @version		$Id$
 */

return array(
	'xoBundleDisplayName' => 'XOOPS Database framework',
	'xoBundleIdentifier' => 'xoops_db',
	'xoServices' => array(
		'xoops_db_DatabaseFactory' => array(
			'xoBundleDisplayName' => 'Local factory for xoops_db database instances',
			'xoClassPath' => '/database.php',
		),
		'xoops_db_Database' => array(
			'xoFactory' => 'xoops_db_DatabaseFactory',
			'xoBundleDisplayName' => 'Connection to a database',
			'xoClassPath' => '/database.php',
		),
		'xoops_db_Database_mysql' => array(
			'xoBundleDisplayName' => 'XOOPS MySQL database driver (PDO compatible)',
			'xoClassPath' => '/mysql.php',
		),
		'xoops_db_Statement_mysql' => array(
			'xoBundleDisplayName' => 'XOOPS MySQL driver prepared statement',
			'xoClassPath' => '/mysql.php',
		),
		'xoops_db_Database_legacy' => array(
			'xoBundleDisplayName' => 'XOOPS Legacy (XOOPS 2.0) database driver',
			'xoClassPath' => '/legacy.php',
		),
	),
);

?>