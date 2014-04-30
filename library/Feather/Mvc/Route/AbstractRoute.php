<?php

namespace Feather\Mvc\Route;

use Feather\Mvc\Http\Request;

abstract class AbstractRoute {
    
    const ACTION_SUFFIX = "Action";
    
    const CONTROLLER_SUFFIX = "Controller";

    //cached controller class name
    private $_controllerClassName = "";

    //cached action method name
    private $_actionMethodName = "";

    //cached params
    private $_params = array();

    /*
    * Get the matched controller class name
    *
    * @return string 
    */
    public function getControllerClassName() {
        return $this->_controllerClassName;
    }

    /*
    * Set the matched controller class name
    *
    * @param string $controller Class name
    * @return
    */
    public function setControllerClassName($controller) {
        $controller = (string) $controller;
        $this->_controllerClassName = $controller;

        return;
    }

    /*
    * Get the matched action method name
    *
    * @return string 
    */
    public function getActionMethodName() {
        return $this->_actionMethodName;
    }

    /*
    * Set the matched action method name
    *
    * @param string $action method name
    * @return
    */
    public function setActionMethodName($action) {
        $action = (string) $action;
        $this->_actionMethodName = $action;

        return;
    }

    /*
    * Get all params
    *
    * @return array
    */
    public function getParams() {
        return $this->_params;
    }

    /*
    * set a variable
    *
    * @param string $key Name of the variable
    * @param string $value Value of the variable
    * @return
    */
    public function setParam($key, $value) {
        $key = (string) $key;
        $this->_params[$key] = $value;

        return;
    }

    /*
    * match the request uri to a specific combination of a controller and a action and several params
    *
    * @param Feather\Mvc\Http\Request Http Request Object
    * @return bool
    */
    abstract public function match(Request $request);

}// END OF CLASS
