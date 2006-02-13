<?php
/**
* XOOPS kernel initialization script
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @since        2.3.0
* @version		$Id$
* @author		Skalpa Keo <skalpa@xoops.org>
*/

if ( !defined( 'XO_MODE_DEV' ) ) {

	define( 'XO_MODE_DEBUG_MASK', 1 );
	define( 'XO_MODE_DEV_MASK', 2 );

	define( 'XO_MODE_PROD', 0 );
	define( 'XO_MODE_DEBUG', XO_MODE_DEBUG_MASK );
	define( 'XO_MODE_DEV', XO_MODE_DEBUG_MASK | XO_MODE_DEV_MASK );

	/**
	* Initialize the XOOPS kernel
	*
	* This function launches the XOOPS kernel, including required files and instanciating
	* the kernel class.
	*/
	function xoops2_create_kernel() {
		error_reporting( E_ALL );
		if ( is_object( $GLOBALS['xoops'] ) ) {
			return false;
		}
		$path = ( DIRECTORY_SEPARATOR == '/' ) ? dirname( __FILE__ ) : str_replace( DIRECTORY_SEPARATOR, '/', dirname(__FILE__) );
		// Include required files
		require_once "$path/exxos.php";
		require_once "$path/kernel.php";
		$hostsInfo = require_once "$path/hosts.php";
		
		// Find which host/alias is the current one, according to the request
		$hostId = $hostsInfo[''];
		$aliasNum = 0;
		if ( php_sapi_name() != 'cli' ) {
			$reqSecure	= ( @$_SERVER['HTTPS'] == 'on' ) ? true : false;
			$reqPort	= empty( $_SERVER['SERVER_PORT'] ) ? ( $reqSecure ? 443 : 80 ) : $_SERVER['SERVER_PORT'];
		
			$location = @empty( $_SERVER['HTTP_HOST'] ) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
			if ( ( $reqPort != 443 && $reqSecure ) || ( $reqPort != 80 && !$reqSecure ) ) {
				$location .= ':' . $reqPort;
			}
			$location .= empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['REQUEST_URI'];
			foreach ( $hostsInfo as $id => $vars ) {
				if ( !empty( $id ) ) {
					foreach ( $vars[ $reqSecure ? 'secureLocation' : 'hostLocation' ] as $num => $alias ) {
						if ( isset($alias) && !strncmp( $location, $alias, strlen( $alias ) ) ) {
							$hostId = $id;
							$aliasNum = $num;
							break 2;
						}
					}
				}
			}
		}
		$vars = $hostsInfo[ $hostId ];
		$vars['hostLocation']	= $vars['hostLocation'][$aliasNum];
		$vars['secureLocation'] = $vars['secureLocation'][$aliasNum];
		$vars['isSecure'] = $reqSecure;
		$vars['hostsList'] = array_keys( $hostsInfo );
		unset( $vars['hostsList'][''] );
		// Instanciate the kernel
		new xoops_kernel_Xoops2( $hostId, $vars );
		return true;
	}

}



/**
* Initialize and boot the XOOPS kernel upon inclusion of this file
*/
global $xoops;

if ( !isset( $GLOBALS['xoops'] ) ) {
	xoops2_create_kernel();
}
return ( php_sapi_name() !== 'cli' ? $GLOBALS['xoops']->boot() : true );

?>