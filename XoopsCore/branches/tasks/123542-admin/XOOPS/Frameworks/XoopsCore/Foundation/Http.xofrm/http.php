<?php
/**
 * xoops_http main class definition
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_http
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

/**
 * xoops_http framework static class
 * @package		xoops_http
 */
class xoops_http {
	/**
	 * Returns an RFC compliant (RFC822 or RFC850) date string from a timestamp
	 * @param integer $time Timestamp to format (will default to now)
	 * @return string
	 */	
	function date( $time = null ) {
        return gmdate(
        	( ini_get('y2k_compliance') ? 'D, d M Y' : 'l, d-M-y' ) .' H:i:s',
        	isset($time) ? $time : time()
        ) . ' GMT';
    }
	/**
	 * Converts a relative URI to an absolute one
	 * @param string $url URI to convert
	 * @return string An absolute URI
	 */
	function absoluteUrl( $url ) {
		global $xoops;

		if ( strpos( $url, '://' ) === false ) {
			$host = 'http' . ( $xoops->isSecure ? 's' : '' ) . '://' . $xoops->hostName;
			if ( !strlen($url) ) {
	            $url = $_SERVER['REQUEST_URI'];
        	} elseif ( substr( $url, 0, 1 ) == '/' ) {
	            return $host . $url;
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
}

