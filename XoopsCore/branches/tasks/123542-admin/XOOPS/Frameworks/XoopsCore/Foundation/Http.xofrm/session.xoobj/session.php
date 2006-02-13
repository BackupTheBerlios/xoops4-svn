<?php
/**
 * xoops_http_SessionService main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_http
 * @subpackage	xoops_http_Session
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'xoops_http' );

/**
 * Basic session service
 * 
 * The default session service provides basic session management functionality.
 * It uses the settings defined in the PHP configuration files, and thus is not really
 * user-configurable (you must use {@link xoops_http_CustomSessionService} for that).
 * 
 * Only cache limiting has been enhanced: this component allows to configure this setting
 * on a user basis (if the 'cacheLimiter' variable is stored in the session, it will be used)
 * @package		xoops_http
 * @subpackage	xoops_http_Session
 */
class xoops_http_SessionService {
	/**
	 * If sessions are created for unauthenticated users
	 * @var boolean
	 */
	var $createGuestSessions = false;
	/**
	 * Save handler to use for reading/writing session data
	 * @var object [xoops_http/xoops_http_Session/xoops_http_SessionHandler]
	 */
	var $saveHandler = 'xoops_http_DatabaseSessionHandler';
	/**
	 * Identifier of the current session
	 * @var string
	 */
	var $sessionId = 0;
	/**
	 * Name of the cookie used to store the session identifier
	 * @var string
	 */
	var $sessionName = '';

	/**#@+
	 * @vargroup 20 Client caching
	 */
	/**
	 * Default cache limiting policy (if different from the one defined in php.ini
	 * @var string
	 */
	var $cacheLimiter = 'private_no_expire';
	/**
	 * Default cache lifetime for content (in minutes)
	 * @var integer
	 */
	var $cacheLifetime = 60;
	/**#@-*/
	
	/**#@+
	 * @tasktype 10 Initialization
	 */
	/**
	* Initialize the session service
	*/
	function xoInit( $options = array() ) {
		if ( $this->saveHandler && !$this->attachHandler( $this->saveHandler ) ) {
			return false;
		}
		if ( !$this->sessionName ) {
			$this->sessionName = session_name();
		}
		// Create the session
		if ( isset( $_COOKIE[$this->sessionName] ) || $this->createGuestSessions ) {
			$this->start();
		}
	 	return true;
	}
	/**
	 * Initialize the specified object as a save handler
	 *
	 * @param mixed $handler Object to attach (or bundleIdentifier of the handler to instanciate)
	 * @return bool True on success, false otherwise
	 */
	function attachHandler( $handler ) {
		if ( !is_object( $handler ) ) {
			if ( ! ( $handler = XOS::create( $handler ) ) ) {
				trigger_error( "Failed to initialize the session save handler.", E_USER_WARNING );
				return false;
			}
		}
    	session_set_save_handler(
    		array( &$handler, 'open' ), array( &$handler, 'close'),
    		array( &$handler, 'read'), array( &$handler, 'write'),
    		array( &$handler, 'destroy'), array( &$handler, 'gc')
    	);
		$this->saveHandler =& $handler;
		return true;
	}
	/**#@-*/
	
	/**#@+
	 * @tasktype 20 Creating and destroying the session
	 */
	/**
	 * Creates a new session
	 */
	function start() {
		global $xoops;
		if ( isset($_SESSION) ) {
		  	return true;
		}
		if ( !empty($this->sessionId) ) {
			session_id( $this->sessionId );
		}
		if ( !$this->cacheLimiter ) {
			$this->cacheLimiter = session_cache_limiter();
		}
		session_cache_limiter( 'none' );
		@session_start();
		$this->sessionId = session_id();
		// Do this here to allow user-dependent cacheLimiter setting
		//if ( @$var = $this->getObjectVar( $this, 'cacheLimiter' ) ) {
		//	$this->cacheLimiter = $var;
		//}
		$xoops->services['http']->setCachingPolicy( $this->cacheLimiter, $this->cacheLifetime );
		return $this->sessionId;
	}
	/**
	* Destroys the current session
	*/
	function destroy() {
		$_SESSION = array();
		unset( $_COOKIE[ session_name() ] );
		session_destroy();
	}
	/**#@-*/
	
	/**#@+
	 * @tasktype 30 Getting and setting session variables
	 */
	/**
	 * Adds, modifies or removes an object session variable
	 * @param mixed $owner Instance or bundle identifier of the object whose variable you wish to set
	 * @param string $key The key whose value you wish to modify or remove
	 * @param mixed $value The value to set for the specified key and object. Pass NULL to remove the specified key from the session
	 * @return mixed The just set value
	 */
	function setObjectVar( $owner, $key, $value ) {
		if ( !isset($_SESSION) ) {
			return null;
		}
		if ( is_object($owner) ) {
			$owner = $owner->xoBundleIdentifier;
		}
		if ( !isset($value) ) {
			unset($_SESSION[$owner][$key]);
			return $value;
		}			
		return $_SESSION[$owner][$key] = $value;
	}
	/**
	 * Obtains the session variable value for the specified key and object
	 * @param mixed $owner Instance or bundle identifier of the object whose variable you wish to get
	 * @param string $key The key whose value you wish to obtain
	 * @return mixed The session variable value
	 */
	function getObjectVar( $owner, $key ) {
		if ( is_object($owner) ) {
			$owner = $owner->xoBundleIdentifier;
		}
		return @$_SESSION[$owner][$key];
	}
	/**#@-*/
}

/**
 * Abstract base class for session persistance handlers
 * @package		xoops_http
 * @subpackage	xoops_http_Session
 */
class xoops_http_SessionHandler {
	
}


?>