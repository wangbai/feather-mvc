<?php

namespace Feather\Mvc\Application;

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
        $this->buildDispatcher();

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
        $template = null;
        switch($templateType) {
        case "simple":
        default:
            $template = new Template\Simple;
        }

        return $template;
    }

    public function buildDispatcher() {
        $controllerBaseDir = $this->_basePath.self::APPLICATION_DIR.self::CONTROLLER_DIR;
        $templateBaseDir = $this->_basePath.self::APPLICATION_DIR.self::TEMPLATE_DIR;

        $this->_dispatcher = new Dispatcher; 
        $this->_dispatcher->setControllerDirectory($controllerBaseDir);        
        $this->_dispatcher->setTemplateDirectory($templateBaseDir);        
        $this->_dispatcher->setRoute($this->_route);
        $this->_dispatcher->setTemplate($this->_template);

        return $this->_dispatcher;
    }

    /*
    * start to run application
    */
    public function run(Request $request, Response $response) {
        try {
            $this->_dispatch($request, $response);
        } catch(Feature\Mvc\Exception $e) {
            $response->setHttpCode($e->getCode());
            $response->setBody($e->getMessage());
        } catch(Exception $e) {
            $response->setHttpCode(Common::SC_INTERNAL_SERVER_ERROR);
            $response->setBody($e->getMessage());
        }

        $response->output();
        return;
    }

}//END OF CLASS
