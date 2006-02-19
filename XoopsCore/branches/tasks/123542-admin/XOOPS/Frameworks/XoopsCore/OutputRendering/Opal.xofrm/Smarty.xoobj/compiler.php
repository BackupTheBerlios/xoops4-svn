<?php
/**
 * xoops_opal_SmartyCompiler main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		2.3.0
 * @package		xoops_opal
 * @subpackage	xoops_opal_Smarty
 * @version		$Id$
 */

/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_PATH' ) ) exit();

require_once SMARTY_DIR . '/Smarty_Compiler.class.php';

/**
 * Opal Smarty compiler
 *
 * This compiler is used by the xoops_opal framework smarty implementation instead of the default one.
 * If the template is recognized as an XML XoopsTemplate file, it will be parsed before compilation,
 * the template data extracted from the correct TemplateData tag.
 * @package		xoops_opal
 * @subpackage	xoops_opal_Smarty
 */
class xoops_opal_SmartyCompiler extends Smarty_Compiler {
	/**
	 * Name of the currently compiled template
	 * @var string
	 * @access protected
	 */
	var $resourceName = '';	
	/**
	 * Language files included before the current template is rendered
	 * @var array
	 */
	var $tplLangFiles = array();
	/**
	 * Widgets to instanciate when the current template is rendered
	 * @var array
	 */
	var $tplWidgets = array();

    function _compile_file($resource_name, $source_content, &$compiled_content) {
		$this->resourceName = $resource_name;
    	if ( substr( $resource_name, -6 ) == '.xotpl' ) {
			$this->tplLangFiles = $this->tplWidgets = array();
			if ( strpos( substr( $source_content, 0, 256 ), '<XoopsTemplate' ) !== false ) {
				
				$parser = xml_parser_create();
				xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
				xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
				xml_parse_into_struct($parser, $source_content, $values, $tags );
				xml_parser_free($parser);

				if ( isset( $tags['Object'] ) ) {
					$objects =& $tags['Object'];
					for ( $n = 0; $n != count($objects); $n+=2 ) {
						$start = $objects[$n] + 1;
						$this->parseObject( $values[ $objects[$n] ], array_slice( $values, $start, $objects[$n+1] - $start ) );
					}
				}
				foreach ( $tags['TemplateData'] as $tag ) {
					$tagInfo =& $values[ $tag ];
					if ( $tagInfo['attributes']['contentType'] == $this->template_engine->currentTheme->contentType ) {
						$source_content = $tagInfo['value'];
						break;
					} elseif ( isset( $tagInfo['attributes']['default'] ) ) {
						$source_content = $tagInfo['value'];
					}
				}
			}
			$mods = $this->default_modifiers;
			$this->default_modifiers = array( '@htmlspecialchars:' . ENT_QUOTES );
			parent::_compile_file( $resource_name, $source_content, $compiled_content );
			$this->default_modifiers = $mods;

			$prelude = '';
			if ( !empty($this->tplWidgets) ) {
				$prelude = "<?php\n";
				foreach ( $this->tplWidgets as $widgetDef ) {
					$prelude .= $this->insertWidget( $widgetDef );
				}
				$prelude .= "\n?>";
			}
			$compiled_content = $prelude . $compiled_content;
			return true;
		}
    	return parent::_compile_file( $resource_name, $source_content, $compiled_content );
    }
    
	/**
	 * Extract widget information from an XML template <object> tag
	 * @access private
	 */
    function parseObject( $objectTag, $children ) {
    	$object = array();
    	if ( !isset( $objectTag['attributes']['bundleId'] ) ) {
    		trigger_error( "Error parsing $this->resourceName: invalid template object definition (missing bundleId)", E_USER_WARNING );
    		return;
    	}
    	$object['bundleId'] = "'" . $objectTag['attributes']['bundleId'] . "'";
    	if ( isset( $objectTag['attributes']['tplVar'] ) ) {
    		$object['assign'] = "'" . $objectTag['attributes']['tplVar'] . "'";
    	}
    	foreach ( $children as $k => $prop ) {
    		$propName = $prop['attributes']['name'];
    		if ( $prop['tag'] == 'Property' ) {
    			if ( $prop['type'] == 'complete' ) {
    				$object[ $propName ] = $this->parsePropertyValue( $prop['value'] );
    			} elseif ( $prop['type'] == 'open' ) {
    				$arrayStart = $k;
    				$arrayName = $propName;
    			} elseif ( $prop['type'] == 'close' ) {
    				$object[ $arrayName ]  = $this->parseArrayProperty( array_slice( $children, $arrayStart+1, $k - $arrayStart - 1 ) );
    			}
    		}
    	}
    	$this->tplWidgets[] = $object;
    }
	/**
	 * Parses an array property content
	 * @access private
	 */
    function parseArrayProperty( $tags ) {
    	$items = array();
    	$n = 0;
    	foreach ( $tags as $k => $tag ) {
    		if ( isset( $tag['attributes']['key'] ) ) {
    			$items[] = "'{$tag['attributes']['key']}' => " . $this->parsePropertyValue( $tag['value'] );
    		} else {
    			$items[] = "$k => " . $this->parsePropertyValue(  $tag['value'] );
    		}
    	}
    	return 'array( ' . implode( ',', $items ) . ')';
    }
	/**
	 * Parses an object property value
	 * @access private
	 */
	function parsePropertyValue( $val ) {
		if ( substr( $val, 0, 1 ) != '$' ) {
			return $this->_expand_quoted_text( "'" . addslashes($val) . "'" );
		}
		return $this->_parse_var_props( $val );
	}
    
    /**
     * Generates widget insertion code
     *
     * This method generates the widget instanciation code to be inserted in the compiled template.
     * It is called for each <object> tag encountered during compilation of XML templates, or by the xoWidget
     * compiler plug-in.
     * 
     * @param array $args
     * @return string PHP code to be inserted in the compiled template
     */
    function insertWidget( $args ) {
    	global $xoops;
    	if ( isset( $args['assign'] ) ) {
			$targetVar = $args['assign'];
			unset( $args['assign'] );
		} else {
			$targetVar = '';
		}
		$bundleId = $args['bundleId'];
		unset( $args['bundleId'] );
		$realBundleId = substr( $bundleId, 1, strlen( $bundleId ) - 2 );
	
		$code = "\n";
		// Get this widget stylesheet and javascript properties
		if ( $css = XOS::classVar( $realBundleId, 'stylesheet' ) ) {
			$css = $realBundleId . '#' . $css;
			$attrs = array(
				'type' => 'text/css',
				'href' => $xoops->url( $this->template_engine->currentTheme->resourcePath( $css ) ),
			);
			$code .= '$this->currentTheme->setMeta( "stylesheet", "' . $css . '", ' . var_export( $attrs, true ) . ");\n";
		}
		if ( $js = XOS::classVar( $realBundleId, 'javascript' ) ) {
			$js = $realBundleId . '#' . $js;
			$attrs = array(
				'type' => 'text/javascript',
				'src' => $xoops->url( $this->template_engine->currentTheme->resourcePath( $js ) ),
			);
			$code .= '$this->currentTheme->setMeta( "script", "' . $js . '", ' . var_export( $attrs, true ) . ");\n";
		}
		$create = 'XOS::create( ' . $bundleId;
		if ( !empty( $args ) ) {
			$create .= ", array(\n";
			foreach ( $args as $prop => $value ) {
				$create .= "\t'$prop' => " . $value . ",\n";
			}
			$create .= ')';
		}
		$create .= " )";
	
		if ( !$targetVar ) {
			$code .= "\$widget = $create;\n";
			$code .= "echo ( \$widget ? \$widget->render() : \"Failed to instanciate $bundleId widget.\" );\n";
		} else {
			$code .= "\$this->_tpl_vars[$targetVar] = $create;\n";
		}
		return $code;
    }
    
    // Fixes for some an original smarty misbehavior, that applies the default modifiers
    // on the foreach and if variables (making arrays content escaped twice)
	function _compile_foreach_start( $tag_args ) {
   		$mods = $this->default_modifiers;
   		$this->default_modifiers = array();
   		$out = parent::_compile_foreach_start( $tag_args );
       	$this->default_modifiers = $mods;
		return $out;
	}
    function _compile_if_tag($tag_args, $elseif = false) {
   		$mods = $this->default_modifiers;
   		$this->default_modifiers = array();
   		$out = parent::_compile_if_tag( $tag_args, $elseif );
       	$this->default_modifiers = $mods;
		return $out;
	}

	function _parse_attrs( $tag_args, $use_mods = true ) {
		if ( $use_mods ) {
			return parent::_parse_attrs( $tag_args );
		}
   		$mods = $this->default_modifiers;
   		$this->default_modifiers = array();
   		$out = parent::_parse_attrs( $tag_args );
       	$this->default_modifiers = $mods;
		return $out;
	}
    function _parse_parenth_args( $parenth_args ) {
   		$mods = $this->default_modifiers;
   		$this->default_modifiers = array();
   		$out = parent::_parse_parenth_args( $parenth_args );
       	$this->default_modifiers = $mods;
		return $out;
    }
	
	
	
	
}

?>