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
 * Collects information for a page request
 *
 * <b>Singelton:</b> There can be only one instance of this class and it must
 * be accessed through the {@link instance()} method!
 *
 * records information about database queries, blocks, and execution time
 * and can display it as HTML
 *
 * @author  Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 *
 * @package kernel
 */
class XoopsLogger
{
    /**#@+
     * @var array
     */
    var $queries = array();
    var $blocks = array();
    var $extra = array();
    var $logstart = array();
    var $logend = array();
    var $executions = array();
    /**#@-*/
    
    /**
    * @var string
    */
    var $context;

    /**
     * constructor
     *
     * @access  private
     */
    function XoopsLogger()
    {

    }

    /**
     * get a reference to the only instance of this class
     *
     * @return  object XoopsLogger  reference to the only instance
     */
    function &instance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new XoopsLogger();
            $instance->context = "core";
        }
        return $instance;
    }

    /**
     * start a timer
     *
     * @param   string  $name   name of the timer
     * @param   bool    $execution  whether to count this timer in the execution time
     *
     */
    function startTime($name = 'XOOPS', $execution = true)
    {
        if ($execution) {
            $this->executions[$name] = true;
        }
        $this->logstart[$name] = explode(' ', microtime());
    }

    /**
     * stop a timer
     *
     * @param   string  $name   name of the timer
     */
    function stopTime($name = 'XOOPS')
    {
        $this->logend[$name] = explode(' ', microtime());
    }

    /**
     * log a database query
     *
     * @param   string  $sql    SQL string
     * @param   string  $error  error message (if any)
     * @param   int     $errno  error number (if any)
     * @param   int     $sql_time  query execution time (if any)
     */
    function addQuery($sql, $error=null, $errno=null, $sql_time = null)
    {
        $this->queries[$this->context][] = array('sql' => $sql, 'error' => $error, 'errno' => $errno, 'sql_time' => $sql_time);
    }

    /**
     * log display of a block
     *
     * @param   string  $name       name of the block
     * @param   bool    $cached     was the block cached?
     * @param   int     $cachetime  cachetime of the block
     */
    function addBlock($name, $cached = false, $cachetime = 0, $queries = 0)
    {
        $this->blocks[] = array('name' => $name, 'cached' => $cached, 'cachetime' => $cachetime, 'queries' => $queries);
    }

    /**
     * log extra information
     *
     * @param   string  $name       name for the entry
     * @param   int     $msg  text message for the entry
     */
    function addExtra($name, $msg)
    {
        $this->extra[] = array('name' => $name, 'msg' => $msg);
    }

    /**
     * get the logged queries in a HTML table
     *
     * @param string $context which context to get
     *
     * @return  string  HTML table with queries
     */
    function dumpQueries($context)
    {
        $ret = '<table class="outer" width="100%" cellspacing="1"><tr><th>Queries '.$context.'</th></tr>';
        $class = 'even';
        $sql_time_total = array();
        if (isset($this->queries[$context])) {
 		foreach ($this->queries[$context] as $q) {
 			if (isset($q['error'])) {
				$ret .= '<tr class="'.$class.'"><td><span style="color:#ff0000;">'.htmlentities($q['sql']).'<br /><b>Error number:</b> '.$q['errno'].'<br /><b>Error message:</b> '.$q['error'];
			} else {
				$ret .= '<tr class="'.$class.'"><td>'.htmlentities($q['sql']);
			}
			if(isset($q['sql_time'])){
			    $ret .= '<br />( '.round($q['sql_time'], 6).')';
			}
			$ret .= '</span></td></tr>';
			$class = ($class == 'odd') ? 'even' : 'odd';
			if(isset($q['sql_time'])){
				$sql_time_total[] = $q['sql_time'];
			}
 		}
		$ret .= '<tr class="foot"><td>Total Number of Queries: <span style="color:#ff0000;">'.count($this->queries[$context]).'</span>';
        }
        else {
            $ret .= '<tr class="foot"><td>Total Number of Queries: <span style="color:#ff0000;">0</span>';
        }
		$ret .= '&nbsp;&nbsp;|&nbsp;&nbsp;Total SQL Generation Time: <span style="color:#ff0000;">'.round(array_sum($sql_time_total), 6).'</span>';
		$ret .= ' </td></tr></table><br />';
        return $ret;
    }

    /**
     * get the logged blocks in a HTML table
     *
     * @return  string  HTML table with blocks
     */
    function dumpBlocks()
    {
        $ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Blocks</th></tr>';
        $class = 'even';
        foreach ($this->blocks as $b) {
            if ($b['cached']) {
                $ret .= '<tr><td class="'.$class.'"><b>'.$b['name'].':</b> Cached (regenerates every '.intval($b['cachetime']).' seconds)</td></tr>';
            } else {
                $ret .= '<tr><td class="'.$class.'"><b>'.$b['name'].':</b> ('.$b['queries'].' Queries) No Cache</td></tr>';
            }
            $class = ($class == 'odd') ? 'even' : 'odd';
        }
        $ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->blocks).'</span> blocks</td></tr></table><br />';
        return $ret;
    }

    /**
     * get the current execution time of a timer
     *
     * @param   string  $name   name of the counter
     * @return  float   current execution time of the counter
     */
    function dumpTime($name = 'XOOPS')
    {
        if (!isset($this->logstart[$name])) {
            return 0;
        }
        if (!isset($this->logend[$name])) {
            $stop_time = explode(' ', microtime());
        } else {
            $stop_time = $this->logend[$name];
        }
        return ((float)$stop_time[1] + (float)$stop_time[0]) - ((float)$this->logstart[$name][1] + (float)$this->logstart[$name][0]);
    }

    /**
     * get extra information in a HTML table
     *
     * @return  string  HTML table with extra information
     */
    function dumpExtra()
    {
        $ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Extra</th></tr>';
        $class = 'even';
        foreach ($this->extra as $ex) {
            $ret .= '<tr><td class="'.$class.'"><b>'.htmlspecialchars($ex['name']).':</b> '.htmlspecialchars($ex['msg']).'</td></tr>';
            $class = ($class == 'odd') ? 'even' : 'odd';
        }
        $ret .= '</table><br />';
        return $ret;
    }

    /**
     * get all logged information formatted in HTML tables
     *
     * @return  string  HTML output
     */
    function dumpAll()
    {
        $ret = $this->dumpQueries('core');
        $ret .= $this->dumpQueries('module');
        $ret .= $this->dumpQueries('block');
        $ret .= $this->dumpBlocks();
        if (count($this->logstart) > 0) {
            $ret .= '<table class="outer" width="100%" cellspacing="1"><tr><th>Execution Time</th></tr>';
            $class = 'even';
            foreach (array_keys($this->executions) as $k) {
                $ret .= '<tr><td class="'.$class.'"><b>'.htmlspecialchars($k).'</b> took <span style="color:#ff0000;">'.$this->dumpTime($k).'</span> seconds to load.</td></tr>';
                $class = ($class == 'odd') ? 'even' : 'odd';
            }
            $ret .= '</table><br />';
        }
        $ret .= $this->dumpExtra();
        return $ret;
    }
    
    /**
    * Returns javascript for the SQL debug window
    *
    * @return string
    **/
    function getSQLDebug() {
        global $xoopsConfig;
        $ret = '<script type="text/javascript">
        		<!--//
        		debug_window = openWithSelfMain("", "popup", 680, 450, true);
        		debug_window.document.clear();
        		';
        $content = '<html><head>
                    <meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" />
                    <meta http-equiv="content-language" content="'._LANGCODE.'" />
                    <title>'.$xoopsConfig['sitename'].'</title>
                    <link rel="stylesheet" type="text/css" media="all" href="'.xoops_getcss($xoopsConfig['theme_set']).'" />
                    </head>
                    <body>'.$this->dumpAll().'
                    <div style="text-align:center;">
                    <input class="formButton" value="'._CLOSE.'" type="button" onclick="javascript:window.close();" />
                    </div>
                    </body>
                    </html>';
        $lines = preg_split("/(\r\n|\r|\n)( *)/", $content);
        foreach ($lines as $line) {
            $ret .= 'debug_window.document.writeln("'.str_replace('"', '\"', $line).'");';
        }
        //$ret .= 'debug_window.focus();';
        $ret .= 'debug_window.document.close();
                window.focus();
        		//-->
        		</script>';
        return $ret;
    }
}
?>