<?php
//global variables
$db; $dataCache;

# set time zone
if(defined('PHP_TIMEZONE_STRING') && PHP_TIMEZONE_STRING !='')
{
	date_default_timezone_set('UTC');
}


# init front controller
$front = LMVC_Front::getInstance();

#start session
LMVC_Session::init();

#set layouts if any

#set directories
$front->setApplicationDirectory(APPLICATION_PATH);
$front->setControllerDirectory(array('admin'=>'/admin/controllers/'));



#regiser plugins

//$adminSessionValidator = new Plugins_AdminSessionValidator();
//$front->registerPlugin($adminSessionValidator);

//$customerSessionValidator = new Plugins_CustomerSessionValidator();
//$front->registerPlugin($customerSessionValidator);

//$front->registerViewHelper(new Helpers_Views_SuccessMessage());





//$front->registerViewHelper(new Helpers_Views_Translator());

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
/*
$router->addRoute('job_view_page',
		new LMVC_Route('jobs/i/:id/s/:s',
				array(
						'module'=>'Default',
						'controller'=>'job',
						'action'=>'view'
				)));
*/
	
/*
$router->addRoute('customer_account', new LMVC_RegExRoute('customer\/([^\/]+)\/([^\/]+)\/([^\/]+)\/?',
		array(
				'module' => 'customer',
				'controller' => 'index',
				'action' => 'index',
				'regex_params' => array(1=>'guid',2=>'websiteKey',3=>'lang')
		)));

*/
#routing
/*
$router = LMVC_Router::getInstance();


//designer/:designerName/:page/:shapes/:occasions/:styles/:sizes/:colors/:prices
$router->addRoute('prod_list_designer', new LMVC_RegExRoute('designer\/([^\/]+)\/?(page-([0-9]{0,})\/?)?(md-([0-9,]{0,})\/?)?(shapes-([0-9,]{0,})\/?)?(occasions-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(styles-([0-9,]{0,})\/?)?(sizes-([0-9,]{0,})\/?)?(colors-([0-9,]{0,})\/?)?(price-([0-9,]{0,})\/?)?(pp-([0-9]{0,})\/?)?(sort-([^\/]+)\/?)?',
			array(
				'module' => 'Default',
				'controller' => 'productlist',
				'action' => 'index',
                'params' => array('mode'=>'designer'),
                'regex_params' => array(1=>'designerName',3=>'page',5=>'md',7=>'shapes',9=>'occasions',11=>'cats',13=>'styles',15=>'sizes',17=>'colors',19=>'price', 21=> 'pp',23=>'sort')
				)));

//search page
$router->addRoute('search_page', new LMVC_RegExRoute('search\/([^\/]+)\/?(page-([0-9]{0,})\/?)?(md-([0-9,]{0,})\/?)?(shapes-([0-9,]{0,})\/?)?(occasions-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(styles-([0-9,]{0,})\/?)?(sizes-([0-9,]{0,})\/?)?(colors-([0-9,]{0,})\/?)?(price-([0-9,]{0,})\/?)?(pp-([0-9]{0,})\/?)?(sort-([^\/]+)\/?)?',
    array(
        'module' => 'Default',
        'controller' => 'productlist',
        'action' => 'index',
        'params' => array('mode'=>'search'),
        'regex_params' => array(1=>'designerName',3=>'page',5=>'md',7=>'shapes',9=>'occasions',11=>'cats',13=>'styles',15=>'sizes',17=>'colors',19=>'price', 21=> 'pp',23=>'sort')
    )));

//new in page
$router->addRoute('whatsnew_page', new LMVC_RegExRoute('newin\/?(page-([0-9]{0,})\/?)?(md-([0-9,]{0,})\/?)?(shapes-([0-9,]{0,})\/?)?(occasions-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(styles-([0-9,]{0,})\/?)?(sizes-([0-9,]{0,})\/?)?(colors-([0-9,]{0,})\/?)?(price-([0-9,]{0,})\/?)?(pp-([0-9]{0,})\/?)?(sort-([^\/]+)\/?)?',
    array(
        'module' => 'Default',
        'controller' => 'productlist',
        'action' => 'index',
        'params' => array('mode'=>'newin'),
         'regex_params' => array(2=>'page',4=>'md',6=>'shapes',8=>'occasions',10=>'cats',12=>'styles',14=>'sizes',16=>'colors',18=>'price', 20=>'pp', 22=>'sort')
    		)));
    

//on sale page
$router->addRoute('sale_page', new LMVC_RegExRoute('sale\/?(page-([0-9]{0,})\/?)?(md-([0-9,]{0,})\/?)?(shapes-([0-9,]{0,})\/?)?(occasions-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(styles-([0-9,]{0,})\/?)?(sizes-([0-9,]{0,})\/?)?(colors-([0-9,]{0,})\/?)?(price-([0-9,]{0,})\/?)?(pp-([0-9]{0,})\/?)?(sort-([^\/]+)\/?)?',
    array(
        'module' => 'Default',
        'controller' => 'productlist',
        'action' => 'index',
        'params' => array('mode'=>'sale'),
        'regex_params' => array(2=>'page',4=>'md',6=>'shapes',8=>'occasions',10=>'cats',12=>'styles',14=>'sizes',16=>'colors',18=>'price', 20=>'pp', 22=>'sort')
    	
    )));

//newsletter page
$router->addRoute('newsletter_page', new LMVC_RegExRoute('newsletter\/([^\/]+)\/?(page-([0-9]{0,})\/?)?(md-([0-9,]{0,})\/?)?(shapes-([0-9,]{0,})\/?)?(occasions-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(styles-([0-9,]{0,})\/?)?(sizes-([0-9,]{0,})\/?)?(colors-([0-9,]{0,})\/?)?(price-([0-9,]{0,})\/?)?(pp-([0-9]{0,})\/?)?(sort-([^\/]+)\/?)?',
		array(
				'module' => 'Default',
				'controller' => 'productlist',
				'action' => 'index',
				'params' => array('mode'=>'newsletter'),
				'regex_params' => array(1=>'designerName',3=>'page',5=>'md',7=>'shapes',9=>'occasions',11=>'cats',13=>'styles',15=>'sizes',17=>'colors',19=>'price', 21=> 'pp',23=>'sort')
		)));



//mostpopular
$router->addRoute('mostpopular_page', new LMVC_RegExRoute('mostpopular\/?(page-([0-9]{0,})\/?)?(md-([0-9,]{0,})\/?)?(shapes-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(occasions-([0-9,]{0,})\/?)?(cats-([0-9,]{0,})\/?)?(styles-([0-9,]{0,})\/?)?(sizes-([0-9,]{0,})\/?)?(colors-([0-9,]{0,})\/?)?(price-([0-9,]{0,})\/?)?(pp-([0-9]{0,})\/?)?(sort-([^\/]+)\/?)?',
		array(
				'module' => 'Default',
				'controller' => 'productlist',
				'action' => 'index',
				'params' => array('mode'=>'mostpopular'),
				'regex_params' => array(2=>'page',4=>'md',6=>'shapes',8=>'occasions',10=>'cats',12=>'styles',14=>'sizes',16=>'colors',18=>'price', 20=>'pp', 22=>'sort')
		)));

#cms pages
$router->addRoute('cms_page',
        new LMVC_Route('page/:urltag',
        array(
            'module'=>'Default',
            'controller'=>'pages',
            'action'=>'index'
        )));
*/

/*
#wishlist pages
$router->addRoute('wishlist',
    new LMVC_Route('wishlist/:page',
        array(
            'module'=>'Default',
            'controller'=>'wishlist',
            'action'=>'index'
        )));		
*/


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