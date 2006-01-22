<?php
// $Id$
/**
* xoops.theme service definition
*
* @copyright	The Xoops project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @package      xoops
* @subpackage   lang
* @author       Skalpa Keo <skalpa@xoops.org>
* @since        2.1.0
* @internal		Almost a straight copy from the Xoops 2.0.x include/common.php code
* ---------------------------------------------------------------------------------
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, see http://www.fsf.org/copyleft/gpl.html
* ---------------------------------------------------------------------------------
*/

require_once XOOPS_ROOT_PATH.'/class/template.php';

class XTheme {

    /*#@+
    * xoops.theme properties
    **/
    /**
   * The name of this theme
   * @var string
   **/
    var $name = 'default';
    var $fullName = '';

    var $path = '';
    var $url = '';
    /**
   * Whether or not the theme engine should include output generated by php
   * @var string
   **/
    var $bufferOutput = true;
    /**
   * Default content-type of pages generated by this theme
   * @var string
   **/
    var $contentType = 'text/html';
    /**
   * The templates this theme contains in an array of relative paths
   * @var array
   **/
    var $templateFiles = array();

    /**
   * Default file for this theme (in case the specified one can't be found)
   * @var string
   **/
    var $defaultFile = 'theme.html';
    /**
   * The file to fetch from the theme
   * @var string
   **/
    var $pageTpl = '';
    /**
   * The type of page to show (main, admin or rss)
   * @var string
   **/
    var $pageType = '';
    /**
	* Enable banner system ?
	* @var bool
	*/
    var $enableBanner = false;
    /**
	* Template engine used to render pages
	* @var object
	*/
    var $tplEngine = false;

    /**#@-*/

    /*#@+
    * xoops.theme.page properties
    **/
    /**
	* Array of strings defining the page title (we use an array here so modules can add a string to the main title)
    * @var array
    **/
    var $title = array();
    /**
    * Separator used to rebuild the title
    * @var string
    **/
    var $titleSeparator = ' - ';
    /**
    * Page banner
    * @var object
    **/
    var $banner = null;

    /**
    * Page footer
    * @var string
    **/
    var $footer = '';

    /**
    * see addJS() and getJS() methods
    **/
    var $js = array();

    /**
    * Constructor
    *
    * @param bool $enableBanner whether to enable banners on this page
    * @param bool $debugging whether debugging is enabled
    * @param bool $mainService whether this is the main theme service (Not used yet, Mith)
    * @param string $name name of the theme to use
    *
    * @return void
    */
    function XTheme($enableBanner = true, $debugging = false, $mainService = false, $name = "") {
        $this->path = XOOPS_THEME_PATH;
        $this->url = XOOPS_THEME_URL;
        /*switch ($this->contentType) {
        case 'text/html':
        $this->document =& $GLOBALS[XOOPS]->create( 'xoops.output.document.xhtml' );
        break;
        default:
        $this->document =& $GLOBALS[XOOPS]->create( 'xoops.output.document' );
        }*/
        $this->enableBanner = $enableBanner;
        if ($name != "") {
            $this->name = $name;
        }

        $this->tplEngine = new XoopsTpl();
        $this->tplEngine->xoops_setDebugging($debugging);
        $this->tplEngine->template_dir = XOOPS_THEME_PATH;
        $this->tplEngine->assign( 'xoops_banner', $this->enableBanner ? xoops_getbanner() : '&#160;' );

        $GLOBALS['xoopsTpl'] =& $this->tplEngine;
        if ( $this->bufferOutput ) {
            global $xoopsConfig;

            //if Gzip is enabled and debug is turned off
            // DISABLED for XOOPS 2.2.3 until we figure out, why it won't work in some configurations - Mith.
            // @TODO: Find out why gzip_compression gives blank pages
//            if ( $xoopsConfig['gzip_compression'] == 1 && ($xoopsConfig['debug_mode'] == array(0 => 0) || $xoopsConfig['debug_mode'] == array()))
//            {
//                $ob_started = false;
//                $phpver = phpversion();
//                $useragent = ( isset( $_SERVER["HTTP_USER_AGENT"] ) ) ? $_SERVER["HTTP_USER_AGENT"] : "";
//
//                if ( $phpver >= '4.0.4pl1' && ( strstr( $useragent, 'compatible' ) || strstr( $useragent, 'Gecko' ) ) )
//                {
//                    if ( extension_loaded( 'zlib' ) ) {
//                        ob_start( 'ob_gzhandler' );
//                        $ob_started = true;
//                    }
//                }
//                else if ( $phpver > '4.0' )
//                {
//                    if ( strstr( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) )
//                    {
//                        if ( extension_loaded( 'zlib' ) )
//                        {
//                            ob_start();
//                            ob_implicit_flush( 0 );
//                            header( 'Content-Encoding: gzip' );
//                            $ob_started = true;
//                        }
//                    }
//                }
//                if (!$ob_started) {
//                    ob_start();
//                }
//            }
//            else
//            {
//                ob_start();
//            }
            ob_start();
        }

        $this->loadGlobalVars();
        $this->loadTheme();
        return true;
    }

    /**
    * Builds title string for page title
    *
    * @return string
    */
    function titleString() {
        return implode( $this->titleSeparator, $this->title );
    }


    /**
    * Determines path for a given theme template
    *
    * checks first theme path for $tplpath
    * then default theme path for $tplpath
    * then returns the default theme's default file
    *
    * @param string $tplpath name of template
    */
    function templatePath( $tplpath ) {
        //		if ( isset($this->templateFiles[$tplpath]) ) {
        //			return $this->templateFiles[$tplpath];
        //		}
        $defpath = $this->path.'/default/' . $this->defaultFile;
        $deffilepath = $this->path.'/default/' . $tplpath;
        $thmpath = $this->path.'/'.$this->name.'/'.$tplpath;
        if ( is_readable( $thmpath ) ) {
            return $thmpath;
        }
        if ( is_readable( $deffilepath ) ) {
            return $defpath;
        }
        if ( is_readable( $defpath ) ) {
            return $defpath;
        }
        trigger_error( "Cannot determine path for template $tplpath", E_USER_WARNING );
        return false;
    }

    /**
    * Display a template
    *
    * @param string $contentTemplate Name of template to display
    * @param string $pageTpl Name of template to use for whole page
    * @param array $customVars additional variables to assign before display
    *
    * @return bool
    */
    function display( $contentTemplate = null, $pageTpl = null, $customVars = null ) {
        static $called = false;

        if ( $this->bufferOutput ) {
            if ( $called ) {
                return true;
            }
            $content = ob_get_contents();
            ob_end_clean();
        } else {
            $content = '';
        }
        $called = true;

        //$this->tplEngine->caching = 0;				// ??? @todo: Kept from Xoops 2.0.x, but to be kicked out

        global $xoopsConfig, $xoopsOption;
        $this->name = $xoopsConfig['theme_set'];

        //load CSS and theme language prior to assigning content
        $this->pageType = isset($xoopsOption['pagetype']) && in_array($xoopsOption['pagetype'], array("admin", "rss")) ? $xoopsOption['pagetype'] : "main";

        $this->tplEngine->assign(array('xoops_theme' => $this->name,
        'xoops_imageurl' => $this->url.'/'.$this->name.'/',
        'xoops_themecss'=> xoops_getcss($this->name, $this->pageType)));
        //load theme language
        $filename = $this->pageType == "admin" ? "admin.php" : "main.php";
        if (file_exists(XOOPS_THEME_PATH."/$this->name/language/".$GLOBALS['xoopsConfig']['language']."/".$filename)) {
            include_once(XOOPS_THEME_PATH."/$this->name/language/".$GLOBALS['xoopsConfig']['language']."/".$filename);
        }
        elseif (file_exists(XOOPS_THEME_PATH."/$this->name/language/english/".$filename)) {
            include_once(XOOPS_THEME_PATH."/$this->name/language/english/".$filename);
        }

        if ( isset( $customVars ) ) {
            $this->tplEngine->assign( $customVars );
        }

        //Add module CSS code
        $moduleCSS = xoops_getcss($this->name, "module");
        if (count($moduleCSS)>0) {
	        foreach($moduleCSS as $modulescss){
            	$this->addCSS($modulescss);
        	}
        }

        //assign JavaScript
        $this->tplEngine->assign('xoops_js', '//--></script>'.implode('', $this->getJS()).'<script type="text/javascript"><!--');

        // Load module-specified variables for cached module pages
        // Keep the two variables before they are deprecated in modules
        global $xoopsOption;
        if(isset($xoopsOption["xoops_pagetitle"])){
        	$this->tplEngine->assign('xoops_pagetitle', $xoopsOption["xoops_pagetitle"]);
    	}
        if(isset($xoopsOption["xoops_module_header"])){
        	$this->tplEngine->assign('xoops_module_header', $xoopsOption["xoops_module_header"]);
    	}

        //Assign main content
        if ( !empty( $contentTemplate ) ) {
            if ( strpos( $contentTemplate, ':' ) === false ) {
                $contentTemplate = $this->templatePath($contentTemplate);
            }
            $content .= $this->tplEngine->fetch( $contentTemplate, $this->getCachedTemplateId() );
        }

        $this->tplEngine->assign( 'xoops_contents',  $content );

        if ((isset($xoopsOption['output_type']) && $xoopsOption['output_type'] == "plain")) {
            //display "plain" template that has theme CSS, but no specific content
            $this->pageTpl = 'system_plain.html';
            $tpl = "db:".$this->pageTpl;
        }
        else {
            if ((isset($xoopsOption['pagetype']) && $xoopsOption['pagetype'] == "admin")) {
                //display theme admin template
                $this->pageTpl = 'themeadmin.html';
            }

            //Find page template to show
            if (is_null($pageTpl)) {
                if ( empty($this->pageTpl) || (false === ( $tpl = $this->templatePath($this->pageTpl) ) ) ) {
                    $tpl = $this->templatePath( $this->defaultFile );
                }
            }
            elseif ((false === ( $tpl = $this->templatePath($pageTpl) ) )) {
                $tpl = $this->templatePath( $this->defaultFile );
            }
        }
        $this->tplEngine->caching = 0;				// ??? @todo: Kept from Xoops 2.0.x, but to be kicked out
        $this->tplEngine->display( $tpl );
        return true;
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
    function addJS($src, $attributes = array(), $content = "") {
        $js = "<script type=\"text/javascript\"";
        if (!is_null($src)) {
            $attributes['src'] = $src;
        }
        if (is_array($attributes) && count($attributes) > 0) {
            foreach ($attributes as $name => $value) {
                $js .= " ".$name ."=\"".$value."\"";
            }
        }
        $js .= ">";
        if (is_null($src)) {
            $js .= $content;
        }
        $js .= "</script>\n";
        $this->js[] = $js;
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
    function addCSS($src, $attributes = array(), $content = "") {
        if (!is_null($src)) {
            $css = "<link rel=\"stylesheet\" type=\"text/css\"";
            $attributes['href'] = $src;
        }
        else {
            $css = "<style type=\"text/css\"";
        }
        if (is_array($attributes) && count($attributes) > 0) {
            foreach ($attributes as $name => $value) {
                $css .= " ".$name."=\"".$value."\"";
            }
        }
        if (is_null($src)) {
            $css .= ">";
            $css .= $content;
            $css .= "</style>\n";
        }
        else {
            $css .= " />\n";
        }
        $this->js[] = $css;
    }

    /**
    * Get Javascript code for document head
    *
    * @return array
    **/
    function getJS() {
        return $this->js;
    }

    /**
	* Load variables used globally in themes
	*
	* @param bool $loadConfig whether to load configuration data from db (called with false from xoops_cp_header() )
	*
	* @return void
	*/
    function loadGlobalVars($loadConfig = true) {
        global $xoopsConfig, $xoopsModule;
        $this->tplEngine->assign(array('xoops_url' => XOOPS_URL,
        'xoops_rootpath' => XOOPS_ROOT_PATH,
        'xoops_langcode' => _LANGCODE,
        'xoops_charset' => _CHARSET,
        'xoops_version' => XOOPS_VERSION,
        'xoops_upload_url' => XOOPS_UPLOAD_URL));
        $this->tplEngine->assign(
        array('xoops_requesturi' => htmlspecialchars($GLOBALS['xoopsRequestUri'], ENT_QUOTES),
        'xoops_sitename' => htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
        'xoops_slogan' => htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES)));
        if ($loadConfig == true) {
            // Meta tags
            $config_handler =& xoops_gethandler('config');
            $config =& $config_handler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
            foreach ($config as $name => $value) {
                // prefix each tag with 'xoops_'
                $this->tplEngine->assign('xoops_'.$name, $value);
            }
            $this->addJS(XOOPS_URL.'/include/xoops.js');

            // Load module information
            if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') != "system") {
                // set page title
                $this->tplEngine->assign('xoops_pagetitle', $xoopsModule->getVar('name'));
                $this->tplEngine->assign('xoops_dirname', $xoopsModule->getVar('dirname'));
            }
            else {
                $this->tplEngine->assign('xoops_pagetitle', htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES));
                $this->tplEngine->assign('xoops_dirname', "system");
            }
        }
        global $xoopsUser, $xoopsModule;
        // Load user variables
        if ($xoopsUser != '') {
            $this->tplEngine->assign(array(   'xoops_isuser' => true,
            'xoops_userid' => $xoopsUser->getVar('uid'),
            'xoops_uname' => $xoopsUser->getVar('uname')));
            if (is_object($xoopsModule)) {
                $this->tplEngine->assign('xoops_isadmin', $xoopsUser->isAdmin($xoopsModule->getVar('mid')));
            }
        } else {
            $this->tplEngine->assign(array('xoops_isuser' => false, 'xoops_isadmin' => false));
        }
    }

    /**
    * Load the theme if valid
    *
    * @return void
    **/
    function loadTheme() {
        global $xoopsConfig, $xoopsUser;
        // $xoopsConfig['theme_set'] is set through GPC
        if (!empty($_REQUEST['xoops_theme_select']) && in_array($_REQUEST['xoops_theme_select'], $xoopsConfig['theme_set_allowed'])) {
            $xoopsConfig['theme_set'] = $_REQUEST['xoops_theme_select'];
            $_SESSION['xoopsUserTheme'] = $_REQUEST['xoops_theme_select'];
        // taking old value in the same session
        } elseif (!empty($_SESSION['xoopsUserTheme']) && in_array($_SESSION['xoopsUserTheme'], $xoopsConfig['theme_set_allowed'])) {
            $xoopsConfig['theme_set'] = $_SESSION['xoopsUserTheme'];
        }
        // taking user preference
        elseif ($xoopsUser) {
            if ($xoopsUser->getVar('theme') != "" && in_array($xoopsUser->getVar('theme'), $xoopsConfig['theme_set_allowed'])) {
                $xoopsConfig['theme_set'] = $xoopsUser->getVar('theme');
            }
        }
        if (isset($GLOBALS['xoopsOption']['pagetype']) && $GLOBALS['xoopsOption']['pagetype'] == "admin") {
            //if admin theme is set to use frontside theme
            if (is_numeric($xoopsConfig['theme_set_admin']) && $xoopsConfig['theme_set_admin'] == 0) {
                //set current admin theme to frontside theme
                $xoopsConfig['theme_set_admin'] = $xoopsConfig['theme_set'];
            }
            //check theme for admin template
            if ($this->tplEngine->template_exists($xoopsConfig['theme_set_admin'].'/themeadmin.html')) {
                //set the current theme to the one selected for admin area
                $xoopsConfig['theme_set'] = $xoopsConfig['theme_set_admin'];
            }
            else {
                //revert to default theme for admin area
                $xoopsConfig['theme_set'] = $this->name;
            }
        }
        $GLOBALS['xoopsOption']['theme_use_smarty'] = 1;
    }

    function getCachedTemplateId() {
        // generate safe cache Id
        $cachegroup = isset($GLOBALS['xoopsOption']['cache_group']) ? "_".$GLOBALS['xoopsOption']['cache_group'] : "";

        $protocol = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], "/", 0)));
        return 'mod_'.$GLOBALS['xoopsModule']->getVar('dirname').'|'.md5(str_replace(XOOPS_URL, '', $protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$cachegroup));
    }

    /**
    * Checks if a template is cached
    *
    * @param string $template name of template
    *
    * @return bool
    **/
    function is_cached($template) {
        return $this->tplEngine->is_cached($template, $this->getCachedTemplateId());
    }

    /**
    * Function: Creates a pretty menu and navigation bar above your module admin page
    *
    * I've nicked the basis of this from functions.php,v 1.2 of the new Profiles module
    * developed by hyperpod for Xoops V1.2, who in turn based this on Hsalazar's work with Soapbox
    *
    * @version 1
    * @author A Kitson
    * @param int $currentoption The menu option to display as being current
    * @param string $breadcrumb the trail back to where we've come from
    */
    function loadModuleAdminMenu ($currentoption = 0, $breadcrumb = '')
    {
        /**
    	* @global object $xoopsModule {@link XoopsModule} object for the current module
    	*/
        global $xoopsModule;

        /**
    	* @var array $menuoptions - get the adminmenu variables from the template object (assigned during xoops_cp_header() )
    	*/
        $menuoptions = $this->tplEngine->get_template_vars('adminmenu');
        /**
        * If the current module has menu links there
        */
        if (isset($menuoptions[$xoopsModule->getVar('mid')])) {
            /**
            * Add the breadcrumb to the links
            */
            $menuoptions[$xoopsModule->getVar('mid')]['breadcrumb'] = $breadcrumb;
            /**
            * Add the currently selected option
            */
            $menuoptions[$xoopsModule->getVar('mid')]['current'] = $currentoption;
            /**
            * Assign the links with additional information to the template object
            */
            $this->tplEngine->assign('modulemenu', $menuoptions[$xoopsModule->getVar('mid')]);
        }
    }

    /**
    * Checks the cache of the current page template (if set in $xoopsOption)
    *
    * @return void
    **/
    function checkCache() {
        global $xoopsModule, $xoopsConfig;
        if (xoops_getenv('REQUEST_METHOD') != 'POST' && !empty($xoopsModule) && !empty($xoopsConfig['module_cache'][$xoopsModule->getVar('mid')])) {
            global $xoopsOption;
            //Enable caching
            $this->tplEngine->xoops_setCaching(2);
            $this->tplEngine->xoops_setCacheTime($xoopsConfig['module_cache'][$xoopsModule->getVar('mid')]);

            if (isset($xoopsOption['template_main'])) {
                $xoopsCachedTemplate = 'db:'.$xoopsOption['template_main'];
                //Check caching
                if ($this->is_cached($xoopsCachedTemplate)) {
                    //Add logger message
                    global $xoopsLogger;
                    $xoopsLogger->addExtra($xoopsCachedTemplate, sprintf('Cached (regenerates every %d seconds)', $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')]));

                    //serve page
                    $this->display($xoopsCachedTemplate);

                    global $xoopsLogger, $xoopsUser;
                    $xoopsLogger->stopTime();
                    if (in_array(2, $xoopsConfig['debug_mode']) && $xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                        //add SQL debug window
                        echo $xoopsLogger->getSQLDebug();
                    }
                    exit();
                }
            }

        } else {
            $this->tplEngine->xoops_setCaching(0);
        }
    }
}
?>