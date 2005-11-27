<?php
/**
* xoops_pyro_Theme component class file
*
* @copyright	The Xoops project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @package      xoops_pyro
* @subpackage   xoops_pyro_Theme
* @author       Skalpa Keo <skalpa@xoops.org>
* @since        2.3.0
* @version		$Id$
*/

/**
* xoops_pyro_ThemeFactory
*
* @author 		Skalpa Keo
* @package		xoops_pyro
* @subpackage	xoops_pyro_ThemeFactory
* @since        2.3.0
*/
class xoops_pyro_ThemeFactory {
	/**
	 * Currently enabled themes (if empty, all the themes in themes/ are allowed)
	 * 
	 * @var array
	 */
	var $allowedThemes = array();
	/**
	 * Default theme to instanciate if none specified
	 * 
	 * @var string
	 */
	var $defaultTheme = 'default-web';

	/**
	 * Instanciate the specified theme
	 */
	function &createInstance( $options = array(), $initArgs = array() ) {
		if ( @empty( $options['folderName'] ) ) {
			$options['folderName'] = $this->defaultTheme;
		} elseif ( !empty( $this->allowedThemes ) && !in_array( $options['folderName'], $this->allowedThemes ) ) {
			$options['folderName'] = $this->defaultTheme;
		}
		$inst =& XOS::createInstanceOf( "xoops_pyro_Theme", $options, $initArgs );
		return $inst;
	}

	/**
	 * List the available themes
	 *
	 * @param	boolean $allowed Whether to return the allowed themes, or all of them
	 *  @return	array
	 */
	function enumerate( $allowed = true ) {
		global $xoops;
		$themes = array();
		if ( $dh = opendir( $xoops2->path('/themes/') ) ) {
			while ( $file = readdir($dh) ) {
				if ( $file{0} != '.' && $file != 'CVS' ) {
					$themes[] = $file;
				}
			}
			closedir( $dh );
		}
		if ( !empty($this->allowedThemes) && $allowed ) {
			return array_intersect( $themes, $this->allowedThemes );
		}
		return $themes;
	}

}

class xoops_pyro_Theme {
   /**
    * The name of this theme
    * @var string
    */
	var $folderName = '';
   /**
    * Physical path of this theme folder
    * @var string
    */
	var $path = '';
	var $url = '';
   /**
   * Whether or not the theme engine should include output generated by php
   * @var string
   */
	var $bufferOutput = true;
	/**
	* Default content-type of pages generated by this theme
	* @var string
	*/
	var $contentType = 'text/html';
	/**
	* Page-level template to use (relative to templates/page/)
	* @var string
	*/
	var $canvasTemplate = '';
	/**
	* Page-level template to use (relative to templates/page/)
	* @var string
	*/
	var $pageTemplate = '';
	/**
	* The file to fetch from the theme
	* @var string
	*/
	var $contentTemplate = '';
	/**
	* Text content to display right after the contentTemplate output
	* @var string
	*/
	var $content = '';
	/**
	* The API version supported by this theme (used to achieve BC)
	* @var string
	*/
	var $themeAPI = '2.3';
	/**
	* Name of this theme parent (if any)
	* @var string
	*/
	var $parentTheme = '';
	/**
	* Array containing all this theme ancestors (parent,grand-parent,etc...)
	* @var array
	* @access protected
	*/
	var $parentInfos = array();
	
	var $renderCount = 0;
	/**
	 * Pointer to the theme template engine
	 *
	 * @var object
	 */
	var $template = false;
   /**#@-*/

	function xoInit( $options = array() ) {
		global $xoops;

		$this->path	= $xoops->path( '/themes/' . $this->folderName );

		$this->template =& XOS::create( 'xoops_template_Smarty' );
		$this->template->currentTheme =& $this;
		$this->template->compile_id = $this->folderName;

		if ( $info = @include "$this->path/xo-info.php" ) {
			XOS::apply( $this, $info );
		} else {
			$this->themeAPI = '2.0';
		}
		if ( $this->bufferOutput ) {
			ob_start();
		}
		return true;
	}

	/**
	 * Render the page
	 *
	 * The theme engine builds pages from 3 templates: canvas, page and content.
	 * 
	 * The canvas template is the outermost one. It is the one containing the html container
	 * elements (html,head,body), the header, footer and the left and right columns.
	 * 
	 * Standard themes should be delivered with the following canvas templates:
	 * - canvas-default.xotpl: The "normal" template, used by most pages on a site
	 * - canvas-dialog.xotpl: A lightweight canvas, without left and right columns, used by popups
	 * - canvas-email.xotpl: The canvas used by e-mails sent by the site
	 * 
	 * The page template is the container for center blocks and the content. Themes don't have to
	 * include several page templates, but applications may have their own page template that is
	 * used instead of the default one (i.e: the XOOPS Management module).
	 * 
	 * A module can call this method directly and specify what templates the theme engine must use.
	 * If render() hasn't been called before, the theme defaults will be used for the canvas and
	 * page template (and xoopsOption['template_main'] for the content).
	 * 
	 * @param string $canvasTpl		The canvas template, if different from the theme default
	 * @param string $pageTpl		The page template, if different from the theme default
	 * @param string $contentTpl	The content template
	 * @param array	 $vars			Template variables to send to the template engine
	 */
	function render( $canvasTpl = null, $pageTpl = null, $contentTpl = null, $vars = array() ) {
		global $xoops;

		if ( !$this->renderCount && $this->bufferOutput ) {
			$this->content .= ob_get_contents();
			ob_end_clean();
		}
		if ( $this->themeAPI != '2.3' ) {
			include $xoops->path( $this->xoBundleRoot . '/render-' . $this->themeAPI . '.php' );
		}
		if ( !empty($canvasTpl) ) {
			$this->canvasTemplate = $canvasTpl;
		}
		if ( !empty($pageTpl) ) {
			$this->pageTemplate = $pageTpl;
		}
		if ( !empty($contentTpl) ) {
			$this->contentTemplate = $contentTpl;
		}
		$vars['xoops']		=& $xoops;
		$vars['xoTheme']	=& $this;
		$this->template->assign( $vars );
		$this->renderZone( 'canvas' );
		$this->renderCount++;
		
	}
	
	/**
	 * Render the specified page part
	 * 
	 * @param string $zone
	 */	
	function renderZone( $zone ) {
		$zones = array( 'canvas' => 0, 'page' => 1, 'content' => 2 );
		if ( isset( $zones[$zone] ) ) {
			$tpl = $zone . 'Template';
			$tpl = $this->$tpl;
			if ( !empty( $tpl ) ) {
				if ( $tpl{0} == '.' ) {
					$tpl = $this->path . substr( $tpl, 1 );
				}
				$this->template->display( $tpl );
				if ( !empty($this->content) && $zone == 'content' ) {
					echo $this->content;
				}
			} elseif ( $zone != 'content' ) {
				$this->renderZone( $zones[$zone] + 1 );
			}
		}	
	}
	function renderAttributes( $coll ) {
		$str = '';
		foreach ( $coll as $name => $val ) {
			if ( $name != '_' ) {
				$str .= ' ' . $name . '="' . htmlspecialchars( $val, ENT_QUOTES ) . '"';
			}
		}
		return $str;
	}
	
	
	function resourcePath( $path ) {
		global $xoops;
		$isWeb = true;
		if ( $path{0} == '/' ) {
			$path = substr( $path, 1 );
			$isWeb = false;
		}
		if ( file_exists( "$this->path/$path" ) ) {
			return "themes/$this->folderName/$path";
		}
		return $isWeb ? "www/$path" : $path;
	}
	
    /**
    * Add Javascript file or JS code to the document head
    *
    * @param string $src path to .js file
    * @param array $attributes name => value paired array of attributes such as title
    * @param string $content JavaScript code
    *
    * @return void
    **/
    function addScript( $src = '', $attributes = array(), $content = '' ) {
    	global $xoops;
		if ( !empty( $src ) ) {
			$attributes['src'] = $xoops->url( $this->resourcePath( $src ) );
		}
		if ( !empty( $content ) ) {
			$attributes['_'] = $content;
		}
		if ( !isset( $attributes['type'] ) ) {
			$attributes['type'] = 'text/javascript';
		}
		$this->setMeta( 'script', $src, $attributes );
    }

    /**
    * Add StyleSheet or CSS code to the document head
    *
    * @param string $src path to .css file
    * @param array $attributes name => value paired array of attributes such as title
    * @param string $content CSS code
    *
    * @return void
    **/
    function addStylesheet( $src = '', $attributes = array(), $content = '' ) {
		if ( !empty( $src ) ) {
			$attributes['href'] = $xoops->url( $this->resourcePath( $src ) );
		}
		if ( !empty( $content ) ) {
			$attributes['_'] = $content;
		}
    	$this->setMeta( 'stylesheet', $src, $attributes );
    }
	/**
	 * Add a <link> to the header
	 * 
	 * @param string	$rel		Relationship from the current doc to the anchored one
	 * @param string	$href		URI of the anchored document
	 * @param array		$attributes	Additional attributes to add to the <link> element
	 */
	function addLink( $rel, $href = '', $attributes = array() ) {
		global $xoops;
		if ( !empty( $href ) ) {
			$attributes['href'] = $href;
		}
		$this->setMeta( 'link', $rel, $attributes );
	}
    
	/**
	 * Change output page meta-information
	 */
    function setMeta( $type = 'meta', $name = '', $value = '' ) {
		if ( !isset( $this->metas[$type] ) ) {
			$this->metas[$type] = array();
		}
    	if ( isset($name) ) {
			$this->metas[$type][$name] = $value;
		} else {
			$this->metas[$type][] = 	$value;
		}
		return $value;
    }
	

	
	

}


?>