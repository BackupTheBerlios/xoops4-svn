<?php
/**
 * xoops_template_Smarty component main file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright   The Xoops project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since       2.3.0
 * @package		xoops_template
 * @subpackage	xoops_template_Smarty
 * @version		$Id$
 * @author		Skalpa Keo <skalpa@xoops.org>
 */

XOS::import( 'Smarty' );

/**
 * XOOPS core default Smarty implementation
 * @since        2.3.0
 * @package		xoops_template
 * @subpackage	xoops_template_Smarty
 */
class xoops_template_Smarty extends Smarty {
	
	var $left_delimiter =	'([';
	var $right_delimiter =	'])';

	var $template_dir		= XOOPS_THEME_PATH;
	var $compile_dir		= 'var/Application Support/xoops_template_Smarty';
	var $cache_dir			= 'var/Caches/xoops_template_Smarty';

	var $use_sub_dirs		= false;
	
	
	var $currentFile		= '';

	/**
	 * Theme to apply to this template (if any)
	 * 
	 * @var object
	 */
	var $currentTheme		= false;
	

	function xoops_template_Smarty( $options = null ) {
		global $xoops;
		
		$this->compile_dir	= $xoops->path( $this->compile_dir );
		$this->cache_dir	= $xoops->path( $this->cache_dir );

		if ( $xoops->xoRunMode & XO_MODE_DEV_MASK ) {
			$this->compile_check = true;
			$this->force_compile = true;
			//$this->debugging = true;
		}
		$this->Smarty();

		$path = str_replace( DIRECTORY_SEPARATOR, '/', dirname( __FILE__ ) );

		array_unshift( $this->plugins_dir, "$path/plugins" );
		array_unshift( $this->plugins_dir, "$path/smarty/plugins" );
		
		$this->register_prefilter( array( &$this, 'filterOldTemplates' ) );
		
		$this->assign_by_ref( 'xoops', $xoops );
	}

	
    function display( $resource_name = null, $cache_id = null, $compile_id = null) {
        return $this->fetch( $resource_name, $cache_id, $compile_id, true );
    }

    function fetch( $resource_name = null, $cache_id = null, $compile_id = null, $display = false) {
    	if ( !isset( $resource_name ) ) {
    		$resource_name = $this->currentFile;
    	} else {
    		$this->currentFile = $resource_name;
    	}
    	return parent::fetch( $resource_name, $cache_id, $compile_id, $display );
    }

	/**
	 * Render output from template data
	 * 
	 * @param   string  $data		The template to render
	 * @param	bool	$display	If rendered text should be output or returned
	 * @return  string  Rendered output if $display was false
	 **/
    function fetchFromData( $tplSource, $display = false ) {
        if ( !function_exists('smarty_function_eval') ) {
            require_once SMARTY_DIR . '/plugins/function.eval.php';
        }
        return smarty_function_eval( array('var' => $tplSource), $this );
    }

	/**
	 * Compiler prefilter to change 2.0 style delimiters to new ones
	 */
    function filterOldTemplates( $data, &$compiler ) {
    	$file = ( substr( $compiler->_current_file, 0, 6 ) == 'xotpl:' ) ? $this->realTemplatePath : $compiler->_current_file;
		if ( substr( $file, -5 ) == '.html' ) {
			$data = str_replace( array( '<{', '}>' ), array( '([', '])' ), $data );
		}
    	return $data;
    }

}

?>