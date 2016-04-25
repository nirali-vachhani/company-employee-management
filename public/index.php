<?php
$start_time = microtime();
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../Application/'));
define('MVC_PATH', realpath(dirname(__FILE__) . '/../'));



set_include_path(get_include_path() .PATH_SEPARATOR . MVC_PATH);
		
set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH);

require(APPLICATION_PATH .'/ApplicationConfig.php');

require(APPLICATION_PATH .'/functions.php');

require('LMVC/Front.php');


require(APPLICATION_PATH . "/Bootstrap.php");

?>