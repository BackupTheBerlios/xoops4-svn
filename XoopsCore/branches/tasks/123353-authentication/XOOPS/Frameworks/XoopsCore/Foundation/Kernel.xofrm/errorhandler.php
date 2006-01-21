<?php
/**
 * xoops_kernel_ErrorHandler main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_kernel
 * @package		xoops_kernel_ErrorHandler
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

/**
 * xoops_kernel_ErrorHandler main class
 * 
 * The XOOPS error handler catches PHP errors and exceptions and optionally sends
 * information about them to the logger service.
 * Physical paths that would be part of the error messages are converted to prevent
 * path disclosure on production servers.
 */
class xoops_kernel_ErrorHandler {
	/**
	 * If this handler should catch errors or not
	 * @var boolean
	 */
	var $catchErrors = false;
	/**
	 * If this handler should catch exceptions or not
	 * @var boolean
	 */
	var $catchExceptions = false;
	/**
	 * Which PHP errors are reported
	 * @var integer
	 */
	var $errorReporting = false;
	/**
	 * Default error reporting levels is $errorReporting is not specified
	 * @var array
	 */
	var $defaultReporting = array( XO_MODE_PROD => 0, XO_MODE_DEBUG => 2039, XO_MODE_DEV => E_ALL );
	/**
	 * The reporting level that was set before the handler has been activated
	 * @var integer
	 * @access private
	 */
	var $oldErrorReporting = false;
	/**
	 * How errors are reported in the message
	 * @var string[]
	 */
	var $errorNames = array(
		E_ERROR => 'Error', E_USER_ERROR => 'Error', E_WARNING => 'Warning', E_USER_WARNING => 'Warning',
		E_NOTICE => 'Notice', E_USER_NOTICE => 'Notice',
	);
	/**
	 * Initializes this instance
	 */
	function xoInit( $options = array() ) {
		global $xoops;
		if ( $this->errorReporting === false ) {
			$this->errorReporting = $this->defaultReporting[ $xoops->xoRunMode ];
		}
		$this->activateErrorHandling( true );
		return true;
	}
	/**
	 * Enable/disable the error handling functionality.
	 *
	 * When set to active, the error handler set the php error reporting level to E_ALL and uses its own
	 * $errorReporting property to mask the errors to report to ensure @ operator still works :-).
	 * @param bool	$enable		Whether to enable or disable the error handler
	 */
	function activateErrorHandling( $enable = true ) {
		if ( $enable && !$this->catchErrors ) {
			set_error_handler( array( &$this, 'handleError' ) );
			$this->oldErrorReporting = error_reporting( E_ALL );
			return $this->catchErrors = true;
		} elseif ( !$enable && $this->catchErrors ) {
		 	restore_error_handler();
			error_reporting( $this->oldErrorReporting );
			return $this->catchErrors = false;
		}
		return $this->catchErrors;
	}
	/**
	 * Error handler (called by PHP on error)
	 */	
	function handleError( $num, $str, $file = '', $line = 0, $context = false ) {
		global $xoops;
		if ( $num & $this->errorReporting & error_reporting() ) {
			$name = isset( $this->errorNames[$num] ) ? $this->errorNames[$num] : 'Undefined error';
			$str = $this->sanitizePaths( $str );
			if ( DIRECTORY_SEPARATOR != '/' ) {
				$file = str_replace( DIRECTORY_SEPARATOR, '/', $file );
			}
			$file = $this->sanitizePaths( $file );
			$str = "$name: $str in $file on line $line";
			if ( $xoops->services['logger'] ) {
				$xoops->services['logger']->logEvent( $str, 'error' );
			} else {
				echo "$str<br />\r\n";
			}
		}
	}
	/**
	 * Method used internally to transform physical paths to their XOOPS form
	 * @param string $str String to search for physical paths
	 * @return string Safe for display transformed string
	 */
	function sanitizePaths( $str ) {
		global $xoops;
		foreach ( $xoops->paths as $root => $v ) {
			$str  = str_replace( $v[0] . '/', "/$root/", $str );
		}
		return $str;
	}	
}

?>