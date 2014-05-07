<?php

namespace Feather\Mvc\Route;

use Feather\Mvc\Http\Request;

abstract class AbstractRoute {
 
    const ACTION_SUFFIX = "Action";
    
    const CONTROLLER_SUFFIX = "Controller";

    //cached action name
    private $_actionName = "";

    //cached controller name
    private $_controllerName = "";

    //cached params
    private $_params = array();

    /*
    * Get the controller name
    *
    * @return string
    */
    public function getControllerName() {
        return $this->_controllerName;
    }

    /*
    * Set the controller name
    *
    * @param string $controllerName Name of the controller
    * @return
    */
    public function setControllerName($controllerName) {
        $controllerName = (string) $controllerName;

        $this->_controllerName = $controllerName;
        return;
    }

    /*
    * Get the action name
    *
    * @return string
    */
    public function getActionName() {
        return $this->_actionName;
    }

    /*
    * Set the action name
    *
    * @param string $actionName Name of the action
    * @return
    */
    public function setActionName($actionName) {
        $actionName = (string) $actionName;

        $this->_actionName = $actionName;
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
    * Get the matched controller class name
    *
    * @return string 
    */
    public function getControllerClassName() {
        return $this->_controllerName.self::CONTROLLER_SUFFIX;
    }

    /*
    * Get the matched action method name
    *
    * @return string 
    */
    public function getActionMethodName() {
        return $this->_actionName.self::ACTION_SUFFIX;
    }

    /*
    * match the request uri to a specific combination of a controller and a action and several params
    *
    * @param Feather\Mvc\Http\Request Http Request Object
    * @return bool
    */
    abstract public function match(Request $request);

}// END OF CLASS
