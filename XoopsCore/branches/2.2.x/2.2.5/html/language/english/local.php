<?php
/**
 * Xoops multi-language string and encoding handling class
 *
 * @copyright	 The XOOPS project http://www.xoops.org/
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package      language
 * @version      $Id$
 * @author       D.J. (phppp)
 * @since        2.2
 */
 
/**
 * The class should be an abstract one using PHP embedded functions
 * based on which, 
 * each local language defines its own equalient methods
 *
 * A comprehensive handler is expected in Xoops 2.3 or 2.4
 */

class XoopsLocal
{	
	// localized substr
	function substr($str, $start, $length, $trimmarker = '...')
	{
	    if ( !XOOPS_USE_MULTIBYTES ) {
	        return ( strlen($str) - $start <= $length ) ? substr( $str, $start, $length ) : substr( $str, $start, $length - strlen($trimmarker) ) . $trimmarker;
	    }
	    if (function_exists('mb_internal_encoding') && @mb_internal_encoding(_CHARSET)) {
	        $str2 = mb_strcut( $str , $start , $length - strlen( $trimmarker ) );
	        return $str2 . ( mb_strlen($str)!=mb_strlen($str2) ? $trimmarker : '' );
	    }
	}
	
	// Each local language should define its own equalient utf8_encode 
	function utf8_encode($text)
	{
		return utf8_encode($text);
    }
	
	function convert_encoding($text, $to='utf-8', $from='')
	{
		if(empty($text)) {		
			return $text;
		}
	    if(empty($from)) $from = _CHARSET;
	    if (empty($to) || !strcasecmp($to, $from)) return $text;
	
		if(XOOPS_USE_MULTIBYTES && function_exists('mb_convert_encoding')) $converted_text = @mb_convert_encoding($text, $to, $from);
		else
		if(function_exists('iconv')) $converted_text = @iconv($from, $to . "//TRANSLIT", $text);
		$text = empty($converted_text)?$text:$converted_text;
	
	    return $text;
	}

	function trim($text)
	{
	    $ret = trim($text);
	    return $ret;
	}
	
	/*
	* Function to display formatted times in user timezone
	*/
	function formatTimestamp($time, $format="l", $timeoffset="")
	{
	    global $xoopsConfig, $xoopsUser;
	    if(strtolower($format) == "rss" ||strtolower($format) == "r"){
        	$TIME_ZONE = "";
        	if(!empty($GLOBALS['xoopsConfig']['server_TZ'])){
				$server_TZ = abs(intval($GLOBALS['xoopsConfig']['server_TZ']*3600.0));
				$prefix = ($GLOBALS['xoopsConfig']['server_TZ']<0)?" -":" +";
				$TIME_ZONE = $prefix.date("Hi",$server_TZ);
			}
			$date = gmdate("D, d M Y H:i:s", intval($time)).$TIME_ZONE;
			return $date;
    	}
    	
	    $usertimestamp = xoops_getUserTimestamp($time, $timeoffset);
	    switch (strtolower($format)) {
	        case 's':
	        $datestring = _SHORTDATESTRING;
	        break;
	        case 'm':
	        $datestring = _MEDIUMDATESTRING;
	        break;
	        case 'mysql':
	        $datestring = "Y-m-d H:i:s";
	        break;
	        case 'rss':
	    	$datestring = "r";
	        break;
	        case 'l':
	        $datestring = _DATESTRING;
	        break;
	        case 'c':
	        case 'custom':	        
	        $current_timestamp = xoops_getUserTimestamp(time(), $timeoffset);
	        if(date("Ymd", $usertimestamp) == date("Ymd", $current_timestamp)){
				$datestring = _TODAY;
			}elseif(date("Ymd", $usertimestamp+24*60*60) == date("Ymd", $current_timestamp)){
				$datestring = _YESTERDAY;
			}elseif(date("Y", $usertimestamp) == date("Y", $current_timestamp)){
				$datestring = _MONTHDAY;
			}else{
				$datestring = _YEARMONTHDAY;
			}
	        break;
	        default:
	        if ($format != '') {
	            $datestring = $format;
	        } else {
	            $datestring = _DATESTRING;
	        }
	        break;
	    }
	    return ucfirst(date($datestring, $usertimestamp));
	}
	
	
	// adding your new functions
	// calling the function:
	// Method 1: echo xoops_local("hello", "Some greeting words");
	// Method 2: echo XoopsLocal::hello("Some greeting words");
	function hello($text)
	{
		$ret = "<div>Hello, ".$text."</div>";
		return $ret;
	}
}
?>