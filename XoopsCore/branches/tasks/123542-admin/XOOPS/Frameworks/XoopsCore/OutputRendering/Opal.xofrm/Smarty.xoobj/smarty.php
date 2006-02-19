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
 * @package		xoops_opal
 * @subpackage	xoops_opal_Smarty
 * @version		$Id$
 * @author		Skalpa Keo <skalpa@xoops.org>
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

XOS::import( 'Smarty' );

/**
 * Opal framework Smarty implementation
 * @since       2.3.0
 * @package		xoops_opal
 * @subpackage	xoops_opal_Smarty
 */
class xoops_opal_Smarty extends Smarty {
	
	var $left_delimiter =	'([';
	var $right_delimiter =	'])';

	var $template_dir		= XOOPS_THEME_PATH;
	var $compile_dir		= 'var/Application Support/xoops_template_Smarty';
	var $cache_dir			= 'var/Caches/xoops_opal_Smarty';

	var $use_sub_dirs		= false;
	

	var $compiler_class		= 'xoops_opal_SmartyCompiler';
	var $compiler_file		= '/compiler.php';
	
	var $currentFile		= '';

	/**
	 * Theme to apply to this template (if any)
	 * 
	 * @var object
	 */
	var $currentTheme		= false;
	

	function xoops_opal_Smarty( $options = null ) {
		global $xoops;
		
		$this->compile_dir	= $xoops->path( $this->compile_dir );
		$this->cache_dir	= $xoops->path( $this->cache_dir );

		$this->compiler_file = str_replace( '\\', '/', dirname( __FILE__ ) ) . $this->compiler_file;
		
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
	 * Renders output from template data
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
    	$file = ( substr( $compiler->_current_file, 0, 6 ) == 'xotpl:' ) ? $compiler->template_engine->realTemplatePath : $compiler->_current_file;
		if ( substr( $file, -6 ) != '.xotpl' ) {
			$data = str_replace( array( '<{', '}>' ), array( '([', '])' ), $data );
		}
    	return $data;
    }

	/**
	 * Temporary fix for a Smarty bug (Smarty doesn't work with recursive arrays)
	 */    
    function _run_mod_handler()
    {
        $_args = func_get_args();
        list($_modifier_name, $_map_array) = array_splice($_args, 0, 2);
        list($_func_name, $_tpl_file, $_tpl_line) =
            $this->_plugins['modifier'][$_modifier_name];

        $_var = $_args[0];
        foreach ($_var as $_key => $_val) {
            $_args[0] = $_val;
			// skalpa: fix to handle recursive arrays
			//$_var[$_key] = call_user_func_array($_func_name, $_args);
            if ( is_array( $_val ) ) {
            	$_cargs = func_get_args();
            	$_cargs[2] = $_val;
            	$_var[$_key] = call_user_func_array( array( &$this, '_run_mod_handler' ), $_cargs );
            } else {
 	           $_var[$_key] = call_user_func_array($_func_name, $_args);
            }
        }
        return $_var;
    }
    
    
    
    
    
    
    
    
}

?>