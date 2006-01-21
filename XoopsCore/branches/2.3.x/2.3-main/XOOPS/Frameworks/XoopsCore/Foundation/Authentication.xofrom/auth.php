<?php
/**
 * xoops_auth_AuthenticationService main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_auth
 * @package		xoops_auth_AuthenticationService
 * @version		$Id$
 */

/**
 * XOOPS default authentication service
 * @package		xoops_auth
 * @subpackage	xoops_auth_AuthenticationService
 */
class xoops_auth_AuthenticationService {
 	/**
	 * The authentication drivers used to check credentials (will be checked in order)
	 * @var object
	 */
	var $authDrivers = array();

	function xoInit( $options = array() ) {
		// Always check the database first
		array_unshift( $this->authDrivers, 'xoops_auth_DatabaseDriver' );
		return true;
	}

    /**
     * Check the validity of the specified user credentials
     * 
     * <p>
     * The authentication service asks every driver configured in the
     * xoops_auth_AuthenticationService::authDrivers property to check the specified
     * credentials, until one positively recognizes the user.
     * </p><p>
     * It returns the XOOPS login of the authenticated user if one is identified, or false otherwise
     * </p>
     *
     * @param string $login User name to provide to auth drivers
     * @param string $password Password to provide to auth drivers
     * @param string $hash Hash function used to encrypt the provided password (if any)
     * @return string XOOPS login of the identified user (false if all drivers failed to authenticate the user)
     */
	function checkCredentials( $login, $password, $hash = '' ) {
		// Check each auth 
		foreach ( $this->authDrivers as $k => $driver ) {
			if ( !is_object( $driver ) ) {
				if ( !$instance = XOS::create( $driver ) ) {
					trigger_error( "Cannot instanciate authentication driver $driver", E_USER_WARNING );
					unset( $this->authDrivers[$k] );
					continue;
				}
				$driver = $this->authDrivers[$k] = $instance;
			}
			if ( $xoopsLogin = $driver->checkCredentials( $login, $password, $hash ) ) {
				return ( $xoopsLogin !== true ) ? $xoopsLogin : $login;
			}
		}
		return false;
	}

}

/**
 * Base class for authentication drivers
 *
 * Authentication drivers can check given credentials using specific services or sources.
 * @package		xoops_auth
 * @subpackage	xoops_auth_AuthenticationDriver
 */
class xoops_auth_AuthenticationDriver {
	/**
	 * Hash function(s) supported by this driver
	 * @var string[]
	 */
	var $hashFunctions = array();
	/**
	 * Checks user credentials validity
	 *
	 * @var string	login		User login
	 * @var string	password	User password (optionally encrypted)
	 * @var string	hash		Name of the function used to hash the password (if any)
	 * @return mixed False if authentication failed, true if it worked, or the user login if different from the specified one
	 */
	function checkCredentials( $login, $password, $hash = '' ) {
		return false;
	}
}



?>