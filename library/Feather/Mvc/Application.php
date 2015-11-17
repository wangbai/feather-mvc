<?php

namespace Feather\Mvc;

use Feather\Mvc\Http\Common;
use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;

use Feather\Mvc\Route;
use Feather\Mvc\Template;
use Feather\Mvc\Dispatcher;

class Application {

    const APPLICATION_DIR = 'application/';

    const CONFIG_DIR = 'config/';

    const CONFIG_FILE = 'config.ini.php';
 
    const CONTROLLER_DIR = 'controller/';

    const TEMPLATE_DIR = 'template/';

    protected $_request = null;

    protected $_response = null;
   
    protected $_route = null;

    protected $_template = null;

    protected $_dispatcher = null;

    protected $_basePath = null;   

    public function __construct($basePath) {
        $this->_basePath = rtrim($basePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    public function init() {
        //load application config
        $appConfigFile = $this->_basePath.self::CONFIG_DIR.self::CONFIG_FILE;     
        $appConfig = array();
        if (file_exists($appConfigFile)) {
            $appConfig = include($appConfigFile);
        }

        //get route type
        if (isset($appConfig['route_type']) 
                && !empty($appConfig['route_type'])) {
            $routeType = trim($appConfig['route_type']);
        } else {
            $routeType = "simple";
        }

        //get template type
        if (isset($appConfig['template_type'])
                && !empty($appConfig['template_type'])) {
            $templateType = trim($appConfig['template_type']);
        } else {
            $templateType = "simple";
        }

        //define input and output
        $this->_request = new Request();
        $this->_response = new Response();

        //build route and template
        $this->_route = $this->buildRoute($routeType);
        $this->_template = $this->buildTemplate($templateType);

        //build dispatcher
        $this->_dispatcher = $this->buildDispatcher();

        return $this;
    }

    public function buildRoute($routeType) {
        $route = null;
        switch($routeType) {
        case "simple":
        default:
            $route = new Route\Simple;
        }

        return $route;
    }

    public function buildTemplate($templateType) {
        $templateBaseDir = $this->_basePath.self::APPLICATION_DIR.self::TEMPLATE_DIR;

        $template = null;
        switch($templateType) {
        case "simple":
        default:
            $template = new Template\Simple($templateBaseDir, $this->_request, $this->_response);
        }

        return $template;
    }

    public function buildDispatcher() {
        $controllerBaseDir = $this->_basePath.self::APPLICATION_DIR.self::CONTROLLER_DIR;
        $dispatcher = new Dispatcher($controllerBaseDir, 
                                $this->_route, 
                                $this->_template, 
                                $this->_request, 
                                $this->_response); 

        return $dispatcher;
    }

    /**
    * start to run application
    */
    public function run() {
        try {
            $response = $this->_dispatcher->run($this->_request, $this->_response);
        } catch(\Feather\Mvc\Exception $e) {
            $this->_response->setHttpCode($e->getCode());
            $this->_response->setBody($e->getMessage());
        } catch(\Exception $e) {
            $this->_response->setHttpCode(Common::SC_INTERNAL_SERVER_ERROR);
            $this->_response->setBody($e->getMessage());
        }

        $this->_response->output();
        return;
    }

    /**
    * get dispatcher
    */
    public function getDispatcher(){
        return $this->_dispatcher;
    }

    public function getRequest(){
        return $this->_request;
    }

    public function getResponse(){
        return $this->_response;
    }

}//END OF CLASS
