<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author Penuel
 */
class LMVC_RegExRoute extends LMVC_Route {


    public function __construct($route, $handler = array()) {
        $this->routePattern = $route;
        $this->routeHandler = $handler;  //handler defines controller and action;
        $this->type ='regex';
    }



}

?>
