<?php
/**
 * XOOPS hosts definition file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * NB: This file is upside down right now ;-)
 * 
 * In the future this is where sites will be defined, and the XOOPS_XXX constants will be created
 * from its content when BC is required.
 * Also, as some might be able to see: we're making this "ready" for multi-hosting, but it's not
 * really supported yet (don't worry: you'll be told about that ;-))
 * 
 * @copyright    The Xoops project http://www.xoops.org/
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since        2.3.0
 * @version		$Id$
 */

return array(
	// ID of the default site
	''					=> 'default.site',
	// Hosts definitions
	'default.site'		=> array(
		// URIs under which this host can be accessed (1st one is default, others are aliases)
		'hostLocation'		=> array(
			substr( XOOPS_URL, strpos( XOOPS_URL, '://' ) + 3 ),
		),
		// Host locations when using secure (HTTPS) access
		// If secure access if not supported, set secureName to an empty array
		// otherwise, this array should have the same number of elements as 'hostLocation'
		'secureLocation'	=> array(
			substr( XOOPS_URL, strpos( XOOPS_URL, '://' ) + 3 ),
		),
		// Paths/URL to use to access the XOOPS folders
		// For each path, the 1st element is the physical path, the 2nd one is the URI
		// URIs without a leading slash are considered relative to the current XOOPS host location
		// URIs with a leading slash are considered semi-relative (you must setup approp rewriting rules in your server conf)
		'paths'			=> array(
			'XOOPS'			=> array( XOOPS_PATH,						'library.php' ),
			'modules'		=> array( XOOPS_ROOT_PATH . '/modules',		'modules' ),
			'themes'		=> array( XOOPS_ROOT_PATH . '/themes',		'themes' ),
			'var'			=> array( XOOPS_VAR_PATH,					null ),
			'www'			=> array( XOOPS_ROOT_PATH,					'' ),
		),
		// Kernel boot parameters
		'bootFile'		=> 'rc.php',
		'xoRunMode'		=> XO_MODE_DEV,
	),
);

?>