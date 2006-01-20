<?php
/**
 * xoops_kernel_Logger component main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_kernel
 * @package		xoops_kernel_Logger
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

/**
 * xoops_kernel_Logger (default logger service) implementation
 *
 * <p>The logger service is a standard service of the XOOPS system, and thus can be accessed using $xopps->services['logger'].
 * It records information about any kind of events using its logEvent() method, and provides facilities to handle an
 * named timers using the startTimer() and stopTimer() methods.</p>
 *
 * <p>This logger only gets activated by default when the kernel runs in DEBUG or DEV mode on.
 * If it is the case, it'll start buffering output to be able toinsert its output inside the page once
 * the request has been processed.</p>
 */
class xoops_kernel_Logger {
	/**
	 * Whether or not to keep record of events
	 * @var boolean
	 */
 	var $activated = false;
	/**
	 * Recorded events, grouped by category
	 * @var array
	 */
	var $events = array();
	/**
	 * Available timers
	 * @var array
	 */
	var $timers = array();

	function xoInit( $options = array() ) {
		ob_start();
	 	return true;
	}
	/**
	 * Shutdown this instance
	 *
	 * On shutdown, all remaining timers are stopped and the events log is rendered to the output.
	 * If the <code>&lt;!--{xo-logger-output}--&gt;</code> string is found within the output content,
	 * the logger will replace it with its own output.
	 * Otherwise, events will be shown after the page content.
	 */
	function xoShutdown() {
	  	$now = $this->microtime();
		foreach ($this->timers as $k => $v ) {
			if ( !isset( $v['stop'] ) ) {
				$this->timers[$k]['stop'] = $now;
			}
		}
	  	$output = '';

	 	while ( false !== ( $str = ob_get_contents() ) ) {		// Clean all open output buffers and reconstruct output :-)
	 		$output = $str . $output;
	 		ob_end_clean();
		}
		if ( $this->activated ) {
			$this->renderEvents( $output );
		}
	}
	/**
	 * Returns the current microtime in seconds.
	 * @return float
	 */
	function microtime() {
		$now = explode( ' ', microtime() );
		return (float)$now[0] + (float)$now[1];
	}
	/**
	 * Adds an event to the log
     * @param	string	$message	Event information
     * @param	string  $cat		Event category
	 */
	function logEvent( $message, $category = '' ) {
		if ( $this->activated ) {
			if ( !isset( $this->events[$category] ) ) {
				$this->events[$category] = array();
			}
			$this->events[$category][] = array( 'message' => $message, 'time' => $this->microtime() );
		}
	}
	/**
	 * Starts a timer
     * @param	string  $name   name of the timer
	 */
	function startTimer( $name = 'XOOPS' ) {
		$this->timers[$name] = array( 'start' => $this->microtime() );
	}
	/**
	 * Stops a timer
     * @param	string  $name   Name of the timer
     * @param	boolean $clear  Whether or not we want to keep this timer afterwards
     * @return float The duration of the stopped timer (in seconds)
	 */
	function stopTimer( $name = 'XOOPS', $clear = false ) {
	 	$dur = ( $end = $this->microtime() ) - $this->timers[$name]['start'];
		if ( $clear ) {
			unset($this->timers[$name]);
		} else {
			$this->timers[$name]['stop'] = $end;
		}
	 	return $dur;
	}

	/**
	 * Inserts the events log into the page output, and send it to the client
	 * @param string $output The content generated during the current request
	 */
	function renderEvents( $output ) {
		$log = "events log";
		$pattern = '<!--{xo-logger-output}-->';
		$pos = strpos( $output, $pattern );
		if ( $pos !== false ) {
			echo substr( $output, 0, $pos ) . $log . substr( $output, $pos + strlen( $pattern ) );
		} else {
			echo $output . $log;
		}
	}	

}


?>