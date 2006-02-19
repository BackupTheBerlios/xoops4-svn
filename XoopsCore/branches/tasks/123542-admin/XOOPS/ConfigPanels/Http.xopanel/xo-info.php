<?php
/**
* xoops_http_HttpPanel bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright	The XOOPS project http://www.xoops.org/
* @license		http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since		2.3.0
* @package		xoops_http
* @package		xoops_http_HttpPanel
* @version		$Id$
*/

return array(
	'xoBundleDisplayName' => 'HTTP services configuration panel',
	'xoBundleIdentifier' => 'xoops_http_HttpConfigPanel',
	'xoClassPath' => '/http-panel.php',

	'configPanelRoot' => 'system/server',
	'configPanelNode' => 'http',
	'configPanelIcon' => 'www/config-system-http.png',
	'configPanelLabel' => 'HTTP',
	'configPanelDesc' => 'Configure redirections, HTTP compression and default (anonymous) caching headers',
);

?>