<?php
class LMVC_View 
{	
	private static $instance;
	private $_view;
	private $_viewFile;
	private $_viewDir;
	private $_layoutDir;
	private $_layout;
	private $_viewVars;	
	private $_viewRenderer;	
	private $_stylesheets = array();
	private $_headerScripts = array();
	private $_footerScripts = array();
	private $_viewCaching = false;
	
	
	private function __construct(){}
	
	final public static function getInstance($className=''){
		if (!isset(self::$instance)) {
			
			if(empty($className))
			{
				trigger_error('Required renderer class name not specified. Renderer must be registered with $front in bootstrap.php', E_USER_ERROR);				
			}
			
			$c = __CLASS__;
			self::$instance = new $c;
			self::$instance->init($className);
		}
		return self::$instance;
	}
	
	private function init($className){
		$this->_viewVars = array();	
		$className = 'LMVC_'.$className.'Renderer';
		$this->setViewRenderer($className::getInstance());
	}
	
	
	
	public function setViewRenderer($_viewRenderer){
		if(isset($this->_viewRenderer)){
			unset($this->_viewRenderer);
		}
		$this->_viewRenderer = $_viewRenderer;	
		$this->setupRenderer();
		$this->_viewRenderer->init();		
	}	
	
	public function setViewCaching($_caching)
	{
		$this->_viewCaching = $_caching;
		$this->getViewRenderer()->setCaching($_caching);
	}
	
	public function getViewRenderer()
	{
		return $this->_viewRenderer;
	}
	
	public function setViewFile($_viewFile){	
		$this->_viewFile = $_viewFile;
	}
	
	public function getViewFile(){	
		return $this->_viewFile;
	}
	
	public function setViewDir($_viewDir){	
		$this->_viewDir = $_viewDir;
	}
	
	public function getViewDir(){	
		return $this->_viewDir;
	}
	
	public function setLayoutDir($_layoutDir){
		$this->_layoutDir = $_layoutDir;
	}	
	
	public function setLayout($_layout){
		$this->_layout = $_layout;
	}
	
	public function setViewVar($_varKey, $_varVal)
	{		
		$this->_viewVars[$_varKey] = $_varVal;
		
	}
	
	public function getViewVars($_varKey=null)
	{
		if(!empty($_varKey))
		{
			if(isset($this->_viewVars[$_varKey])) 
				return $this->_viewVars[$_varKey];
			else
				trigger_error("Undefined viewVar $_varName", E_USER_NOTICE);
		}
		else
		{
			return $this->_viewVars;
		}		
		
	}
	
	public function setViewVars($_viewVars){
		if(empty($this->_viewVars))
		{
			$this->_viewVars = $_viewVars;	
		}	
		else{
			foreach($_viewVars as $key=>$val)
			{
				$this->_viewVars[$key] = $val;
			}
		}		
	}

	public function registerStylesheet($stylesheet)
	{
		if(!in_array($stylesheet, $this->_stylesheets))
		{
			array_push($this->_stylesheets, $stylesheet);	
		}
		
	}
	
	public function registerHeaderScript($script)
	{
		if(!in_array($script, $this->_headerScripts))
		{
			array_push($this->_headerScripts, $script);	
		}
		
	}
	
	public function registerFooterScript($script)
	{
		if(!in_array($script,$this->_footerScripts))
		{
			array_push($this->_footerScripts, $script);	
		}
	}
	
	private function makeStylesheetTags($_stylesheets)
	{
		$linkTags = "";
		foreach($_stylesheets as $stylesheet){
			$linkTags .= '<link href="'. $stylesheet .'" type="text/css" rel="stylesheet"/>' ."\n"; 
		}
		return $linkTags;
	}
	
	private function makeScriptTags($_scripts)
	{
		$scriptTags = "";
		foreach($_scripts as $script){
			$scriptTags .= '<script src="'. $script .'" type="text/javascript" langauge="javascript" ></script>' ."\n";
		}
		return $scriptTags;
	}
	
	public function render(){
					
		$viewContent = $this->renderView();
				
		if(LMVC_Front::getInstance()->isLayoutEnabled())
		{			
			echo $this->_viewRenderer->renderLayout($viewContent);
		}
		else
		{			
			echo $viewContent;
		}
		
	}
	
	public function renderView(){
		$this->setupRenderer();
		return $this->_viewRenderer->renderView();		
	}
	
	public function renderViewFragment($_viewFragmentDir, $_viewFragmentFile, $_viewFragmentVars = array())
	{
		
		$fullViewVars = $this->getViewVars();
		$viewVars = array_merge($_viewFragmentVars, array('view_vars'=> $fullViewVars));		
		return $this->_viewRenderer->renderViewFragment($_viewFragmentDir, $_viewFragmentFile, $viewVars);
	}
	
	private function setupRenderer(){
		$styleSheets = $this->makeStylesheetTags($this->_stylesheets);
		$headerScripts = $this->makeScriptTags($this->_headerScripts);
		$footerScripts = $this->makeScriptTags($this->_footerScripts);
		
		$this->setViewVar('header_scripts', $headerScripts);
		$this->setViewVar('footer_scripts', $footerScripts);
		$this->setViewVar('style_sheets', $styleSheets);
		
		if(LMVC_Front::getInstance()->isLayoutEnabled())
		{
			$this->_viewRenderer->setLayout($this->_layout);
			$this->_viewRenderer->setLayoutDir($this->_layoutDir);			
		}		
		$this->_viewRenderer->setViewFile($this->_viewFile);
		$this->_viewRenderer->setViewDir($this->_viewDir);			
		$this->_viewRenderer->setViewVars($this->_viewVars);
		
	}
}
?>