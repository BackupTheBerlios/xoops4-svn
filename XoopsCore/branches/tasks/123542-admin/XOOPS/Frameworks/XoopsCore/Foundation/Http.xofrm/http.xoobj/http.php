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
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'xoops_http' );

/**
 * HTTP response customization component
 *
 * The HttpHandler component encapsulates the HTTP response manipulation functions
 * (redirection for now, and more later on)
 * @package		xoops_http
 * @subpackage	xoops_http_HttpHandler
 */
class xoops_http_HttpHandler {
	/**
	 * Whether HTTP redirections are enabled or not
	 * @var bool
	 */
	var $enableRedirections	= true;
	/**
	 * Whether to use output compression or not, if the client supports it
	 */
	var $enableCompression = true;
	/**
	 * Whether to use output compression or not, if the client supports it
	 */
	var $compressionLevel = 6;
	/**
	 * Cache limiter sent by default to the client
	 * @var string
	 */
	var $cacheLimiter = 'public';
	/**
	 * Default client caching lifetime (in seconds)
	 * @var integer
	 */
	var $cacheLifetime = 3600;
	/**
	 * Entity mimetype
	 */
	var $contentType = 'text/html';
	/**
	 * Entity encoding charset
	 */
	var $contentEncoding = 'iso-8859-1';
	/**
	 * Response entity ETag value
	 */
	var $entityTag = '';
	/**
	 * Entity last modification date (defaults to now)
	 */
	var $lastModified = 0;
	
	/**
	* How many seconds the fake redirection page should be shown
	* @var integer
	*/
	var $fakeRedirectDelay = 3;

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
	var $fakeRedirectTemplate = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	
<head>{refresh}</head>
<body>
	<div style="text-align:center; background-color:#EBEBEB; border:1px solid #AAAAAA; font-weight:bold;">
		<h4>{message}</h4><p>{ifnotreload}</p>
	</div>
	<!--{xo-logger-output}-->
</body></html>';
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
		header( 'X-Powered-By: ' . $GLOBALS['xoops']->xoShortVersionString, false );
		header( 'X-Powered-By: The noosphere', false );
		
		if ( $this->enableCompression && extension_loaded( 'zlib' ) ) {
			if ( $this->compressionLevel ) {
				ini_set( 'zlib.compression_level', $this->compressionLevel );
			}
			ob_start( 'ob_gzhandler' );
		}
		if ( !$this->lastModified ) {
			$this->lastModified = time();
		}
		$this->entityTag = $_SERVER['REQUEST_URI'];
		
		$this->setCachingPolicy();
		$this->setEntityInfo();
		return true;
	}

	/**
	 * Reload the redirection information from the session
	 */
	function reloadSessionState() {
		@$state =& $_SESSION[$this->xoBundleIdentifier];
		if ( $state ) {
			if ( @$state['tmpDisallowRedirections'] ) {
				$this->enableRedirections = false;
			}
			if ( !empty( $state['redirect_info'] ) ) {
				foreach ( $state['redirect_info'] as $k => $v ) {
					$_SERVER[ 'REDIRECT_' . $k ] = $v;
				}
				unset( $state['redirect_info'] );
				$this->redirectMessage = $_SERVER['REDIRECT_ERROR_NOTES'];
			}
		}
	}
	/**
	 * Set/sends the output caching headers
	 */
	function setCachingPolicy( $limiter = null, $lifetime = null ) {
		if ( isset($limiter) ) {
			$this->cacheLimiter = $limiter;
		}
		if ( isset($lifetime) ) {
			$this->cacheLifetime = $lifetime;
		}
		switch ( $this->cacheLimiter ) {
		case 'none':
			break;
		case 'no-cache':
		case 'nocache':
			header( 'Expires: ' . xoops_http::date( time() - 3600 ) );
			header( 'Cache-control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0' );
			break;
		case 'private':
			header( 'Expires: ' . xoops_http::date( time() - 3600 ) );
		case 'private_no_expire':
			header( "Cache-control: private,max-age=$this->cacheLifetime,pre-check=$this->cacheLifetime" );
			break;
		case 'public':
		default:
			header( 'Expires: ' . xoops_http::date( time() + $this->cacheLifetime ) );
			header( "Cache-control: public,max-age=$this->cacheLifetime" );
			//header( "Last-Modified: " . xoops_http::date( $this->lastModified ) );
			break;
		}
	}
	/**
	 * Set/sends the entity information headers (content-type,encoding and last-mod date)
	 *
	 * @param string $mime Mimetype of the entity
	 * @param string $encoding Charset of the entity
	 */
	function setEntityInfo( $mime = null, $charset = null, $lastMod = null ) {
		if ( isset($mime) ) {
			$this->contentType = $mime;
		}
		if ( isset($charset) ) {
			$this->contentEncoding = $charset;
		}
		if ( isset($lastMod) ) {
			$this->lastModified = $lastMod;
		}
		header( "Content-Type: $this->contentType; charset=$this->contentEncoding" );
		header( "Last-Modified: " . xoops_http::date( $this->lastModified ) );
	}
	
	/**
	 * Send the specified file content to the browser
	 *
	 * @param unknown_type $filename
	 * @param unknown_type $disposition
	 * @param unknown_type $data
	 * @param unknown_type $size
	 */
	function sendFileEntity( $filename, $disposition = 'inline', $data = null, $size = 0 ) {
		if ( !isset($data) ) {
			$data = file_get_contents( $filename );
		}
		if ( !$size ) {
			$size = strlen( $data );
		}
		if ( false !== ( $pos = strrpos( $filename, '/' ) ) ) {
			$filename = substr( $filename, $pos + 1 );
		}
		header( "Content-disposition: $disposition; filename=\"$filename\"" );
		header( "Content-length: $size" );
		if ( $disposition != 'inline' ) {
			// If the file is sent as an attachment, send caching headers so MSIE can correctly access the file
			header( 'Cache-control: cache,must-revalidate' );
		}
		echo $data;
	}
	
	
	function addVariation( $key, $value ) {
		header( 'Vary: ' . ( substr($key,0,3) == 'xo-' ? '*' : $key ), false );
		$this->entityTag .= '-' . $key . '=' . $value;
		header( 'ETag: "' . md5($this->entityTag) . '"' );
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
		/* if ( empty($location) && isset($this->errorPages[$status]) ) {
			$location = $this->errorPages[$status];
		} */
		if ( $location == -1 ) {
			$location = !@empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $xoops->url( '/www/' );
		}
		if ( !empty($location) ) {
		    if ( preg_match( "/[\\0-\\31]|about:|script:/i", $location ) ) {
        		$location = $xoops->url( '/www/' );
    		}
    		if ( !strpos( $location, '://' ) ) {
				$location = xoops_http::absoluteUrl( $location );
    		}
		}
		if ( !$xoops->isVirtual ) {
			$this->sendResponseCode( $status, $message );
			if ( !empty( $location ) ) {
				if ( $this->enableRedirections ) {
					$_SESSION[$this->xoBundleIdentifier]['redirect_info'] = array(
						'STATUS' => $status,
						'URL' => $_SERVER['REQUEST_URI'],
						'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
						'ERROR_NOTES' => $message,
					);
					header( "Location: $location" );
					echo "<body><a href='$location'>Page redirected. Status: $status, $message.</a></body>";
				} else {
					//$this->sendResponseCode( 200, $message );
					$vars = array(
						'{refresh}' => '<meta http-equiv="refresh" content="' . $this->fakeRedirectPageDelay . '; url=' . htmlspecialchars( $location, ENT_QUOTES ) . '" />',
						'{message}' => $message,
						'{ifnotreload}' => sprintf( _IFNOTRELOAD, $location )
					);
					echo str_replace( array_keys( $vars ), array_values( $vars ), $this->fakeRedirectTemplate );
				}
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
			//header( "Status: $status $message" );
			header( "HTTP/1.1 $status $message" );
		}
	}
	
	
}



?>