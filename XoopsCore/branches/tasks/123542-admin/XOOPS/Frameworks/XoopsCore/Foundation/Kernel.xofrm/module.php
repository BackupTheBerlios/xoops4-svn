<?php
/**
 * xoops_kernel_Module main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_kernel
 * @subpackage	xoops_kernel_Module
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();


/**
 * Local factory in charge of modules instanciation
 * @package		xoops_kernel
 * @subpackage	xoops_kernel_Module
 */
class xoops_kernel_ModuleFactory {
	/**
	 * Instanciate the specified module
	 */
	function &createInstanceOf( $bundleId, $options = array(), $initArgs = array() ) {
		global $xoops;

		XOS::import( 'xoops_kernel_Module' );
		// @TODO: This is lame and limited, whatever, should be arranged later
		if ( !empty( $bundleId ) ) {
			// Module ID specified: get its location from the registry
			if ( !XOS::import( $bundleId ) ) {
				trigger_error( "Cannot instanciate unknown module " . $bundleId, E_USER_WARNING );
				return false;
			}
			$root = $xoops->path( XOS::classVar( $bundleId, 'xoBundleRoot' ) ) . '/';
		} else {
			$root = '';
		}
		$moduleInfo = @include $root . 'xo-info.php';
		unset( $moduleInfo['xoServices'] );
		$options = array_merge( $moduleInfo, $options );
		if ( isset( $moduleInfo['xoClassPath'] ) ) {
			XOS::import( $moduleInfo['xoBundleIdentifier'] );
			$inst =& XOS::createInstanceOf( $moduleInfo['xoBundleIdentifier'], $options );
		} else {
			$inst =& XOS::createInstanceOf( 'xoops_kernel_Module', $options );
		}
		$inst->xoBundleIdentifier = $moduleInfo['xoBundleIdentifier'];
		$inst->xoBundleRoot = XOS::classVar( $inst->xoBundleIdentifier, 'xoBundleRoot' );
		// If we are instanciating the "current" module, find the current location
		if ( $inst && empty( $bundleId ) ) {
			$moduleRoot = substr( $inst->xoBundleRoot, 4 );
			$scriptFile = substr( strstr( $_SERVER['SCRIPT_NAME'], $moduleRoot ), strlen( $moduleRoot ) );
			$inst->currentLocation = $inst->findLocationName( $scriptFile );
		}
		return $inst;
	}

}


/**
 * Base class for XOOPS modules (applications)
 * @category	XoopsOS
 * @package		xoops_kernel
 * @subpackage	xoops_kernel_Module
 */
class xoops_kernel_Module {
	
	
	var $isGpc = false;
	
	
	/**
	 * Groups that are given access to this module
	 * @var string[]
	 */	
	var $allowFor = array( XOOPS_GROUP_ADMIN );	

	var $currentLocation = false;

	function xoInit( $options = array() ) {
		$this->isGpc = get_magic_quotes_gpc();
		return true;
	}	

	function findLocationName( $relpath ) {
		foreach ( $this->moduleLocations as $k => $loc ) {
			if ( $relpath == $loc['scriptFile'] ) {
				return $k;
			}				
		}
		return '';
	}
	
	
	
	function requestParameters( $source = 'R' ) {
		$loc =& $this->moduleLocations[ $this->currentLocation ];
		return isset( $loc['parameters'] ) ? xoops_kernel_Module::requestValues( $loc['parameters'], $source ) : array();
	}
	
	function requestValues( $defs, $source = 'R' ) {
		$vars = array();
		foreach ( $defs as $varName => $varDef ) {
			$vars[ $varName ] = xoops_kernel_Module::requestValue( $varName, $varDef[0], $varDef[1], $source );
		}
		return $vars;
	}
	
	function requestValue( $var, $default = null, $type = XO_TYPE_INT, $source = 'R' ) {
		if ( is_string( $source ) ) {
			$source = strrev( $source );
			$globals = array( 'R' => &$_REQUEST, 'E' => &$_ENV, 'G' => &$_GET, 'P' => &$_POST, 'C' => &$_COOKIES, 'S' => &$_SESSION );
			foreach ( str_split( $source, 1 ) as $ch ) {
				if ( isset( $globals[$ch][$var] ) ) {
					$value = $globals[$ch][$var];
					break;
				}
			}
		} elseif ( isset( $source[$var] ) ) {
			$value = $source[$var];
		}
		if ( !isset($value) ) {
			$value = $default;
		} else {
			if ( $type & XO_TYPE_ARRAY ) {
				if ( !is_array($value) ) {
					$value = array($value);
				}
				foreach ( $value as $k => $v ) {
					$value[$k] = xoops_kernel_Module::sanitizeValue( $v, $type xor XO_TYPE_ARRAY );
				}
			} else {
				$value = xoops_kernel_Module::sanitizeValue( $value, $type );
			}
		}
		return $value;
	}


	
	
	
	function sanitizeValue( $value, $type = XO_TYPE_INT ) {
		if ( ( $type & XO_TYPE_STRING ) && $this->isGpc ) {
			$value = stripslashes( $value );
		}
		switch ( $type ) {
		case XO_TYPE_FLOAT:
			return (float)$value;
		case XO_TYPE_BOOL:
			return (bool)$value;
		case XO_TYPE_FILE:
		case XO_TYPE_PATH:
			return str_replace( array( '/', '\\' ), '', $value );
		case XO_TYPE_STRING:
			return htmlspecialchars( $value, ENT_QUOTES );
		case XO_TYPE_MARKUP:
			return $value;
		case XO_TYPE_INT:
		default:
			return (int)$value;
		}
	}
	
	
}

?>