<?php
/**
* xoops_kernel_Xoops2 class definition
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @package		xoops_kernel
* @subpackage	xoops_kernel_Xoops2
* @since        2.3.0
* @author		Skalpa Keo <skalpa@xoops.org>
* @version		$Id$
*/

/**
* Xoops2 kernel class
*
* - It handles the boot sequence (read the specified bootfile, run startup items, and instanciate the current module)
* - It contains functionnally limited implementations for both the error handler and the logger services, to ensure
*   those are available at all time, so even early errors generated before booting can be handled)
*
* @package		xoops_kernel
* @subpackage	xoops_kernel_Xoops2
*/
class xoops_kernel_Xoops2 {
	var $xoBundleIdentifier		= 'xoops_kernel_Xoops2';
	var $xoRunMode				= XO_MODE_PROD;
	var $xoShortVersionString	= 'XOOPS 2.3.0-alpha1 "Cheerleaders"';

	var $xoBundleRoot = 'XOOPS/Frameworks/XoopsCore/Foundation/Kernel.xofrm';
	
	/**
	 * Current host identifier
	 *
	 * @var string
	 */
	var $hostId					= '';
	/**
	 * Host base URI when using regular access (HTTP)
	 *
	 * @var string
	 */
	var $hostLocation			= '';
	/**
	 * Host base URI when using secure access (HTTPS)
	 *
	 * @var string
	 */
	var $secureLocation			= '';
	/**
	 * If the current request uses secure access or not
	 *
	 * @var bool
	 */
	var $isSecure				= false;
	/**
	 * Locations of the XOOPS folders
	 *
	 * @var array
	 */
	var $paths					= '';
	/**
	 * Name of the boot file to execute
	 *
	 * @var string
	 */
	var $bootFile				= 'rc.php';
	/**
	 * Folder containing the startup items
	 *
	 * @var string
	 */
	var $startupItemsPath		= 'XOOPS/StartupItems';
	/**
	 * References to the currently running services
	 *
	 * @var array
	 */
	var $services				= null;
	var $captures				= array(
		'http'		=> 'xoops_http_HttpHandler',
		'session'	=> 'xoops_http_SessionService',
		'legacydb'	=> array( 'xoops_db_Database', array( 'driverName' => 'xoops.legacy' ) ),
	);

	/** 
	 * Indicates whether the boot sequence has been performed or not
	 * 
	 * @var boolean
	 * @access private
	 */
	var $hasBooted				= false;
	/**
	 * Has the shutdown sequence been performed or not
	 * 
	 * @var boolean
	 * @access private
	 */
	var $hasShutdown			= false;
	/**
	 * Semi-relative URI to this XOOPS files
	 *
	 * @var string
	 */
	var $baseLocation			= '';

	var $isVirtual				= false;

	var $previousErrorHandler	= false;

	var $isHandlingErrors		= false;
	var $_oldReporting			= 0;
	var $errorReporting			= 0;
	
	function xoops_kernel_Xoops2( $hostId, $hostVars ) {
	 	$GLOBALS['xoops']	=& $this;
	 	$GLOBALS['exxos']->pathHandler =& $this;

	 	$this->hostId = $hostId;
	 	XOS::apply( $this, $hostVars );
	 	
		if ( $this->xoRunMode & XO_MODE_DEV_MASK ) {
			$this->errorReporting |= E_ALL;
		}
		//error_reporting(E_ALL);
		$this->activateErrorHandler( true );

		$this->loadRegistry();
	 	$this->services =& $GLOBALS['exxos']->services;


	 	$captures = @include $this->path( 'var/Application Support/xoops_kernel_Xoops2/services.php' );
	 	if ( is_array($captures) ) {
	 		$this->captures = array_merge( $this->captures, $captures );
	 	}

		$this->bootFile = str_replace( array( '/', '\\' ), '', $this->bootFile );		// Might be useless check, but this one won't hurt
		
		$this->baseLocation = $this->isSecure ? $this->secureLocation : $this->hostLocation;
		if ( false !== ( $pos = strpos( $this->baseLocation, '/' ) ) ) {
			$this->baseLocation = substr( $this->baseLocation, $pos );
		} else {
			$this->baseLocation = '';
		}
		//$this->services['logger'] = $this->services['errorhandler'] = $this;

	}

	/**
	 * Load the components registry from persistent storage
	 * 
	 * @access private
	 */
	function loadRegistry() {
		$reg = @include $this->path( 'var/Application Support/xoops_kernel_Xoops2/registry.php' );
		if ( !is_array($reg) || ( $this->xoRunMode & XO_MODE_DEV_MASK ) ) {
			$reg = include $this->path( "$this->xoBundleRoot/scripts/rebuild_registry.php" );
		}
		$GLOBALS['exxos']->registry = $reg;
	}
	
	/**
	 * Create an instance of the specified service by name
	 * 
	 * @param string $name		Name under which the service will be available
	 * @param string $bundleId	ID of the class to instanciate
	 * @param array  $options	Parameters to send to the service during instanciation
	 */
	function &loadService( $name, $bundleId = '', $options = array() ) {
		if ( !isset( $this->services[$name] ) ) {
			if ( isset( $this->captures[$name] ) ) {
				if ( is_array( $this->captures[$name] ) ) {
					list( $bundleId, $options ) = $this->captures[$name];
				} else {
					$bundleId = $this->captures[$name];
				}
			} elseif ( empty( $bundleId ) ) {
				$bundleId = $name;
			}
			$this->services[$name] =& XOS::create( $bundleId, $options );
		}
		return $this->services[$name];
	}
	
	/**
	 * Enable/disable the errorhandler.
	 *
	 * When set to active, the error handler set the php error reporting level to E_ALL and uses its own
	 * $errorReporting property to mask the errors to report (so the @ operator still works :-)
	 * 
	 * @param bool	$enable		Whether to enable or disable the error handler
	 */
	function activateErrorHandler( $enable = true ) {
		if ( $enable && !$this->isHandlingErrors ) {
			set_error_handler( array( &$this, 'handleError' ) );
			$this->_oldReporting = error_reporting( E_ALL );
			return $this->isHandlingErrors = true;
		} elseif ( !$enable && $this->isHandlingErrors ) {
		 	restore_error_handler();
			error_reporting( $this->_oldReporting );
			return $this->isHandlingErrors = false;
		}
		return $this->isHandlingErrors;
	}
	
	function handleError( $num, $str, $file = '', $line = 0, $context = false ) {
		static $names = array(
			E_ERROR => 'Error', E_USER_ERROR => 'Error', E_WARNING => 'Warning', E_USER_WARNING => 'Warning',
			E_NOTICE => 'Notice', E_USER_NOTICE => 'Notice',
		);
		if ( $num & $this->errorReporting & error_reporting() ) {
			foreach ( $this->paths as $root => $v ) {
				$str  = str_replace( $v[0] . '/', "/$root/", $str );
			}
			$name = isset( $names[$num] ) ? $names[$num] : 'Undefined error';
			$msg = "$name: $str";
			if ( $file && $line ) {
				$file = str_replace( DIRECTORY_SEPARATOR, '/', $file );
				foreach ( $this->paths as $root => $v ) {
					$file = str_replace( $v[0] . '/', "/$root/", $file );
				}
				$msg .= " in $file on line $line";
			}
			echo "$msg<br />\r\n";
		}
	}
	
	/**
	* Convert a XOOPS path to a physical one
	*/
	function path( $url, $virtual = false ) {
		// If the URL begins with protocol:// then remove it
		if ( $pos = strpos( $url, '://' ) ) {
			$url = substr( $url, $pos + 3 );
		}
		$parts = explode( '#', $url );
		if ( count( $parts ) == 1 ) {
			if ( $parts[0]{0} == '/' ) {
				$parts[0] = substr( $parts[0], 1 );
			}
			$parts = explode( '/', $parts[0], 2 );
			if ( !$virtual ) {
				return !isset( $this->paths[$parts[0]] ) ? '' : ( $this->paths[$parts[0]][0] . '/' . $parts[1] );
			} else {
				if ( !isset( $this->paths[$parts[0]][1] ) ) {
					return false;
				}
				if ( empty( $this->paths[$parts[0]][1] ) ) {
					return $this->baseLocation . '/' . $parts[1];
				}
				return $this->baseLocation . '/' . $this->paths[$parts[0]][1] . '/' . $parts[1];
			}
		} else {
			$root = XOS::classVar( $parts[0], 'xoBundleRoot' );
			return $this->path( $root . '/' . $parts[1] );
		}
	}
	/**
	* Convert a XOOPS path to an URL
	*/
	function url( $url ) {
		return $this->path( $url, true );
	}

	
	/**
	 * Perform the boot sequence
	 *
	 * @access public
	 * @return bool
	 */
	function boot() {
		//$this->setupModule();
		if ( !$this->hasBooted ) {
			//register_shutdown_function( array( &$this, 'shutdown' ) );
			if ( !empty($this->bootFile) ) {
				require_once $this->path( "/XOOPS/Boot/$this->bootFile" );
			}
			$this->hasBooted = true;
			if ( $this->launchStartupItems() ) {
				return true;
			} 
		}
		return true;
	}

	/**
	 * Perform the shutdown sequence
	 *
	 * @access public
	 * @return bool
	 */
	function shutdown() {
		if ( !$this->hasShutdown ) {
			$this->hasShutdown = true;
		}
	}

	/**
	 * Launch the kernel startup items
	 *
	 * <p>Startup items are scripts than will be executed during every request.<br />
	 * Upon completion of its normal startup sequence, the XOOPS kernel will launch every startup item
	 * located in the StartupItems folder (by default <em>XOOPS/StartupItems</em>).</p>
	 * <p>
	 * To make a startup item:<br />
	 * Create a folder in the startup items folder, then add a .php script named exactly like it inside.
	 * </p>
	 * <p>A startup item script can cancel the boot sequence by returning <em>false</em>.</p>
	 * 
	 * @access private
	 */
	function launchStartupItems() {
		if ( empty( $this->startupItemsPath ) ) return true;
		
		$path = $this->path( $this->startupItemsPath );
		if ( @$dh = opendir( $path ) ) {
			while ( $file = readdir( $dh ) ) {
				if ( $file{0} == '.' || $file == 'CVS' )	continue;
				if ( is_dir( "$path/$file" ) ) {
					if ( -1 == @include "$path/$file/$file.php") {
						return false;
					}
				}
			}
			closedir( $dh );
		}
		return true;
	}

	function launchModule() {
		return true;
		if ( !$this->loadService( 'module', 'xoops_kernel_Module' ) ) {
			return false;
		}
		if ( !$this->services['module']->checkAccess() ) {
			//$this->services['http']->setResponse( 403, null, null, true );
			return false;
		}
		return $this->services['module']->xoBundleIdentifier;
	}

	
} // class xoops_kernel_Xoops2


?>