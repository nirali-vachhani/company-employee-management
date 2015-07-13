<?php
$pathToDB = dirname(__FILE__);		
set_include_path(get_include_path() .PATH_SEPARATOR . $pathToDB . '/Libs/PEAR');
set_include_path(get_include_path() .PATH_SEPARATOR . $pathToDB . '/Libs');



final class LMVC_DB2
{	
	
	private $db;
	private $dbType;
	
	const AUTOQUERY_INSERT = 1;
	
	

	
	private function __construct($host, $username, $password, $dbname, $type = 'Zend_DB', $zend_driver ='')
	{
     
       
	}
	
	public static function factory($host, $username, $password, $dbname, $factory, $adapter)
	{
		return $factory::factory($adapter, array(
    				'host'     => $host,
    				'username' => $username,
    				'password' => $password,
    				'dbname'   => $dbname));
	}


	public function __clone() {
       trigger_error('Clone is not allowed.', E_USER_ERROR);
   	}
   	
   	public function getDB()
   	{
   		return $this->db;
   	}
	
   	/*
    private function connect($host, $username, $password, $dbname, $type = 'Zend_DB', $zend_driver ='')
    {
    	if($type == 'PEAR_DB')
    	{
    		require_once('PEAR.php');
    		require_once('Libs/PEAR/DB.php');    		
    		$this->dbType = 'PEAR_DB';
    	
	    	$dsn = 'mysql://'.$username.':'.$password.'@'.$host.'/'.$dbname;
	    	$options = array(
			    'debug'       => 2,
			    'portability' => DB_PORTABILITY_ALL ^ DB_PORTABILITY_LOWERCASE,
			);
	    	
	    	$this->db = DB::connect($dsn, $options);
	    	
	    	if(PEAR::isError($this->db)){
	    		$this->handleError($this->db);
	    	}
	    	else{
	    		$this->db->setErrorHandling(PEAR_ERROR_CALLBACK, array($this,'handleError'));
	    	}
    	}
    	else
    	{
    		$this->dbType = 'Zend_DB';    		
    		$this->db = Zend_Db::factory('Pdo_Mysql', array(
    				'host'     => 'localhost',
    				'username' => 'root',
    				'password' => 'root',
    				'dbname'   => 'looyalty'
    		));    		
    	}
    	return $this->db;
    }*/
    
    public function handleError($error_object)
    {
    	
    	if(PEAR::isError($error_object)){
				
			$back_trace = $error_object->getBackTrace();
				
			$strTrace = "";
			$back_trace= array_reverse($back_trace);

			foreach($back_trace as $trace){
				if(isset($trace['line'])){
					$strTrace .= "LINE: ". $trace['line'] ;
				}

				if(isset($trace['file'])){
					$strTrace .= " File: ". $trace['file'] ;
				}
				if(isset($trace['class'])){
					$strTrace .= " Class: ". $trace['class'] ;
				}
				if(isset($trace['function'])){
					$strTrace .= " Function: ". $trace['function'] ;
				}
				$strTrace .= "<br>" ;
			}
				
			$message = "<p align=\"left\">". $error_object->getUserInfo() ."</p>";
			if(!PEAR::isError($this->dbObj)){
				$message .= "<p align=\"left\">LAST QUERY: <br>". $this->dbObj->last_query."</p>";
			}
			
			$message .= "<p align=\"left\">". $strTrace ."</p>";
			if(!PEAR::isError($this->dbObj)){
				throw new LMVC_Exception($error_object->getUserInfo());
			}
			else
			{
				echo "<pre>";
			
				echo $message;
				
				//@@todo:
				
				//mail error to webmaster
    	
    			echo "<pre>";
    				
    			die();
			}
				
			
		}
    	
    }
	
}
?>