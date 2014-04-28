<?php

namespace Feather\Mvc;

use Feather\Mvc\Route\AbstractRoute;

use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;
use Feather\Mvc\Http\Common;

class Dispatcher {
    
    private $_controllerDirectory = "";

    private $_templateDirectory = "";

    /*
    * Set the root directory of all the controllers
    *
    * @param string $dir
    * @return
    */
    public function setControllerDirectory($dir) {
        $dir = (string) $dir;
        $this->_controllerDirectory = $dir;
    }

    /*
    * Get the root directory of all the controllers
    *
    * @return
    */
    public function getControllerDirectory() {
        return $this->_controllerDirectory;
    }

    /*
    * Set the root directory of all the templates
    *
    * @param string $dir
    * @return
    */
    public function setTemplateDirectory($dir) {
        $dir = (string) $dir;
        $this->_templateDirectory = $dir;
    }

    /*
    * Get the root directory of all the templates
    *
    * @return
    */
    public function getTemplateDirectory() {
        return $this->_templateDirectory;
    }

    /*
    * start to run application
    */
    public function run(Request $request, Response $response) {
        try {
            $this->_dispatch($request, $response);
        } catch(Exception $e) {
            $response->setHttpCode($e->getCode());
            $response->setBody($e->getMessage());
        }

        $response->output();
        return;
    }

    public function dispatch() {
       
    }

    public function loadClassByRoute(AbstractRoute $route) {
        $controllerName = $route->getControllerClassName();

        $controllerFile = str_replace(DIRECTORY_SEPARATOR, "\\", $controller).".php";
        if (!file_exists($controllerFile)) {
            throw new Dispatcher\NoController("The controller source:".$controllerFile." doesn't exist");   
        }

        include($controllerFile);

        if (!class_exists($)) {
            throw new Dispatcher\NoController("The controller class:".$controllerClass." doesn't exist");
        }

        $conObj = new $controllerName($this->_request, $this->_response);
        return $conObj;       
    }

    public function loadTemplateByRoute(AbstractRoute $route) {
        $controllerName = $route->getControllerClassName();
    }

    public function loadTemplateByPath($path) {
    }

}// END OF CLASS
