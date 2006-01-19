<?php
/**
 * xoops_http_CustomSessionService main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The Xoops project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3
 * @package		xoops_http
 * @subpackage	xoops_http_Session
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'xoops_http_SessionService' );

/**Only use cookies to propagate session ID*/
define( 'XO_SID_USE_COOKIE', 1 );
/**Only use URI to propagate session ID*/
define( 'XO_SID_USE_URI', 2 );
/**Try cookie and fall back to query string if not supported by the user agent*/
define( 'XO_SID_USE_ANY', 3 );

/**
 * xoops_http_CustomSessionService main class
 * @since		2.3
 * @package		xoops_http
 * @subpackage	xoops_http_Session
 */
class xoops_http_CustomSessionService extends xoops_http_SessionService {

	// ---------- Session ID options ----------
	/**
	 * Server variables used to compute the session id (comma separated)  i.e: USER_AGENT,REMOTE_ADDR
	 * @var string
	 */
	var $hashVars		= '';
	/**
	 * Hash algorithm used to generate the session IDs. '0' means MD5 (128 bits) and '1' means SHA-1 (160 bits)
	 * @var integer
	 */
	var $hashFunction	= 0;
	/**
	 * Define how many bits are stored in each character when converting the binary hash data to something readable.
	 *
	 * The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",")
	 * @var integer
	 */
	var $hashBitsPerCharacter = 4;
	/**
	 * Policy for session ID propagation
	 * @var integer
	 */
	var $sidPolicy = XO_SID_USE_COOKIE;

	// ---------- Session cookie options (for cookie SID propagation) ----------
	/**
	 * Name of the cookie used to store the session identifier
	 * @var string
	 */
	var $cookieName		= 'XOSESSIONID';
	/**
	 * Maximum allowed duration of a session (if set to 0: until the user closes its browser)
	 *
	 * Setting the cookie lifetime allows you to specify a maximum duration for your users sessions. This will make
	 * a session automatically expire after a specific time, even if its owner is still active (to remove sessions
	 * after inactivity, see garbage collection properties).
	 * @var integer
	 */
	var $cookieLifetime	= 0;
	/**
	 * Domain of the cookie
	 * @var string
	 */
	var $cookieDomain	= '';
	/**
	 * Path of the cookie
	 * @var string
	 */
	var $cookiePath		= '/';

	// ---------- Transparent SID options (for URI SID propagation) ----------
	/**
	 * Whether to enable transparent session id management if necessary (only applicable if sidPolicy allows the use of URI for SID propagation)
	 * @var string
	 */
	var $useTransparentSid = true;
	/**
	 * Tags that need rewriting by transparent sid
	 * @var string
	 */
	var $urlRewriterTags	= 'a=href,area=href,frame=src,iframe=src,input=src,form=,fieldset=';

	// ---------- Garbage collection option ----------
	/**
	 * Numbers of seconds of inactivity after which the session is considered invalid
	 * 
	 * Defaults to 15 minutes
	 * @var integer
	 */
	var $gcMaxlifetime	= 900;
	/**
	 * Probability of execution of the garbage collection routine
	 * @var integer
	 */
	var $gcProbability	= 1;
	/**
	 * Divisor used to calculate the probability of execution of the garbage collection routine
	 * @var integer
	 */
	var $gcDivisor		= 100;

	// ---------- Content caching ----------
	/**
	 * Default cache lifetime for content (in minutes)
	 * @var integer
	 */
	var $cacheLimiter = 'private_no_expire';
	/**
	 * Default cache lifetime for content (in minutes)
	 * @var integer
	 */
	var $cacheLifetime = 1;
	
	/**
	 * Property => php.ini setting lookup table
	 * @var array
	 * @access private
	 */
	var $propertyToPhp = array(
		'gcMaxlifetime'		=> 'session.gc_maxlifetime',
		'gcProbability'		=> 'session.gc_probability',
		'gcDivisor'			=> 'session.gc_divisor',
		'useTransparentSid'	=> 'session.use_trans_sid',
		'urlRewriterTags'	=> 'url_rewriter.tags',
	);
	/**
	* Initialize the session service
	*/
	function init( $options = array() ) {

		switch ($this->sidPolicy) {
		case XO_SID_USE_URI:
			ini_set( 'session.use_cookies', false );
			ini_set( 'session.use_only_cookies', false );
			break;
		case XO_SID_USE_ANY:
			ini_set( 'session.use_cookies', true );
			ini_set( 'session.use_only_cookies', false );
			break;
		case XO_SID_USE_COOKIE:
		default:
			ini_set( 'session.use_cookies', true );
			ini_set( 'session.use_only_cookies', true );
			break;
		}
	 	foreach ( $this->propertyToPhp as $prop => $php ) {
	 		if ( isset($this->$prop ) ) {
	 			ini_set( $php, $this->$prop );
	 		}
	 	}

		if ( $this->sidPolicy & XO_SID_USE_COOKIE ) {
			if ( isset( $_COOKIE[$this->cookieName] ) ) {
				$this->sessionId = $_COOKIE[$this->cookieName];
			}
			session_set_cookie_params( $this->cookieLifetime, $this->cookiePath, $this->cookieDomain );
		}
		session_name( $this->cookieName );
		session_cache_expire( $this->cacheLifetime );

		return parent::xoInit( $options );
	}
	function start() {
		if ( $id = parent::start() ) {
			$expire = $this->cookieLifetime ? ( $this->cookieLifetime + time() ) : 0;
			setcookie( $this->cookieName, $id, $expire, $this->cookiePath, $this->cookieDomain );
		}
	}
	
	/**
	 * Destroy the current session
	 */
	function destroy() {
		parent::destroy();
		if ( $this->sidPolicy & XO_SID_USE_COOKIE ) {
			setcookie( $this->cookieName, '', time() - 3600, $this->cookiePath, $this->cookieDomain );
		}
	}
}


?>