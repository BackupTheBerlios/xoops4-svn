<?php
/// $Id$
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
// Author: Jan Pedersen (AKA Mithrandir)                                     //
// URL: http://www.xoops.org                                                 //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

require_once XOOPS_ROOT_PATH."/class/snoopy/Snoopy.class.php";

class XoopsSnoopy extends Snoopy 
{
	
// Added on March 4, 2003 by onokazu@xoops.org
/*======================================================================*\
	Function:	set_submit_xml
	Purpose:	Set the submission content type to
				text/xml
\*======================================================================*/
	function set_submit_xml()
	{
		$this->_submit_type = "text/xml";
	}


/*======================================================================*\
	Function:	_prepare_post_body
	Purpose:	Prepare post body according to encoding type
	Input:		$formvars  - form variables
				$formfiles - form upload files
	Output:		post body
\*======================================================================*/
	
	function _prepare_post_body($formvars, $formfiles)
	{
		switch ($this->_submit_type) {
			case "application/x-www-form-urlencoded":
			case "multipart/form-data":
			    return parent::_prepare_post_body($formvars, $formfiles);

			// Added on March 4, 2003 by onokazu@xoops.org
			case "text/xml":
			default:
    			settype($formvars, "array");
    			settype($formfiles, "array");
    
    			if (count($formvars) == 0 && count($formfiles) == 0) {
    			    return;
    			}
				$postdata = $formvars[0];
				break;
		}

		return $postdata;
	}
}

?>