<?php
class Plugins_ClientSessionValidator{
	
	private $exemptedProperties = array();
             
	public function preDispatch(LMVC_Request $request){
		$this->exemptedProperties['login'] = array('index');
		$this->exemptedProperties['logout'] = array('index');
		$this->exemptedProperties['resetpassword'] = array('index','reset','success');
		
		if($request->getModuleName() == "client"){
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
		$id = LMVC_Session::get('id');
		$clientId = LMVC_Session::get('clientId');
		if(empty($id))
		{			
			$this->setVars();
			$retUrl = $_SERVER['REQUEST_URI'];	
				
			header("Location: /client/login/?retUrl=".$retUrl);
			exit();	
		}
		else
		{
			$this->checkAuthorization();
		}
		

	}
	
	private function checkAuthorization()
	{
	
		$acl = Service_ACL_Broker::getInstance('Client');
	
		if(!$acl->currentUserHasAccess())
		{
			header("Location: /client/unauthorized/");
			exit();
		}
		else
		{
	
		}
	}

	private function setVars()
	{
		$id = LMVC_Session::get('id');
		$clientId = LMVC_Session::get('clientId');
		$clientUserFullName =  LMVC_Session::get('clientUserFullName');
		$clientName =  LMVC_Session::get('clientName');
		if(empty($id)) $id=0;
		
		LMVC_Front::getInstance()->setPreDispatchVar('sessionId',$id);
		LMVC_Front::getInstance()->setPreDispatchVar('sessionClientId',$clientId);
		LMVC_Front::getInstance()->setPreDispatchVar('sessionClientUserFullName',$clientUserFullName);
		LMVC_Front::getInstance()->setPreDispatchVar('sessionClientName',$clientName);
		LMVC_Front::getInstance()->setPreDispatchVar('acl',  Service_ACL_Broker::getInstance('Client'));
	}

	
	
}
?>
