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


define( 'EXXOS', 'xoops' );


define( 'XO_TYPE_INT',		0x10 );
define( 'XO_TYPE_FLOAT',	0x11 );
define( 'XO_TYPE_BOOL',		0x12 );

define( 'XO_TYPE_STRING',	0x20 );
define( 'XO_TYPE_FILE',		0x21 );
define( 'XO_TYPE_PATH',		0x22 );
define( 'XO_TYPE_MARKUP',	0x23 );

define( 'XO_TYPE_ARRAY',	0x80 );

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
class xoops_kernel_Xoops2 extends XOS {
	var $xoBundleIdentifier		= 'xoops_kernel_Xoops2';
	var $xoRunMode				= XO_MODE_PROD;
	var $xoShortVersionString	= 'XOOPS 2.3.0-alpha1 "Cheerleaders"';

	var $xoBundleRoot = 'XOOPS/Frameworks/XoopsCore/XoopsOS/Kernel.xofrm';
	
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

	var $captures				= array(
		'error'		=> 'xoops_kernel_ErrorHandler',
		'logger'	=> 'xoops_kernel_Logger',
		'http'		=> 'xoops_http_HttpHandler',
		'session'	=> 'xoops_http_SessionService',
		'auth'		=> 'xoops_auth_AuthenticationService',
		'theme'		=> 'xoops_opal_Theme',
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
	 * Hostname to use to construct absolute URIs
	 *
	 * @var string
	 */
	var $hostName				= '';
	/**
	 * Semi-relative URI to this XOOPS files
	 *
	 * @var string
	 */
	var $baseLocation			= '';
	/**
	 * Currently logged in user
	 *
	 * NOTE: This currently points to a XoopsUser instance.
	 * THIS WILL CHANGE BEFORE THE 2.3.0 RELEASE !!
	 * Use the $xoopsUser global if you need to access a XoopsUser
	 * 
	 * @var object
	 */
	var $currentUser			= '';
	/**
	 * Type of object to create when instanciating the current user (unimplemented)
	 */
	var $userBundleIdentifier = 'xoops_kernel_User';

	/**
	 * Whether or not we want to enable somebody to login using the system user credentials (unimplemented)
	 *
	 * $enableSystemUser and $systemUserProperties allow you to define a virtual user identity
	 * that you'll be able to use to login to your site in case of problems with the current
	 * authentication drivers or database.
	 *
	 * For security purposes, people are advised to disable this function, and should only enable it
	 * when it is needed.
	 */
	var $enableSystemUser = false;
	/**
	 * Values of the properties used to build the system user object
	 */
	var $systemUserProperties = array(
		'login'		=> 'xoopsadmin',
		'password'	=> '',
		'groups'	=> array( 1, 2 ),
	);
	/**
	 * Indicates if the kernel is currently performing a virtual page request
	 */
	var $isVirtual				= false;

	function xoops_kernel_Xoops2( $hostId, $hostVars ) {
	 	$GLOBALS['xoops']	=& $this;
	 	$this->pathHandler =& $this;

	 	$this->hostId = $hostId;
	 	XOS::apply( $this, $hostVars );
	 	
		if ( @$GLOBALS['xoPreventDevMode'] ) {
			$this->xoRunMode = 0;
		}
	 	// Enable error reporting by default in development mode, enable it otherwise
 		error_reporting( ( $this->xoRunMode == XO_MODE_DEV ) ? E_ALL : 0 );

 		$this->loadRegistry();
	 	//$this->services =& $GLOBALS['exxos']->services;

	 	$captures = @include $this->path( 'var/Application Support/xoops_kernel_Xoops2/services.php' );
	 	if ( is_array($captures) ) {
	 		$this->captures = array_merge( $this->captures, $captures );
	 	}

		$this->bootFile = str_replace( array( '/', '\\' ), '', $this->bootFile );		// Might be useless check, but this one won't hurt
		
		$this->baseLocation = $this->isSecure ? $this->secureLocation : $this->hostLocation;
		if ( false !== ( $pos = strpos( $this->baseLocation, '/' ) ) ) {
			$this->hostName = substr( $this->baseLocation, 0, $pos );
			$this->baseLocation = substr( $this->baseLocation, $pos );
		} else {
			$this->hostName = $this->baseLocation;
			$this->baseLocation = '';
		}
	}

	/**
	 * Perform the boot sequence
	 * 
	 * The following operations are done in order during the boot-sequence:
	 * - The startup-sequence file (rc.php by default) is executed
	 * - The authenticated user object is created (if any)
	 * - The current module is initialized
	 * - Startup items are executed
	 *
	 * @access public
	 * @return bool
	 */
	function boot() {
		if ( $this->hasBooted ) {
			return $this->launchCurrentModule();
		}
		register_shutdown_function( array( &$this, 'shutdown' ) );
		if ( !empty($this->bootFile) ) {
			require_once $this->path( "/XOOPS/Boot/$this->bootFile" );
		}
		$this->launchCurrentModule();
		
		if ( $this->launchStartupItems() ) {
			return true;
		} 
		$this->hasBooted = true;
		return true;
	}
	/**
	 * Perform the system-wide shutdown sequence
	 * 
	 * During kernel shutdown, instanciated services are checked for an 'xoShutdown'
	 * method and if they provide one it will be called.
	 *
	 * @access public
	 * @return bool
	 */
	function shutdown() {
		if ( !$this->hasShutdown ) {
			$this->hasShutdown = true;
			$services = array_reverse( array_keys( $this->services ) );
			foreach ( $services as $srv ) {
				if ( method_exists( $this->services[$srv], 'xoShutdown' ) ) {
					$this->services[$srv]->xoShutdown();
				}
			}
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

	function launchCurrentModule() {
		$module =& $this->loadModule();
		if ( !$module ) {
			$this->services['http']->sendResponse( 500, null, -1 );
		} else {
			if ( method_exists( $module, 'xoRunModule' ) && !call_user_func( array( &$module, 'xoRunModule' ) ) ) {
				return false;
			}
			//if ( !$module->checkAccess() ) {
				//$this->services['http']->sendResponse( 403, null, -1 );
			//} else {
				$this->currentModule =& $module;
				return true;
			//}
		}
		return false;
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
		$this->registry = $reg;
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
			// Preferences retrieval will be integrated in the instanciation layer during alpha3 dev
			// so, this ugly hack is just here temporarily to ensure the 2 actual config panels work
			// correctly, and will be removed soon :-)
			if ( $name == 'http' || $name == 'session' ) {
				$prefs =& XOS::create( 'xoops_core_PreferencesHandler' );
				$values = $prefs->getMultiple( null, $bundleId, XO_PREFS_ANYUSER, XO_PREFS_CURRENTHOST );
				$options = array_merge( $values, $options );
			}
			// -----
			$this->services[$name] =& XOS::create( $bundleId, $options );
		}
		return $this->services[$name];
	}
	/**
	 * Creates an instance of the specified module bundle
	 * 
	 * @param string $bundleId	Bundle ID of the module to instanciate
	 * @param array  $options	Parameters to send to the service during instanciation
	 */
	function &loadModule( $bundleId = '', $options = array() ) {
		if ( !empty($bundleId) && isset( $this->services[$bundleId] ) ) {
			return $this->services[$bundleId];
		}
		if ( !isset( $this->factories[ 'xoops_kernel_Module' ] ) ) {
			$this->factories['xoops_kernel_Module'] =& XOS::create( 'xoops_kernel_ModuleFactory' );
		}
		$module =& $this->factories['xoops_kernel_Module']->createInstanceOf( $bundleId, $options );
		if ( $module ) {
			$this->services[ $module->xoBundleIdentifier ] =& $module;
		}
		return $module;		
	}

	/**
	 * Accept the specified user as the currently logged in user
	 *
	 * @param string $login Login of the user to accept
	 * @param boolean $permanent Whether to accept this user permanently or for the current request only
	 * @return boolean
	 */
	function acceptUser( $login = '', $permanent = false ) {
		if ( !$login ) {
			return false;
		}
		// @TODO-2.3: Clean this up later...
		$GLOBALS['xoopsDB'] = $this->loadService( 'legacydb' );

		require_once XOOPS_ROOT_PATH . '/include/functions.php';
		require_once XOOPS_ROOT_PATH . '/kernel/object.php';
		require_once XOOPS_ROOT_PATH . '/class/criteria.php';
		
		$handler =& xoops_gethandler('member');
		list($user) = $handler->getUsers( new Criteria( 'uname', $login ) );
		if ( is_object( $user ) ) {
			$GLOBALS['xoopsUser'] = $user;
			XOS::import( 'xoops_kernel_User' );
			$lvl_lookup = array( 0 => XO_LEVEL_INACTIVE, 1 => XO_LEVEL_REGISTERED, 5 => XO_LEVEL_ADMIN );
			$this->currentUser = XOS::create( 'xoops_kernel_User', array(
				'userId' => $user->getVar( 'uid', 'n' ),
				'login' => $user->getVar( 'uname', 'n' ),
				'email' => $user->getVar( 'email', 'n' ),
				'groups' => $user->getGroups(),
				'fullName' => $user->getVar( 'name', 'n' ),
				'level' => $lvl_lookup[ $user->getVar( 'level', 'n' ) ],
			) );
			if ( isset( $this->services['http'] ) ) {
				$this->services['http']->addVariation( 'xo-user', $this->currentUser->userId );
			}
			if ( $permanent && $this->services['session'] ) {
				$this->services['session']->start();
				$_SESSION[$this->xoBundleIdentifier]['currentUser'] = $login;
			}
			return true;
		} else {
			$GLOBALS['xoopsUser'] = '';
			$this->currentUser = XOS::create( 'xoops_kernel_User' );
		}
		return false;
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
			if ( substr( $parts[0], 0, 1 ) == '/' ) {
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
			@list( $location, $query ) = explode( '?', $parts[1] );
			if ( empty($location) || strpos( $location, '/' ) !== false ) {
				return $this->path( $root . '/' . $location, $virtual );
			}
			if ( $module = $this->loadModule( $parts[0] ) ) {
				$uri = $module->xoBundleRoot;
				if ( isset( $module->moduleLocations[$location] ) ) {
					$uri .= $module->moduleLocations[$location]['scriptFile'];
				} else {
					trigger_error( "Unknown location {$parts[0]}#$location", E_USER_WARNING );
					return false;
				}
				if ( isset($query ) ) {
					$uri .= ( strpos( $uri, '?' ) ? '&' : '?' ) . $query;
				}
				return $this->path( $uri, $virtual );
			}
		}
		return false;
	}
	/**
	* Convert a XOOPS path to an URL
	*/
	function url( $url ) {
		return $this->path( $url, true );
	}
	/**
	* Performs a virtual sub-request
	*
	* This will call the page specified in $url as if it was normally requested, giving the possibility to
	* customize the content of the $_REQUEST and $_SERVER variables the sub-request will receive.<br />
	* NOTE: For this to work correctly, the called page must use <b>return</b> to end processing if necessary
	* - NOT exit() or die() -, also it must use $_REQUEST to get its parameters, not $_POST or $_GET (but that
	* is how a well-coded page should be done anyway).
	*
	* @param	array	$request	Values to be passed to the sub-request as its $_REQUEST var
	* @param	array	$server		Values to add/change in the $_SERVER var
	* @return	mixed	The value returned by the requested page (or false if an error occured)
	*/
	function virtual( $url, $request = array(), $server = array() ) {
		// Extract the query parameters specified via the url string and add them to $_REQUEST
		$parsed = parse_url( $url );
		if ( isset($parsed['query']) ) {
			$args=array();
			parse_str( $parsed['query'], $args );
			if ( !empty($args) ) {
				$request = array_merge( $request, $args );
			}
			if ( !isset($server['REQUEST_URI']) ) {
				//$server['REQUEST_URI'] = $parsed['path'];
			}
		}
		list( $moduleName, $moduleLocation ) = explode( '#', $url, 2 );
		if ( !$subModule = $this->loadModule( $moduleName ) ) {
			trigger_error( "Cannot perform virtual request to unknown module $moduleName", E_USER_WARNING );
			return false;
		}
		if ( substr( $moduleLocation, 0, 1 ) == '/' ) {
			$subModule->currentLocation = $subModule->findLocationName( $moduleLocation );
		} else {
			$subModule->currentLocation = $moduleLocation;
		}
		$path = $this->path( $subModule->xoBundleRoot . $subModule->moduleLocations[ $subModule->currentLocation ]['scriptFile'] );

		$backup = array( $this->isVirtual, $this->currentModule, $_REQUEST, $_SERVER );
		$cwd = getcwd();
		list( $this->isVirtual, $this->currentModule, $_REQUEST, $_SERVER ) = array( true, $subModule, $request, array_merge( $_SERVER, $server ) );
		chdir( dirname( $path ) );
		$ret = include $path;
		list( $this->isVirtual, $this->currentModule, $_REQUEST, $_SERVER ) = $backup;
		chdir( $cwd );
		return $ret;
	}

} // class xoops_kernel_Xoops2

/**
 * Returns a translated string
 */
function XO_( $str ) {
	global $xoops;
	return $xoops->services['lang'] ? $xoops->services['lang']->translate( $str ) : $str;	
}


if ( !function_exists( 'str_split' ) ) {
	function str_split( $string, $chunkSize = 1 ) {
		list( $pos, $len, $out ) = array( 0, strlen($string), array() );
		while ( $pos <= $len ) {
			$out[] = substr( $string, $pos, $chunkSize );
			$pos += $chunkSize;
		}
		return $out;
	}
	function array_combine( $keys, $values ) {
		$size = count($keys);
		if ( !$size || $size != count($values) )	return false;
		$out = array();
		for ( $i=0; $i!=$size; $i++ ) {
			$out[ $keys[$i] ] = $values[$i];
		}
		return $out;
	}
}









?>