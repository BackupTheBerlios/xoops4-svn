<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

/**
 * THIS CLASS IS DEPRECATED.
 * Use the logger service features instead ( $xoops->services['logger'] )
 */
class XoopsLogger {

	function XoopsLogger() {
	}
    function &instance() {
        static $instance;
        if (!isset($instance)) {
            $instance =& new XoopsLogger();
        }
        return $instance;
    }
    function startTime( $name = 'XOOPS' ) {
    	if ( $GLOBALS['xoops']->services['logger'] ) {
    		$GLOBALS['xoops']->services['logger']->startTimer( $name );
    	}
    }
    function stopTime($name = 'XOOPS') {
    	if ( $GLOBALS['xoops']->services['logger'] ) {
    		$GLOBALS['xoops']->services['logger']->stopTimer( $name );
    	}
    }
    function addQuery($sql, $error=null, $errno=null) {
    	if ( $GLOBALS['xoops']->services['logger'] ) {
    		if ( !isset( $error ) ) {
    			$GLOBALS['xoops']->services['logger']->logEvent( $sql, 'query' );
    		} else {
    			$GLOBALS['xoops']->services['logger']->logEvent( "Database error: $sql ($errno: $error)", 'query' );
    		}
    	}
    }
    function addBlock($name, $cached = false, $cachetime = 0) {
    	if ( $GLOBALS['xoops']->services['logger'] ) {
    		if ( !$cached ) {
    			$GLOBALS['xoops']->services['logger']->logEvent( "$name (not cached)", 'block' );
    		} else {
    			$GLOBALS['xoops']->services['logger']->logEvent( "$name (from cache, caching time: $cachetime)", 'block' );
    		}
    	}
    }
    function addExtra($name, $msg) {
    	if ( $GLOBALS['xoops']->services['logger'] ) {
   			$GLOBALS['xoops']->services['logger']->logEvent( "$name: $msg", '' );
    	}
    }
}

?>