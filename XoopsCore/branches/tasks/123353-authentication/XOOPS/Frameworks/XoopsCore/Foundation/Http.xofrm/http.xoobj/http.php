<?php
/**
* xoops_http_HttpHandler component main class definition
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright	The XOOPS project http://www.xoops.org/
* @license		http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since		2.3.0
* @package		xoops_http
* @subpackage	xoops_http_HttpHandler
* @version		$Id$
*/


/**
 * xoops_http_HttpHandler component
 *
 * The HttpHandler component encapsulates the HTTP response manipulation functions
 * (redirection for now, and more later on)
 */
class xoops_http_HttpHandler {
	/**
	* Whether HTTP redirections are enabled or not
	* @var bool
	*/
	var $enableRedirections	= true;
	/**
	* How many seconds the fake redirection page should be shown
	* @var integer
	*/
	var $fakeRedirectPageDelay = 3;

	/**
	 * Fake redirection page content template
	 * 
	 * <p>When HTTP redirections are disabled, this string is returned to the client.</p>
	 * <p>This page can contain any of the following, and they will be replaced before the page is sent:<br />
	 * <ul>
	 * <li>{message}: The redirection message</li>
	 * <li>{refresh}: A &lt;meta http-equiv="refresh"&gt; tag that ensures client-side redirection is performed</li>
	 * </ul>
	 *
	 * @var string
	 */
	var $fakeRedirectTemplate = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	
<head>{refresh}</head>
<body>
	<div style="text-align:center; background-color: #EBEBEB; border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-right: 1px solid #AAAAAA; border-bottom: 1px solid #AAAAAA; font-weight : bold;">
		<h4>{message}</h4><p>{ifnotreload}</p>
	</div>
	<!--{xo-logger-output}-->
</body></html>';
	/**
	* Default strings to add to the response status header (indexed by status code)
	* @var array
	*/
	var $responseMessages = array(
		200			=> 'OK',
		400			=> 'Bad request',
		404			=> 'Not found',
	);
	/**
	* Location of the default error pages (unimplemented)
	* @var string
	*/
	var $defaultErrorPages = false;
	/**
	* Redirection message sent by the last page
	* @var string
	*/
	var $redirectMessage = '';


	function xoInit( $options = array() ) {
		if ( !empty( $_SESSION[$this->xoBundleIdentifier]['redirect_info'] ) ) {
			foreach ( $_SESSION[$this->xoBundleIdentifier]['redirect_info'] as $k => $v ) {
				$_SERVER[ 'REDIRECT_' . $k ] = $v;
			}
			//var_export( $_SESSION[$this->xoBundleIdentifier]['redirect_info'] );
			unset( $_SESSION[$this->xoBundleIdentifier]['redirect_info'] );
			$this->redirectMessage = $_SERVER['REDIRECT_ERROR_NOTES'];
		}
		if ( @$_SESSION[$this->xoBundleIdentifier]['tmpDisallowRedirections'] ) {
			$this->enableRedirections = false;
		}
		header( 'X-Powered-By: ' . $GLOBALS['xoops']->xoShortVersionString, false );
		header( 'X-Powered-By: The noosphere', false );
		return true;
	}

	/**
	 * Convert a relative URI to an absolute one
	 */
	function absoluteUrl( $url ) {
		global $xoops;
		static $host;	

		if ( strpos( $url, '://' ) === false ) {
			if ( !isset($host) ) {
				list( $defaultPort, $proto ) =
					( substr( $_SERVER['SERVER_PROTOCOL'], 4, 1 ) == '/' ) ? array( 80, 'http://' ) : array( 443, 'https://' );
				if ( !$host = @$_SERVER['HTTP_HOST'] ) {
					$host = !empty($this->defaultServerName) ? $this->defaultServerName : $_SERVER['SERVER_NAME'];
				}
				if ( !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != $defaultPort ) {
					$host .= ':' . $_SERVER['SERVER_PORT'];
				}
				$host = $proto . $host;
			}
	        if ( !strlen($url) ) {
	            $url = $_SERVER['REQUEST_URI'];
        	} elseif ( substr( $url, 0, 1 ) == '/' ) {
	            return $host . $xoops->url( "/www$url" );
        	}
        	// Check for PATH_INFO
        	if (isset($_SERVER['PATH_INFO']) && $_SERVER['PHP_SELF'] != $_SERVER['PATH_INFO']) {
            	$path = dirname(substr($_SERVER['PHP_SELF'], 0, -strlen($_SERVER['PATH_INFO'])));
        	} else {
            	$path = dirname($_SERVER['PHP_SELF']);
        	}
        	if (substr($path = strtr($path, '\\', '/'), -1) != '/') {
            	$path .= '/';
        	}
			$url = $host . $path . $url;
		}
		return $url;
	}
	/**
	 * Set/send the response status and redirection location
	 * 
	 * This set the status response headers, and optionally redirect the client to the 
	 * specified location.
	 * The way the redirection is performed depends on the value of the $enableRedirections property:
	 * if true, a Location: header is sent
	 * if false, a custom html page containing a refresh &lt;meta&gt; tag will be displayed to perform client-side
	 * redirection
	 * 
	 * @param int $status The HTTP status code to send to the client
	 * @param string $message The HTTP status message to send to the client
	 * @param string $location Location to redirect the client to
	 */
	function sendResponse( $status = null, $message = '', $location = '' ) {
		global $xoops;

 	 	if ( is_array($status) ) {
	 		list( $status, $message, $location ) = $status;
		}
		if ( empty($status) ) {
			$status = 200;			// HTTP_STATUS_OK
		}
		if ( empty($message) && isset($this->errorMessages[$status]) ) {
		 	$message =  $this->errorMessages[$status];
		}
		/*
		if ( empty($location) && isset($this->errorPages[$status]) ) {
			$location = $this->errorPages[$status];
		}
		*/
		if ( $location == -1 ) {
		 	if ( !( $location = @$_SERVER['HTTP_REFERER'] ) ) {
		 		$location = '/www/';
			}
		}
		if ( !empty($location) && !$xoops->isVirtual ) {
		    if ( preg_match( "/[\\0-\\31]|about:|script:/i", $location ) ) {
        		$location = '/www/';
    		}
    		if ( !strpos( $location, '://' ) ) {
				$location = $this->absoluteUrl( $xoops->url( $location ) );
    		}
			if ( $this->enableRedirections ) {
				$_SESSION[$this->xoBundleIdentifier]['redirect_info'] = array(
					'STATUS' => $status,
					'URL' => $_SERVER['REQUEST_URI'],
					'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
					'ERROR_NOTES' => $message,
				);
				header( "Location: $location" );
				$this->sendResponseCode( $status, $message );
				echo "<body><a href='$location'>Page redirected. Status: $status, $message.</a></body>";
			} else {
				$this->sendResponseCode( 200, $message );
				$vars = array(
					'{refresh}' => '<meta http-equiv="refresh" content="' . $this->fakeRedirectPageDelay . '; url=' . htmlspecialchars( $location, ENT_QUOTES ) . '" />',
					'{message}' => $message,
					'{ifnotreload}' => sprintf( _IFNOTRELOAD, $location )
				);
				echo str_replace( array_keys( $vars ), array_values( $vars ), $this->fakeRedirectTemplate );
			}
		}
		return array( $status, $message, $location );
	}
	/**
	 * Send the specified response code header to the response
	 * @param int $status The HTTP status code to send to the client
	 * @param string $message The HTTP status message to send to the client
	 * @access protected
	 */
	function sendResponseCode( $status = 200, $message = 'OK' ) {
		if ( !headers_sent() ) {
			$message = str_replace( array( "\r", "\n" ), '', $message );
			if ( $pos = strpos( $message, '<' ) ) {
				$message = substr( $message, 0, $pos );
			}
			header( "Status: $status $message" );
			header( "HTTP/1.1 $status $message" );
		}
	}
	
	
}



?>