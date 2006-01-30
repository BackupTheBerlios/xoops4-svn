<?php


define( 'XO_LEVEL_DISABLED', -1 );
define( 'XO_LEVEL_ANONYMOUS', 0 );
define( 'XO_LEVEL_INACTIVE', 1 );
define( 'XO_LEVEL_REGISTERED', 2 );
define( 'XO_LEVEL_ADMIN', 256 );


class xoops_kernel_User {
	
	var $userId = 0;

	var $level = XO_LEVEL_ANONYMOUS;
	var $groups = array( XOOPS_GROUP_ANONYMOUS );
	
	var $login = 'anonymous';
	var $password = '';
	
	var $email = '';
	
	var $fullName = '';

}




?>