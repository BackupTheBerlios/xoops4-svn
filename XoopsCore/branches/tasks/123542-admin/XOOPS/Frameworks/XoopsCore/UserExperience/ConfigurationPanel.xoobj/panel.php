<?php
/**
 * xoops_panel_ConfigurationPanel component main class file
 *
 * @copyright	The Xoops project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @since       2.3.0
 * @version		$$
 * @package     xoops_panel
 * @subpackage  xoops_panel_ConfigurationPanel
 */

XOS::import( 'xoops_app_PageController' );

/**
 * Base class for configuration panels
 * 
 * The page controller receives a page request, extracts any relevant data,
 * invokes any updates to the model, and if necessary forwards the request to the view.
 */
class xoops_panel_ConfigurationPanel extends xoops_app_PageController {
	/**
	 * Configuration handler instanciated by the container application
	 * @var object
	 */
	var $configHandler = false;

}

?>