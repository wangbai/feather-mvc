<?php

namespace Feather\Mvc\Application;

use Feather\Mvc\Application\Request;
use Feather\Mvc\Application\Response;

class Application {

    const CLASSFILE_SUFFIX = '.php';

    const TEMPLATEFILE_SUFFIX = '.tpl';

    const APPLICATION_DIR = 'application/';

    const CONFIG_DIR = 'config/';

    const CONFIG_FILE = 'config.ini.php';
 
    const CONTROLLER_DIR = 'controller/';

    const TEMPLATE_DIR = 'template/';

    protected $_request = null;

    protected $_response = null;
   
    private $_route = null;

    private $_template = null;

    private $_basePath = null;   

    public function __construct($basePath) {
        $this->_basePath = rtrim($basePath, "/")."/";

        //init
        $this->init();
    }

    public function init() {
        //load application config
        $appConfigFile = $this->_basePath.self::CONFIG_DIR.self::CONFIG_FILE;     
        if (!file_exists($appConfigFile)) {
            throw new Application\Exception("Config:".$appConfigFile." doesn't exist");           
        }
        $appConfig = include($appConfigFile);

        //get route type
        if (isset($appConfig['route_type']) 
                && !empty($appConfig['route_type'])) {
            $route_type = trim($appConfig['route_type']);
        } else {
            $route_type = "simple";
        }

        //get template type
        if (isset($appConfig['template_type'])
                && !empty($appConfig['template_type'])) {
            $template_type = trim($appConfig['template_type']);
        } else {
            $template_type = "simple";
        }

        //define input and output
        $this->_request = new Request();
        $this->_response = new Response();

        //the first config module is the default module 
        $this->_router = new Lm_Router($this->_configModules[0]);
    }

    /*
    * start the application
    */
    public function run() {
        $response = $this->_response;

        try {
            $this->_dispatch();
        } catch(Lm_Application_Exception $e) {
            $response->setHttpCode(Lm_Application_Http::ERROR_NOT_FOUND);
            $response->setBody($e->getMessage());
        } catch(Exception $e) {
            $response->setHttpCode(Lm_Application_Http::FATAL_SERVER_ERROR);
            $response->setBody($e->getMessage());
        }
 
        $response->output();
        return;
    }

    private function _dispatch() {
        $route = $this->_router->parse($this->_request->getRequestURI());

        //check module  
        $this->_checkModule($route);
   
        //execute handler
        $controller = $this->_loadController($route);
        $action = $route->getActionMethodName();
    
        if (!method_exists($controller, $action)) {
            throw new Lm_Application_NoAction("The action:".$action." doesn't exist in ".get_class($controller));
        }

        $response = $controller->__call($action, null);

        //handle error
        if ($response->isError()) {
            throw $response->getException();
        }

        //handle template
        if ($response->getNeedTemplate()) {
            $templateName = $response->getTemplateName();
            if (!empty($templateName)) {
                $route->setAction($templateName);
            }
            $this->_loadTemplate($route);
        }
    }

    private function _checkModule($route) {
        $module = $route->getModule();

        //check module
        if (!in_array($module, $this->_configModules)) {
            throw new Lm_Application_NoModule("The module:".$module." hasn't been configured");
        }

        $moduleDir = $this->_getModuleDirPath($route);
        if (!is_dir($moduleDir)) {
            throw new Lm_Application_NoModule("The module dir:".$moduleDir." does't exist");
        }
    }

    private function _getModuleDirPath($route) {
        $module = $route->getModule();
        return $this->_basePath.self::APPLICATION_DIR.self::MODULE_DIR.$module."/";
    }

    private function _loadController($route) {
        $moduleDir = $this->_getModuleDirPath($route);

        $controllerClass = $route->getControllerClassName();
        $controllerFile = $moduleDir.self::CONTROLLER_DIR.$controllerClass.self::CLASSFILE_SUFFIX;
        if (!file_exists($controllerFile)) {
            throw new Lm_Application_NoController("The controller source:".$controllerFile." doesn't exist");
        }
        
        require ($controllerFile);
        if (!class_exists($controllerClass)) {
            throw new Lm_Application_NoController("The controller class:".$controllerClass." doesn't exist");
        }

        $conObj = new $controllerClass($this->_request, $this->_response); 
        return $conObj;
    }

    private function _loadTemplate($route) {
        $moduleDir = $this->_getModuleDirPath($route);
        
        $controller = $route->getController();
        $action = $route->getAction();

        $templateFile = $moduleDir.self::TEMPLATE_DIR.$controller."/".$action.self::TEMPLATEFILE_SUFFIX;

        $template = new Lm_Template_Base($templateFile, $this->_response);
        $template->load();
        return;
    }

}//END OF CLASS
