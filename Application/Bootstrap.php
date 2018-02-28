<?php
//global variables
$db; $dataCache;

# set time zone
if(defined('PHP_TIMEZONE_STRING') && PHP_TIMEZONE_STRING !='')
{
	date_default_timezone_set(PHP_TIMEZONE_STRING);
}


# init front controller
$front = LMVC_Front::getInstance();

#start session
LMVC_Session::init();

#set layouts if any

#set directories
$front->setApplicationDirectory(APPLICATION_PATH);
$front->setControllerDirectory(array('admin'=>'/admin/controllers/','scripts'=>'/scripts/controllers/'));
$front->setViewRenderer('Smarty');
$front->setViewCaching(false);

//user module uses the Default module layout..

LMVC_Layout::getInstance()->setLayout('user', 'layouts');



#register plugins

/*
$adminSessionValidator = new Plugins_AdminSessionValidator();
$front->registerPlugin($adminSessionValidator);

$userSessionValidator = new Plugins_UserSessionValidator();
$front->registerPlugin($userSessionValidator);


$languageSetter = new Plugins_LanguageSetter();
$front->registerPlugin($languageSetter);
*/

//$front->registerViewHelper(new Helpers_Views_SuccessMessage());
//$front->registerViewHelper(new Helpers_Views_Translator());


//Registering for Tooltip helper
//$front->registerViewHelper(new Helpers_Views_Tooltip());

//$front->registerPlugin($customerSessionValidator);
//$breadCrumbHelper = new Helpers_Views_BreadCrumb();
//$front->registerViewHelper($breadCrumbHelper);


//$menu = new Helpers_Views_Menu();
//$front->registerViewHelper($menu);


//$footer = new Helpers_Views_Footer();
//$front->registerViewHelper($footer);
//$front->registerViewHelper(new Helpers_Views_TranslatorFormatted());


/*
$customerSessionValidator = new Plugins_CustomerSession();
$front->registerPlugin($customerSessionValidator);

$frontMenu = new Plugins_FrontMenu();
$front->registerPlugin($frontMenu);

$seo = new Plugins_SEO();
$front->registerPlugin($seo);

$front->registerViewHelper(new Helpers_Views_ActiveShortCut());
$front->registerViewHelper(new Helpers_Views_SubMenu());
$front->registerViewHelper(new Helpers_Views_SuccessMessage());

$catRoutes = new Plugins_CategoryRoutes();
$front->registerPlugin($catRoutes);

$frontMenu = new Plugins_FrontMenu();
$front->registerViewHelper(new Helpers_Views_FormatPrice());
*/



$router = LMVC_Router::getInstance();

#routing
/*
# Edit wish list
$router->addRoute('wishlist_edit', 
	new LMVC_Route('wishlist/e/:key',
 		array(
 			'module'=>'Default',
 			'controller'=>'wishlist',
 			'action'=>'edit',
 		)));

#View  wishlist
$router->addRoute('wishlist_view',
		new LMVC_Route('wishlist/v/:key',
				array(
						'module'=>'Default',
						'controller'=>'wishlist',
						'action'=>'view',
				)));

#Invalid wishlist
$router->addRoute('wishlist_invalid',
		new LMVC_Route('wishlist/i/:invalid',
				array(
						'module'=>'Default',
						'controller'=>'wishlist',
						'action'=>'invalid',
				)));

#Successful create wishlist
$router->addRoute('wishlist_success',
	new LMVC_Route('wishlist/s/:success',
		array(
			'module'=>'Default',
			'controller'=>'wishlist',
			'action'=>'success',
		)));


#Successful registration
$router->addRoute('manageproducts',
	new LMVC_Route('wishlist/mp/:key',
		array(
			'module'=>'Default',
			'controller'=>'wishlist',
			'action'=>'manageproducts',
		)));

#To display page
$router->addRoute('page', new LMVC_RegExRoute('page\/(.*)\/?',
		array(
				'module' => 'Default',
				'controller' => 'page',
				'action' => 'index',
				'regex_params' => array(1=>'pageslug')
		)));
*/
	
function minify_callback($buffer)
{

	$search = array(
			'/\s+\/\/[^\n]+/s',  // strip whitespaces after tags, except space			
	);
	$replace = array(
			''
	);
	$buffer = preg_replace($search, $replace, $buffer);
	
	$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
	);

	$replace = array(
			'>',
			'<',
			'\\1'
	);

	$buffer = preg_replace($search, $replace, $buffer);

	return $buffer;

}

ob_start('minify_callback');



#dispatch now!
$front->dispatch();



//debug db+php time sync.
/*

echo "DB: ".  $db->fetchOne("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()))") ."<br/>";
echo "PHP: ". date('Y-m-d H:i:s');
die(); 

*/	


#destroy
unset($front);

?>
