<?php
//config constants.

define('PHP_TIMEZONE_STRING','UTC');	//set the timezone string as per your app timezone requirements. Make sure it matches with DB time zone below.
define('DB_TIMEZONE_STRING', '+0:00');

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);	
define('SITE_URL', "http://" . $_SERVER['HTTP_HOST']);

define('VIEW_COMPILE_DIR', APPLICATION_PATH .'/extras/templates_c');
define('VIEW_CACHE_DIR', APPLICATION_PATH .'/extras/templates_cache');
define('VIEW_CONFIG_DIR', APPLICATION_PATH .'/extras/templates_config');

define('SYSTEM_FROM_EMAIL','no-reply@someproject.com <Project Name>');	//it could be your project name

if ($_SERVER['HTTP_HOST'] == "template.regurmvctemplate.com") 
{
	define('DB_HOST', 'localhost');
    define('DB_UNAME', 'root');
    define('DB_PWD', 'root');
    define('DB_NAME', '');
    define('DEBUG_EMAIL', 'your_email_address@regur.net');       
    define('ENV', "development");      
    ini_set('display_errors',"On");
    error_reporting(E_ALL);    
}
elseif ($_SERVER['HTTP_HOST'] == "your.localhost:1116") //another dev block or some staging env block by copying the above block. Create as may per your requirments
{
    
}
else	//production/live env block!!
{
	
	define('DB_HOST', 'localhost');
	define('DB_UNAME','');
	define('DB_PWD', '');
	define('DB_NAME','');
	define('DEBUG_EMAIL','some_real@regur.net');
	
	define('ENV', "production");
	ini_set('display_errors',"Off");	
	error_reporting(0);		
}


?>