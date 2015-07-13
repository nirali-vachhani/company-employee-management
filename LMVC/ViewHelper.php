<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ViewHelper
 *
 * @author Penuel
 */
abstract class LMVC_ViewHelper {

    protected $helperName = '';

    //put your code here
    final public function __construct() {        
        $this->init();
    }

    

    final public function getHelperName() {
        return $this->helperName;
    }
    
    abstract public function init();

    abstract public function helperFunc($params);
}

?>
