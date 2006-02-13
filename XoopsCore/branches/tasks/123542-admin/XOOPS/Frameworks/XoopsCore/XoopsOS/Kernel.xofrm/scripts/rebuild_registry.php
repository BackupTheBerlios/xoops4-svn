<?php
/**
* XOOPS kernel registry building script
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* This script scans the XOOPS core components folders (lib and modules) and build a lookup table
* (the components registry) that will be used by Exxos to know what components are available in
* the system.
* The use of such a system has some drawbacks (like higher memory comsumption), but as it comes
* with performance gains in other parts of 2.3.0, it can be considered acceptable.
* Also, THIS WILL BE REMOVED in a later version, once Exxos implements real evolution patterns.
* 
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @package		xoops_kernel
* @since        2.3.0
* @version		$Id$
*/
/**
 * This file cannot be requested directly
 */
if ( !defined( 'XO_MODE_DEV' ) ) exit();


function xoScanComponentsFolder( $registry, $path, $recurse = false ) {
	global $xoops;

	$realpath = $xoops->path( $path );
	if ( $dh = opendir( $realpath ) ) {
		while ( $file = readdir( $dh ) ) {
			if ( $file{0} != '.' && is_dir( "$realpath/$file" ) ) {
				$local = @include "$realpath/$file/xo-info.php";
				if ( is_array($local) && isset( $local["xoBundleIdentifier"] ) ) {
					$registry = xoRegisterComponent( $registry, $local, "$path/$file" );
				} elseif ( $recurse ) {
					$registry = xoScanComponentsFolder( $registry, "$path/$file", true );
				}
			}
		}
		closedir($dh);
	}
	return $registry;
}

function xoRegisterComponent( $registry, $bundleInfo, $bundleRoot, $prefix = '' ) {
	global $xoops;

	$isModule = ( substr( $bundleInfo['xoBundleIdentifier'], 0, 4 ) == 'mod_' );
	
	$services = array();
	if ( isset( $bundleInfo['xoServices'] ) ) {
		foreach ( $bundleInfo['xoServices'] as $localId => $localInfo ) {
			if ( @$subRoot = $localInfo['xoBundleRoot'] ) {
				if ( $localInfo = include $xoops->path( $bundleRoot . $subRoot . '/xo-info.php' ) ) {
					if ( !$isModule ) {
						$localInfo = xoRegisterComponent( array(), $localInfo, $bundleRoot . $subRoot );
					} else {
						$localInfo = xoRegisterComponent( array(), $localInfo, $bundleRoot . $subRoot, $bundleInfo['xoBundleIdentifier'] . '#' );
					}
					$services = array_merge( $services, $localInfo );
				}
			} else {
				$localInfo['xoBundleRoot'] = $bundleRoot;
				if ( $isModule ) {
					$localInfo['xoBundleIdentifier'] = $localId;
					$localId = $bundleInfo['xoBundleIdentifier'] . '#' . $localId;
				}
				if ( is_array( $localInfo ) ) {
					$services[$localId] = $localInfo;
				}
			}
		}
		unset( $bundleInfo['xoServices'] );
	}
	$bundleId = $bundleInfo['xoBundleIdentifier'];
	unset( $bundleInfo['xoBundleIdentifier'] );
	$bundleInfo['xoBundleRoot'] = $bundleRoot;
	foreach ( $bundleInfo as $k => $v ) {
		if ( substr( $k, 0, 2 ) != 'xo' ) {
			unset( $bundleInfo[$k] );
		}
	}

	$services[$bundleId] = $bundleInfo;
	return array_merge( $registry, $services );
}

$registry = array();
$registry = xoScanComponentsFolder( $registry, '/XOOPS/Frameworks', true );
$registry = xoScanComponentsFolder( $registry, '/XOOPS/ConfigPanels', true );
$registry = xoScanComponentsFolder( $registry, '/www/modules', true );

if ( $fp = fopen( $this->path( '/var/Application Support/xoops_kernel_Xoops2/registry.php' ), 'wt' ) ) {
	fwrite( $fp, "<?php\nreturn " . var_export( $registry, true ) . ";\n?>" );
	fclose( $fp );
}
return $registry;

?>