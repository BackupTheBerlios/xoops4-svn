<?php
/**
* xoops_http framework bundle information file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright	The XOOPS project http://www.xoops.org/
* @license		http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since		2.3.0
* @package		xoops_http
* @version		$Id$
*/

// xoBundleVersion: x.y.zRn  with R=release type (a:cvs snapshot, b:alpha, c:beta, d: RC, e: final)
// We must be able to compare versions by comparing this string

return array(
	'xoBundleDisplayName'			=> 'Xoops HTTP framework',
	'xoBundleIdentifier'			=> 'xoops_http',

	'xoServices' => array(
		'xoops_http_HttpHandler'	=> array ( 'xoBundleRoot' => '/http.xoobj' ),
		'xoops_http_SessionService'	=> array ( 'xoBundleRoot' => '/session.xoobj' ),
	),
);

?>