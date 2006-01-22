<?php
class XoopsAuthFactory
{

	function XoopsAuthFactory()
	{
	}

	/**
	 * Get a reference to the only instance of authentication class
     * 
     * if the class has not been instantiated yet, this will also take 
     * care of that
	 * 
     * @static
     * @return      object  Reference to the only instance of authentication class
	 */
	function &getAuthConnection()
	{
		static $auth_instance;		
		if (!isset($auth_instance)) {
			$config_handler =& xoops_gethandler('config');
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('conf_name', 'auth_method'));
			$config =& $config_handler->getConfigs($criteria); 			
			require_once XOOPS_ROOT_PATH.'/class/auth/auth.php';
			if (!$config) { // If there is a config error, we use xoops
				$xoops_auth_method = 'xoops';
			} else {
			    $xoops_auth_method = $config[0]->getConfValueForOutput();
			    if(!is_readable(XOOPS_ROOT_PATH.'/class/auth/auth_' . $xoops_auth_method . '.php')){
					$xoops_auth_method = 'xoops';
				}
			}
			$file = XOOPS_ROOT_PATH.'/class/auth/auth_' . $xoops_auth_method . '.php';
			require_once $file;
			$class = 'XoopsAuth' . ucfirst($xoops_auth_method);
			switch ($xoops_auth_method) {
				case 'xoops' :
					$dao =& $GLOBALS['xoopsDB']; 
					break;
				case 'ldap'  : 
					$dao = null;
					break;
			}
			$auth_instance = new $class($dao);
		}
		return $auth_instance;
	}

}
?>