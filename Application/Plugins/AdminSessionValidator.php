<?php
class Plugins_AdminSessionValidator{
	
	private $exemptedProperties = array();
             
	public function preDispatch(LMVC_Request $request){
		$this->exemptedProperties['login'] = array('index');
		$this->exemptedProperties['logout'] = array('index');
		
		if($request->getModuleName() == "admin"){
			$this->setVars();
			
			$currentController = $request->getControllerName();
			$currentAction = $request->getActionName();
			
			if(array_key_exists($currentController,$this->exemptedProperties)){
				
				if(in_array($currentAction,$this->exemptedProperties[$currentController])){
					return;
				}
				else{
					$this->validateSession();
				}	
			}else{
				$this->validateSession();
			}
			
			
		}
	}
	
	private function validateSession()
	{
		$id = LMVC_Session::get('adminId');
		if(empty($id))
		{			
			$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];	
				
			header("Location: /admin/login?retURL=$retUrl");
			exit();	
		}	
		else
		{
			$this->checkAuthorization();
		}

	}
	
	
	private function checkAuthorization()
	{
		
		$acl = Service_ACL_Broker::getInstance('Admin');		
		
		if(!$acl->currentUserHasAccess())
		{	
			header("Location: /admin/unauthorized/");
			exit();
		}
		else
		{
	
		}
	}

	private function setVars()
	{
		$adminId = LMVC_Session::get('adminId');		
		$adminUsername = LMVC_Session::get('adminUsername');
		$adminFullName = LMVC_Session::get('adminFullName');
		if(empty($adminId)) $adminId=0;
		
		LMVC_Front::getInstance()->setPreDispatchVar('sessionAdminId',$adminId);		
		LMVC_Front::getInstance()->setPreDispatchVar('sessionAdminUsername',$adminUsername);
		LMVC_Front::getInstance()->setPreDispatchVar('sessionAdminFullname',$adminFullName);		
		LMVC_Front::getInstance()->setPreDispatchVar('acl',  Service_ACL_Broker::getInstance('Admin'));
	}

}
?>
