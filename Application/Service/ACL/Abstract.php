<?php

/**
 * Abstract service class that provides ACL capabilities to check/authorize user permissions.
 * 
 *  
 */
abstract class Service_ACL_Abstract {

    private static $instance;
    private $roles = array();
    private $fullAccessRole = '';
    private $currentUserRole = '';

    private function __construct() {

    	$this->registerRoles();
    	
    	if(count($this->roles) == 0)
    	{
    		trigger_error('Roles not defined. Please add a role and permissions in base class', E_USER_ERROR);
    	}
    	
    	$this->fullAccessRole = $this->fullAccessRoleName();
    	
    	if(empty($this->fullAccessRole))
    	{
    		trigger_error('Full access role is not defined.', E_USER_ERROR);
    	}
    	
    	$this->currentUserRole = $this->getCurrentUserRole();
    	
    	if(empty($this->currentUserRole) && $this->currentUserRole !== false)
    	{
    		trigger_error('Current user role is not defined.', E_USER_ERROR);
    	}
    }

    
    
    public static function getInstance($className) {
        if (!isset(self::$instance)) {
            self::$instance = new $className;
        }
        return self::$instance;
    }
    
    
    
    public function addRole($roleName, $permissions)
    {
    	if(is_array($permissions) && count($permissions)>0)
    	{
    		$this->roles[$roleName] = $permissions;
    	}
    }

    /**
     * checks if he current user is of role $roleName;
     * 
     * @param unknown $roleName
     * @return boolean
     */
    public function isUserOfRole($roleName) {
        if ($this->currentUserRole == $roleName) {
            return true;
        } else {
            return false;
        }
    }

    public function currentUserHasAccess($controllerName = "", $actionName = "") {
        return $this->userHasAccess($controllerName, $actionName, $this->currentUserRole);
    }

    public function userHasAccess($controllerName = "", $actionName = "", $role = "") {

        $request = LMVC_Request::getInstance();
        if (empty($controllerName)) {

            $currentController = $request->getControllerName();
        } else {
            $currentController = $controllerName;
        }
        

        //action

        if (empty($actionName)) {
            $currentAction = $request->getActionName();
        } else {
            $currentAction = $actionName;
        }
		


        if (empty($role)) {
            $userRole = $this->currentUserRole; //LMVC_Session::get('userRole');
        } else {
            $userRole = $role;
        }



        if ($this->isUserOfRole($this->fullAccessRole)) {
            return true;
        } else {

            if (array_key_exists($userRole, $this->roles)) {

                if (in_array($currentController, array_keys($this->roles[$userRole]))) {

                    $allowList = $this->roles[$userRole][$currentController][0];
                    $denyList = $this->roles[$userRole][$currentController][1];
                    if ($allowList == "*") {

                        $deniedActions = explode(",", $denyList);
						if(is_array($currentAction))
						{
							foreach($currentAction as $ca)
							{
								if (!in_array($ca, $deniedActions)) {
									return true;
								}
							}
						}
						else
						{
							if (!in_array($currentAction, $deniedActions)) {
								return true;
							}
						}
                    } else {
                        $allowedActions = explode(",", $allowList);

						if(is_array($currentAction))
						{
							foreach($currentAction as $ca)
							{
								if (in_array($ca, $allowedActions)) {
									return true;
								}
							}
						}
						else
						{

							if (in_array($currentAction, $allowedActions)) {
								return true;
							}
						}
                    }
                }
            }
        }


        return false;
    }
    
    abstract protected function registerRoles();
    
    abstract public function fullAccessRoleName();
    
    abstract public function getCurrentUserRole();

}

?>
