<?php
/**
 * xoops_core_PreferencesHandler main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_core
 * @subpackage	xoops_core_PreferencesHandler
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'xoops_db_Accessor' );


define( 'XO_PREFS_HOST', 1 );
define( 'XO_PREFS_BUNDLE', 2 );
define( 'XO_PREFS_USER', 4 );

define( 'XO_PREFS_ANYHOST', '' );
define( 'XO_PREFS_ANYUSER', 0 );
define( 'XO_PREFS_CURRENTHOST', 1 );
define( 'XO_PREFS_CURRENTUSER', 1 );

/**
 * XOOPS default preferences handler
 * 
 * The preferences system allows you to store custom properties values for XOS objects. These settings will
 * then be automatically applied to new objects when you instanciate them. Preferences are assigned a scope 
 * using a combination of username, component ID, host name and language. This mechanism allows you to create
 * preferences which apply to different domains.
 * 
 * For example, using preferences you can save a preference value that applies to:
 * - The current user on the current host
 * - All users on a specific host
 * - The current user on any host, when using a specific language
 * - Any user on any host, when using a specific language
 * 
 * The PreferencesHandler has a high-level API which makes it very simple to store and retrieve components
 * preferences using the default scope (current user, any host) which is appropriate for the majority of 
 * situations, as well as a low-level API which allows you to specify the exact scope of the preferences you 
 * want to manipulate when necessary.
 * 
 * NB: Preferences are normally managed automatically by the system, so you should not have to deal with this
 * class by yourself, unless you are making a {@link xoops_panel_ConfigurationPanel Configuration Panel}.
 *
 * @package		xoops_core
 * @subpackage	xoops_core_PreferencesHandler
 * @devstatus	unfinished
 */
class xoops_core_PreferencesHandler extends xoops_db_Accessor {

	var $tables = array( 'sys_preference' );
	
	
	var $user = false;
	
	
	var $hostName = false;

	var $language = false;
	
	var $prefsValues = array();

	/**
	 * Folder to read managed preferences from
	 *
	 * @var string
	 */
	var $preferencesFolder = '/XOOPS/Preferences/';
	
	/**#@+ @tasktype 10 Initialization*/
	function xoInit( $options = array() ) {
		if ( !$this->db ) {
			$this->db =& XOS::create( 'xoops_db_Database' );
		}
		$table = $this->db->prefix( 'sys_preference' );
		$selectPred = "(`bundleId`='' OR `bundleId`=?) AND (`hostName`='' OR `hostName`=?) AND (`language`='' OR `language`=?)";
		
		$this->queries = array(
			'getPrefs' => "SELECT * FROM `$table` WHERE `userId`=? AND $selectPred ORDER BY `weight`,`language`",
			'delPrefs' => "DELETE FROM `$table` WHERE `bundleId`=:bundle AND `userId`=:user AND `hostName`=:host",
			'insertScalar' =>
				"INSERT INTO `$table` (`weight`,`bundleId`,`userId`,`hostName`,`language`,`propertyName`,`scalarValue`,`complexValue`) " .
				"VALUES ",
			'insertComplex' =>
				"INSERT INTO `$table` (`weight`,`bundleId`,`userId`,`hostName`,`language`,`propertyName`,`scalarValue`,`complexValue`) " .
				"VALUES ",
		);
		return true;
	}
	/**#@-*/

	/**#@+ @tasktype 20 Getting preferences values*/
	/**
	 * Obtains a preference value for the specified key and object
	 *
	 * @param string $key The preference key whose value to obtain
	 * @param string $bundleId The identifier of the object whose preferences to search
	 * @return mixed The preference data for the specified key and object. If no value was located, returns null
	 */
	function getObjectValue( $key, $bundleId ) {
		return $this->getValue( $key, $bundleId, XO_PREFS_CURRENTUSER, XO_PREFS_ANYHOST );
	}
	/**
	 * Obtains a preference value for the specified domain
	 *
	 * @param string $key The preference key whose value to obtain
	 * @param string $bundleId The identifier of the object whose preferences to search
	 * @param integer $userID XO_PREFS_CURRENTUSER if to search the current-user domain, otherwise XO_PREFS_ANYUSER
	 * @param unknown_type $hostName
	 */
	function getValue( $key, $bundleId = '', $userId = XO_PREFS_CURRENTUSER, $hostName = XO_PREFS_ANYHOST ) {
		$vars =& $this->loadValues( $bundleId, $userId, $hostName );
		return $vars ? @$vars[$key] : null;
	}
	/**
	 * Convenience function that allows you to obtain multiple preference values
	 *
	 * @param string $key An array of preference keys the values of which to obtain
	 * @param string $bundleId The identifier of the object whose preferences to search
	 * @param integer $userId XO_PREFS_CURRENTUSER if to search the current-user domain, otherwise XO_PREFS_ANYUSER
	 * @param unknown_type $hostName
	 */
	function getMultiple( $keys = array(), $bundleId = '', $userId = 0, $hostName = '' ) {
		$ptr =& $this->loadValues( $bundleId, $userId, $hostName );
		if ( empty($keys) ) {
			return $ptr;
		} else {
			$vars = array();
			foreach ( $keys as $key ) {
				if ( isset( $ptr[$key] ) ) {
					$vars[$key] = $ptr[$key];
				}
			}
			return $vars;
		}
	}
	/**#@-*/

	/**#@+ @tasktype 30 Setting preferences values*/
	/**
	 * Adds, modifies, or removes a preference
	 *
	 * @param string $key The preference key whose value you wish to set
	 * @param string $value The value to set for the specified key and object. Pass NULL to remove the specified key from the object’s preferences
	 * @param string $bundleId The identifier of the object whose preferences you wish to create or modify
	 * @return mixed The preference data for the specified key and object. If no value was located, returns null
	 */
	function setObjectValue( $key, $value, $bundleId, $localized = false ) {
		return $this->setValue( $key, $value, $bundleId, XO_PREFS_CURRENTUSER, XO_PREFS_ANYHOST );
	}
	/**
	 * Adds, modifies, or removes a preference value for the specified domain
	 *
	 * @param string $key The preference key whose value you wish to set
	 * @param string $value The value to set for the specified key and object. Pass NULL to remove the specified key from the specified domain
	 * @param integer $userId XO_PREFS_CURRENTUSER if to to modify the current user’s preferences, XO_PREFS_ANYUSER for all users
	 * @param unknown_type $hostName
	 */
	function setValue( $key, $value = null, $bundleId = '', $userId = 0, $hostName = 0, $localized = false ) {
		$vars =& $this->loadValues( $bundleId, $userId, $hostName );
		if ( isset($value) ) {
			$vars[$key] = $value;
			//$localizedKeys[$bundleId][$hostName] = $localized;
		} else {
			unset( $vars[$key] );
		}
		return $value;
	}
	/**
	 * Obtains a preference value for the specified domain
	 *
	 * @param string $key An array of preference keys the values of which to obtain
	 * @param string $bundleId The identifier of the object whose preferences to search
	 * @param integer $userId XO_PREFS_CURRENTUSER if to search the current-user domain, otherwise XO_PREFS_ANYUSER
	 * @param unknown_type $hostName
	 */
	function setMultiple( $values = array(), $bundleId = '', $userId = 0, $hostName = '', $localized = array() ) {
		$vars =& $this->loadValues( $bundleId, $userId, $hostName );
		if ( $vars ) {
			foreach ( $values as $key => $value ) {
				if ( isset($value) ) {
					$vars[$key] = $value;
					//$localizedKeys[$bundleId][$hostName] = $localized;
				} else {
					unset( $vars[$key] );
				}
			}
			return true;
		}
		return false;
	}
	/**#@-*/

	/**#@+ @tasktype 40 Saving preferences to persistent storage*/
	/**
	 * Writes to permanent storage all pending changes to the preference data for the specified object
	 *
	 * @param string $bundleId The ID of the object whose preferences to write to storage
	 */
	function synchronizeObject( $bundleId ) {
		return $this->synchronize( $bundleId, XO_PREFS_CURRENTUSER, XO_PREFS_ANYHOST );
	}
	/**
	 * Writes to permanent storage all pending changes to the preference data for the specified object and domain
	 *
	 * @param string $bundleId The ID of the object whose preferences to write to storage
	 * @param integer $userId XO_PREFS_CURRENTUSER if to search the current-user domain, otherwise XO_PREFS_ANYUSER
	 * @param unknown_type $hostName
	 */
	function synchronize( $bundleId, $userId = 0, $hostName = '' ) {
		$this->updateDatabaseValues( $bundleId, $userId, $hostName );
		//return $this->synchronize( $bundleId, XO_PREFS_CURRENTUSER, XO_PREFS_ANYHOST );
	}
	/**#@-*/
	/**#@+ @tasktype 50 Miscellaneous functions*/
	function objectValueIsForced( $key, $bundleId, $hostName = '' ) {
		$vars =& $this->loadValues( $bundleId, 0, $hostName );
		if ( $vars ) {
			return in_array( $key, $this->managedKeys[$bundleId][$hostName] );
		}
		return false;
	}
	
	function &loadValues( $bundleId = '', $userId = 0, $hostName = '' ) {
		global $xoops;

		$this->calculateDomainWeight( $bundleId, $userId, $hostName );
		$varsPointer = false;

		if ( !$userId ) {
			if ( !isset( $this->globalPrefs[$bundleId][$hostName] ) ) {
				$vars = $this->loadPersistentValues( $bundleId, $hostName );
				if ( !isset( $this->globalPrefs[$bundleId][''] ) ) {
					$this->globalPrefs[$bundleId][''] = $vars[0][''];
					$this->managedKeys[$bundleId][''] = $vars[1][''];
				}
				if ( !isset( $this->globalPrefs[$bundleId][$hostName] ) ) {
					$this->globalPrefs[$bundleId][$hostName] = $vars[0][$hostName];
					$this->managedKeys[$bundleId][$hostName] = $vars[1][$hostName];
				}
				$vars = $this->loadDatabaseValues( $bundleId, 0, $hostName );
				foreach ( $vars as $key => $value ) {
					if ( !in_array( $key, $this->managedKeys[$bundleId][$hostName] ) ) {
						$this->globalPrefs[$bundleId][$hostName][$key] = $value;
					}
				}
			}
			$varsPointer =& $this->globalPrefs[$bundleId][$hostName];
		} else {
			if ( !isset( $this->userPrefs[$bundleId][$hostName] ) ) {
				$this->userPrefs[$bundleId][$hostName] = array();
				$vars = $this->loadDatabaseValues( $bundleId, $userId, $hostName );
				foreach ( $vars as $key => $value ) {
					$this->userPrefs[$bundleId][$hostName][$key] = $value;
				}
			}
			$varsPointer =& $this->userPrefs[$bundleId][$hostName];
		}
		return $varsPointer;
	}

	/**
	 * Read all the keys of the specified domain from the database
	 *
	 * @param unknown_type $bundleId
	 * @param unknown_type $userId
	 * @param unknown_type $host
	 * @return unknown
	 * @access protected
	 */
	function loadDatabaseValues( $bundleId = '', $userId = 0, $host = '' ) {
		global $xoops;
		
		$this->calculateDomainWeight( $bundleId, $userId, $host );
		
		$rows = $this->fetchAll( 'getPrefs', array( $userId, $bundleId, $host, '' ) );
		$values = array();
		foreach ( $rows as $row ) {
			$values[ $row['propertyName'] ] = isset( $row['complexValue'] ) ? unserialize( $row['complexValue'] ) : $row['scalarValue'];
		}
		return $values;		
	}
	
	function calculateDomainWeight( &$bundleId, &$userId, &$hostName ) {
		global $xoops;
		$weight = 0;
		if ( !empty( $bundleId ) ) {
			$weight += XO_PREFS_BUNDLE;
		} else {
			$bundleId = '';
		}
		if ( $userId == XO_PREFS_CURRENTUSER ) {
			$userId = $xoops->currentUser->userId;
			$weight += XO_PREFS_USER;
		} else {
			$userId = 0;
		}
		if ( !empty( $hostName ) ) { 
			$hostName = $xoops->hostId;
			$weight += XO_PREFS_HOST;
		} else {
			$hostName = XO_PREFS_ANYHOST;
		}
		return $weight;
	}
	
	
	function updateDatabaseValues( $bundleId = '', $userId = 0, $hostName = '' ) {
		$weight = $this->calculateDomainWeight( $bundleId, $userId, $hostName );
		
		if ( !isset( $this->globalPrefs[$bundleId][$hostName] ) ) {
			trigger_error( "Cannot update preferences that have not been loaded", E_USER_NOTICE );
			return false;
		}
		// First, delete all the rows corresponding to the specified domain
		$count = $this->exec( 'delPrefs', array( ':bundle' => $bundleId, ':user' => $userId, ':host' => $hostName ) );

		$vars =& $this->globalPrefs[$bundleId][$hostName];
		$params = array( ':bundle' => $bundleId, ':user' => $userId, ':host' => $hostName, ':language' => '');
		$params = array_map( array( &$this->db, 'quote' ), $params );
		$scalarValues = array();
		$complexValues = array();
		$valString = '(' . $weight . ',' . implode( ',', $params );
		
		foreach ( $vars as $prop => $val ) {
			$prop = $this->db->quote( $prop );
			if ( ( is_string( $val ) && strlen( $val ) > 255 ) || is_array( $val ) ) {
				$complexValues[] = "$valString,$prop,''," . $this->db->quote( serialize( $val ) ) . ')';
			} else {
				$scalarValues[] = "$valString,$prop," . $this->db->quote($val) . ",NULL)";
			}
		}
		if ( !empty( $scalarValues ) ) {
			$this->db->exec( $this->queries['insertScalar'] . implode( ",", $scalarValues ) );
		}
		if ( !empty( $complexValues ) ) {
			$this->db->exec( $this->queries['insertComplex'] . implode( ",", $complexValues ) );
		}
	}
	/**
	 * Read persistent preferences definition
	 *
	 * @param unknown_type $bundleId
	 * @param unknown_type $hostName
	 * @access protected
	 */	
	function loadPersistentValues( $bundleId = '', $host = '' ) {
		global $xoops;
		if ( $bundleId && !XOS::import( $bundleId ) ) {
			trigger_error( "Cannot load persistant preferences of unknown type $bundleId", E_USER_WARNING );
			return false;
		}
		$dummy = 0;
		$this->calculateDomainWeight( $bundleId, $dummy, $host );
		
		
		$managed = $vars = array( '' => array() );

		if ( $bundleId && class_exists( $bundleId ) ) {
			$vars[''] = array_merge( $vars[''], get_class_vars( $bundleId ) );
		}
		if ( !$bundleId ) {
			$bundleId = 'global';
		}
		if ( $fromfile = @include $xoops->path( "/XOOPS/Preferences/$bundleId.php" ) ) {
			if ( !is_array( $fromfile ) ) {
				trigger_error( "Managed preferences file for $bundleId is corrupt", E_USER_WARNING );
			} else {
				$managed[''] = array_merge( $managed[''], array_keys( $fromfile ) );
				$vars[''] = array_merge( $vars[''], $fromfile );
			}
		}
		if ( $host ) {
			$managed[$host] = $managed[''];
			$vars[$host] = $vars[''];
			if ( $fromfile = @include $xoops->path( "/XOOPS/Preferences/ByHost/$host-$bundleId.php" ) ) {
				if ( !is_array( $fromfile ) ) {
					trigger_error( "Managed preferences file for $bundleId is corrupt", E_USER_WARNING );
				} else {
					$managed[$host] = array_merge( $managed[$host], array_keys( $fromfile ) );
					$vars[$host] = array_merge( $vars[$host], $fromfile );
				}
			}
		}
		return array( $vars, $managed );
	}
	/**#@-*/

}


?>