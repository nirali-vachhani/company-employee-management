<?php
class ErrorController extends LMVC_Controller{
	
	public function init(){
		
	}
	
	public function errorAction(){		
		
			$e = LMVC_Front::getException();
			$this->setViewVar('e', $e);
			
	}
	
	
	
	
}
?>