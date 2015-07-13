<?php

class Helpers_Views_SiteConfig
{

    public function __construct($params)
    {
    	
    	

        $siteConfig = new Models_SiteConfig();
        $smarty = LMVC_SmartyRenderer::getInstance()->getRenderer();
        $smarty->assign('siteConfig', $siteConfig);

    }
    
   

}
