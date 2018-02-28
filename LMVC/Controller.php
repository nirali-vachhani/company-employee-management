<?php
abstract class LMVC_Controller
{
	private $_view;
	private $pageTitleKey = "page_title";
	private $metaDescKey = "meta_desc";
	private $_actionErrors;

	private $properties = array();

	final public function __construct(){
		$this->_view = LMVC_View::getInstance();
		$this->_actionErrors = array();
	}

	public function __set($name, $value)
	{
		
		$this->properties[$name] = $value;
	}

	public function __get($name)
	{
		
		if (array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}

		$trace = debug_backtrace();
		trigger_error(
		'Undefined property via __get(): ' . $name .
		' in ' . $trace[0]['file'] .
		' on line ' . $trace[0]['line'],
		E_USER_NOTICE);
		
		return null;
	}

	final public function setTitle($_title)
	{
		$this->_view->setViewVar($this->pageTitleKey, $_title);
	}
	final public function setMetaDescription($_description)
	{
		$this->_view->setViewVar($this->metaDescKey, $_description);
	}
	final public function setNoRenderer($_noRenderer)
	{
		LMVC_Front::getInstance()->setNoRenderer($_noRenderer);
	}

	final public function getTitle()
	{
		$this->_view->getViewVar($this->pageTitleKey);
	}
	final public function  isPost()
	{
		return LMVC_Request::getInstance()->isPost();
	}
	final public function getRequest()
	{
		return LMVC_Request::getInstance();
	}
	final public function setViewVar($_varName, $_val)
	{
		$this->_view->setViewVar($_varName, $_val);
	}
	final public function setViewVars($_arrVars)
	{
		foreach($_arrVars as $key=> $value)
		{
			$this->_view->setViewVar($key,$value);
		}
	}
	final public function registerStylesheet($stylesheet)
	{
		$this->_view->registerStylesheet($stylesheet);
	}
	final public function registerHeaderScript($script)
	{
		$this->_view->registerHeaderScript($script);
	}
	final public function registerFooterScript($script)
	{
		$this->_view->registerFooterScript($script);
	}
	final public function addError($err_msg)
	{
		if(is_array($err_msg))
		{
			foreach($err_msg as $msg)
			{
				array_push($this->_actionErrors, $msg);
			}

		}
		else
		{
			if(!empty($err_msg))
				array_push($this->_actionErrors,$err_msg);
		}

	}
	final public function hasErrors()
	{
		if(count($this->_actionErrors)>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	final public function getErrors($index = null) {

		if (empty($index)) {
			return $this->_actionErrors;
		} else {
			if (!isset($this->_actionErrors[$index])) {
				trigger_error("No error message exists at the specified $index", E_USER_WARNING);
			} else {
				return $this->_actionErrors[$index];
			}
		}
	}


	

	final public function forward($_action, $_controller = "", $_module = "", $_params = array())
	{
		$this->setNoRenderer(false);
		$front = LMVC_Front::getInstance();
		$front->resetReRouting();

		if(empty($_controller))
		{
			$controller = $this->getRequest()->getControllerName();

			header("Status: 404 Not Found");



		}
		else
		{
			$controller = $_controller;
		}

		$front->dispatch($_module,$controller,$_action, $_params);

		exit();
	}

	final public function registerViewHelper($helper,$stack_index = null)
	{
		$rtn = LMVC_ViewHelperBroker::getInstance()->registerHelper($helper, $stack_index);
		if($rtn)
		{
			$viewObj = LMVC_View::getInstance();
			$viewObj->getViewRenderer()->registerHelper($helper);
		}
		else
		{
			trigger_error("Helper not registered",E_USER_NOTICE);
		}
	}
	
	final public function getViewHelper($helper_name)
	{
		return LMVC_ViewHelperBroker::getInstance()->getHelper($helper_name);
		
	}
	
	



}
?>