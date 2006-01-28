<?php
/**
* XOS class declaration
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright    The XOOPS project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @package		exxos
* @subpackage	exxos_XOS
* @since        2.3.0
* @version		$Id$
* @internal     This one should stay as small and efficient as possible: flexibility must be encapsulated into evolution
*/

class XOS {
	/**
	* Currently loaded components registry
	*/
	var $registry =		array();
	/**
	* Available components factories
	*/
	var $factories =	array();
	/**
	* The currently running services (singletons)
	*/
	var $services =		array();
	/**
	 * Path handler used to convert registry paths to physical ones (if necessary)
	 *
	 * @var object
	 */
	var $pathHandler =	false;
	/**
	 * Internal look-up table used to determine which components definition have already been imported
	 *
	 * @var		array
	 * @access	private
	 */
	var $imported =		array();
	
	/**
	 * Import a specific component definition
	 */
	function import( $bundleId ) {
		$me =& $GLOBALS[EXXOS];

		if ( !isset( $me->registry[$bundleId] ) ) {
			return false;
		}
		if ( isset( $me->imported[$bundleId] ) ) {
			return $me->imported[$bundleId];
		}
		if ( isset( $me->registry[$bundleId]['xoClassPath'] ) ) {
			$path = $me->registry[$bundleId]['xoBundleRoot'] . $me->registry[$bundleId]['xoClassPath'];
			if ( $me->pathHandler ) {
				$path = $me->pathHandler->path( $path );
			}
			if ( include_once( $path ) ) {
				return $me->imported[$bundleId] = true;
			}
			return $me->imported[$bundleId] = false;
		}
	}

	/**
	 * Get a class variable value
	 */
	function classVar( $id, $prop ) {
		$me =& $GLOBALS[EXXOS];
		
		if ( !isset( $me->registry[$id] ) ) {
			return null;
		}
		if ( isset( $me->registry[$id][$prop] ) ) {
			return $me->registry[$id][$prop];
		}
		XOS::import( $id );
		$vars = get_class_vars( $id );
		return $vars[$prop];
	}
	
	/**
	 * Create an object instance
	 * 
	 * This function will delegate object instanciation to a local factory if one has been
	 * specified. It is also able to handle singletons: when a singleton is requested
	 * it will check if it has not been already created and return a reference to the already
	 * existing instance if it has.
	 */
	function &create( $id, $options = null, $args = null ) {
		$me =& $GLOBALS[EXXOS];
		$inst = false;
		if ( is_array( $id ) ) {
			@list( $id, $options, $initArgs ) = $id;
		}
		if ( isset( $me->services[$id] ) ) {
			return $me->services[$id];
		}
		XOS::import($id);
		if ( isset( $me->registry[$id] ) ) {
			if ( !isset($me->factories[$id]) && isset($me->registry[$id]['xoFactory']) ) {
				$me->factories[$id] =& XOS::create( $me->registry[$id]['xoFactory'] );
				unset($me->registry[$id]['xoFactory']);
			}
			if ( @is_object( $me->factories[$id] ) ) {
				if ( method_exists( $me->factories[$id], 'createInstanceOf' ) ) {
					$inst =& $me->factories[$id]->createInstanceOf( $id, $options );
				} else {
					$inst =& $me->factories[$id]->createInstance( $options );
				}
			} else {
				$inst =& XOS::createInstanceOf( $id, $options, $args );
			}
			if ( is_object( $inst ) ) {
				if ( @$inst->xoSingleton ) {
					$me->services[ $id ] =& $inst;
					if (!@empty( $options[ 'xoServiceName' ] ) ) {
						$me->services[ $options[ 'xoServiceName' ] ] =& $inst;
					}
				}
			}
		}
		return $inst;
	}

	/**
	 * Create an instance of the specified class
	 * 
	 * This method is internally called by 'create' to create classes instances.
	 * It can also be used by local factories.
	 */
	function &createInstanceOf( $class, $options = null, $args = null ) {
		$inst = false;
		if ( !class_exists($class) ) {
			trigger_error( "Unknown class $class", E_USER_WARNING );
			return $inst;
		}
		$inst =& new $class();
		if ( is_object( $inst ) ) {
			// Set specified properties values
			XOS::apply( $inst, $options );
			XOS::apply( $inst, $GLOBALS[EXXOS]->registry[$class] );
			$inst->xoBundleIdentifier = $class;
			// Initialize the component instance
			if ( method_exists( $inst, 'xoInit' ) ) {
				if ( !$inst->xoInit( $options ) ) {
					return $inst;
				}
			}
			return $inst;
		}
		return $inst;
	}

	/**
	* Set several properties of an object
	*/
	function apply( &$inst, $props = null ) {
		if ( is_object( $inst ) ) {
			if ( isset( $props ) && is_object( $props ) ) {
				$props = get_object_vars($props);
			}
			if ( !empty($props) ) {
				foreach ( $props as $p => $v ) {
					if ( is_callable( array( &$inst, $method = 'set' . ucfirst($p) ) ) ) {
						$inst->$method( $v );
					} else {
						$inst->$p = $v;
					}
				}
			}
		}
	}
	
}

?>