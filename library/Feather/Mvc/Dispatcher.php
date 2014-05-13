<?php

namespace Feather\Mvc;

use Feather\Mvc\Route\AbstractRoute;
use Feather\Mvc\Template\AbstractTemplate;

use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;
use Feather\Mvc\Http\Common;

class Dispatcher {
    
    private $_controllerDirectory = "";

    private $_route = null;
 
    private $_template = null;

    private $_request = null;

    private $_response = null;
 
    /**
    * Create a dispatcher instance
    *
    * @param string $controllerDirectory
    * @param Feather\Mvc\Route\AbstractRoute $route
    * @param Feather\Mvc\Template\AbstractTemplate $template
    * @param Feather\Mvc\Http\Request $request
    * @param Feather\Mvc\Http\Response $response
    */
    public function __construct($controllerDirectory, AbstractRoute $route, AbstractTemplate $template, Request $request, Response $response) {
        $this->setControllerDirectory($controllerDirectory);
        $this->setRoute($route);
        $this->setTemplate($template);
        $this->setRequest($request);
        $this->setResponse($response);
    }
  
    /**
    * Get the root directory of all the controllers
    *
    * @return string
    */
    public function getControllerDirectory() {
        return $this->_controllerDirectory;
    }

    /**
    * Set the root directory of all the controllers
    *
    * @param string $dir
    * @return
    */
    public function setControllerDirectory($dir) {
        $dir = (string) $dir;
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->_controllerDirectory = $dir;
        return;
    }

    /**
    * Get the associated route
    *
    * @return Feather\Mvc\Route\AbstractRoute 
    */
    public function getRoute() {
        return $this->_route;
    }

    /**
    * Set the associated route
    *
    * @param Feather\Mvc\Route\AbstractRoute $route
    * @return
    */
    public function setRoute(AbstractRoute $route) {
        $this->_route = $route;
        return;
    }

    /**
    * Get the associated template
    *
    * @return Feather\Mvc\Template\AbstractTemplate
    */
    public function getTemplate() {
        return $this->_template;
    }

    /**
    * Set the associated template
    *
    * @param Feather\Mvc\Route\AbstractTemplate $template
    * @return
    */
    public function setTemplate(AbstractTemplate $template) {
        $this->_template = $template;
        return;
    }

    /**
    * Get the request associated with the template
    *
    * @return Feather\Mvc\Http\Request
    */
    public function getRequest() {
        return $this->_request;
    }

    /**
    * Set the request associated with the template
    *
    * @param Feather\Mvc\Http\Request $request
    * @return
    */
    public function setRequest(Request $request) {
        $this->_request = $request;
        return;
    }

    /**
    * Get the response associated with the template
    *
    * @return Feather\Mvc\Http\Response
    */
    public function getResponse() {
        return $this->_response;
    }

    /**
    * Set the response associated with the template
    *
    * @param Feather\Mvc\Http\Response $response
    * @return
    */
    public function setResponse(Response $response) {
        $this->_response = $response;
        return;
    }

    /**
    * Run the action according to the request
    *
    * @return Feather\Mvc\Http\Reponse
    */
    public function run() {
        $route = $this->getRoute();
        $request = $this->getRequest();

        $isMatched = $route->match($request);
        if (!$isMatched) {
            throw new Dispatcher\Exception($request->getRequestURI()." is not Found", Common::SC_NOT_FOUND);
        }

        $controller = $route->getControllerClassName();
        $action = $route->getActionMethodName();

        $response = $this->_run($controller, $action);

        //handle error
        if ($response->isExceptional()) {
            throw $response->getException();
        }

        //handle template
        if ($response->isNeedTemplate()) {
            $templatePath = $response->getTemplatePath();
            if (empty($templatePath)) {
                $templatePath = str_replace('\\', DIRECTORY_SEPARATOR, $controller).DIRECTORY_SEPARATOR.$action.".tpl";
            }
            
            $content = $this->getTemplate()->render($templatePath);
            $response->setBody($content);
        }
        return $response;
    }

    /**
    * load the controller and run the action
    * 
    * @return Feather\Mvc\Http\Reponse
    */
    protected function _run($controller, $action) {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $controllerFile = $this->getControllerDirectory().str_replace('\\', DIRECTORY_SEPARATOR, $controller).".php";
        if (!file_exists($controllerFile)) {
            throw new Dispatcher\Exception("The controller source:".$controllerFile." doesn't exist");   
        }

        include_once($controllerFile);

        if (!class_exists($controller)) {
            throw new Dispatcher\Exception("The controller class:".$controller." doesn't exist");
        }
        $conObj = new $controller($request, $response);
        return $conObj->__call($action, null);
    }
    
}// END OF CLASS
