<?php

/**
 * Service ACL Class
 * 
 *  
 */
class Service_ACL_User extends Service_ACL_Abstract
{

	protected function registerRoles()
	{
		
		//specify your controllers and actions access for this Role over here..
	
		$permissions =  array(
		 'login' => array("*", ""),
		 'logout' => array("*", ""),
		 'unauthorized' => array("*", ""),
		 'dashboard' =>  array("*",""),
		 'index' => array("*",""), 
		 'profile' => array("*", ""),
		 'error' => array("*", ""),		 
		 'orders' => array("*", ""),
		'calendar' => array("*", ""),
		 );
		 
		$this->addRole('normal', $permissions);
		

		
	}
	
	public function fullAccessRoleName()
	{
		return 'primary';
	}
	
	public function getCurrentUserRole()
	{
		if(LMVC_Session::get('userRole') !='')
		{
			return LMVC_Session::get('userRole');
		}
		else
		{
			return false;
		}
	}
}

?>
