<?php
/**
 * xoops_kernel_User main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_kernel
 * @subpackage	xoops_kernel_User
 * @version		$Id$
 */


/**This user account has been disabled*/
define( 'XO_LEVEL_DISABLED', -1 );
define( 'XO_LEVEL_ANONYMOUS', 0 );
define( 'XO_LEVEL_INACTIVE', 1 );
define( 'XO_LEVEL_REGISTERED', 2 );
define( 'XO_LEVEL_ADMIN', 256 );

/**
 * Base class for system users
 * @package		xoops_kernel
 * @subpackage	xoops_kernel_User
 */
class xoops_kernel_User {
	
	var $userId = 0;

	var $level = XO_LEVEL_ANONYMOUS;
	var $groups = array( XOOPS_GROUP_ANONYMOUS );
	
	var $login = 'anonymous';
	var $password = '';
	
	var $email = '';
	
	var $fullName = '';

}


?>