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

/**
 * xoops_http_SessionService main class
 * 
 * The default session service provides basic session management functionality.
 * It uses the settings defined in the PHP configuration files, and thus is not really
 * user-configurable (you must use xoops_http_CustomSessionService for that).
 * 
 * Only cache limiting has been enhanced: this component allows to configure this setting
 * on a user basis (if the 'cacheLimiter' variable is stored in the session, it will be used)
 */
class xoops_http_SessionService {
	/**
	* Save handler to use for reading/writing session data
	* @var mixed
	*/
	var $saveHandler = '';
	/**
	 * Identifier of the current session
	 * @var string
	 */
	var $sessionId = 0;
	/**
	 * Default cache limiting policy (if different from the one defined in php.ini
	 * @var string
	 */
	var $cacheLimiter = '';
	
	/**
	* Initialize the session service
	*/
	function xoInit( $options = array() ) {
		if ( $this->saveHandler && !$this->attachHandler( $this->saveHandler ) ) {
			return false;
		}
		$this->start();
	 	return true;
	}
	/**
	 * Initialize the specified object as a save handler
	 *
	 * @param mixed $handler Object to attach (or bundleIdentifier of the handler to instanciate)
	 * @return boolean true on success, false otherwise
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
	
	/**
	* Create a new session
	*/
	function start() {
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
		if ( @$var = $this->getObjectVar( $this, 'cacheLimiter' ) ) {
			$this->cacheLimiter = $var;
		}
		$this->resetCacheLimiting( $this->cacheLimiter, 60 * session_cache_expire() );
		return $this->sessionId;
	}
	/**
	* Destroy the current session
	*/
	function destroy() {
		$_SESSION = array();
		session_destroy();
	}
	/**
	* Set a named session variable
	*/
	function setObjectVar( $owner, $key, $value ) {
		if ( !isset($_SESSION) ) {
			return null;
		}
		if ( is_object($owner) ) {
			$owner = $owner->xoBundleIdentifier;
		}
		return $_SESSION[$owner][$key] = $value;
	}
	/**
	* Get the value of a named session variable
	*/
	function getObjectVar( $owner, $key ) {
		if ( is_object($owner) ) {
			$owner = $owner->xoBundleIdentifier;
		}
		return @$_SESSION[$owner][$key];
	}
	/**
	 * Resend the cache limiting headers
	 */
	function resetCacheLimiting( $policy, $cacheLifetime = 0, $lastModified = null ) {
		if ( !isset( $lastModified ) ) {
			$lastModified = time();
		}
		switch ( $policy ) {
		case 'none':
			break;
		case 'no-cache':
			header( 'Expires: ' . $this->date( time() - 3600 ) );
			header( 'Cache-Control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0' );
			break;
		case 'private':
			header( 'Expires: ' . $this->date( time() - 3600 ) );
		case 'private_no_expire':
			header( "Cache-Control: private,max-age=$cacheLifetime,pre-check=$cacheLifetime" );
			break;
		case 'public':
		default:
			header( 'Expires: ' . $this->date( time() + $cacheLifetime ) );
			header( "Cache-Control: public,max-age=$cacheLifetime" );
			header( "Last-Modified: " . $this->date( $lastModified ) );
			break;
		}
	}
	/**
	 * Return an RFC compliant (RFC822 or RFC850) date string from a timestamp
	 *
	 * @param interger $time
	 * @return string
	 */	
	function date( $time = null ) {
        return gmdate(
        	( ini_get('y2k_compliance') ? 'D, d M Y' : 'l, d-M-y' ) .' H:i:s',
        	isset($time) ? $time : time()
        ) . ' GMT';
    }

}


?>