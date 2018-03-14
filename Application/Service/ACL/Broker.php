<?php

/**
 * ACL Broker Class that provides access to Various ACL Classes for different modules
 * 
 *  
 */
class Service_ACL_Broker {

    private static $instance;
    private static $acls = array();
    private static $serviceClassPrefix = 'Service_ACL_';

    private function __construct($aclClass) {

    	$className = $aclClass;
    	$obj = Service_ACL_Abstract::getInstance($className);
    	if(is_subclass_of($obj, 'Service_ACL_Abstract'))
    	{
    		self::$acls[$className] = $obj;
    	}
    	else
    	{
    		trigger_error('ACL Class must be sub class of Service_ACL_Abstract Class', E_USER_ERROR);
    	}
    	
    }

    
    
    public static function getInstance($acl) {
    	
    	$aclClass = self::$serviceClassPrefix . $acl;
        if (!isset(self::$instance)) {
            $c = __CLASS__;            
            self::$instance = new $c ($aclClass);
        }
        return self::$acls[$aclClass];
       
    }
    
    
   
}

?>
