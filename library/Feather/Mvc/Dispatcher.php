<?php

namespace Feather\Mvc;

use Feather\Mvc\Route\AbstractRoute;
use Feather\Mvc\Template\AbstractTemplate;

use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;
use Feather\Mvc\Http\Common;

class Dispatcher {
    
    private $_controllerDirectory = "";

    private $_templateDirectory = "";

    private $_route = null;
    
    private $_template = null;

    /*
    * Get the root directory of all the controllers
    *
    * @return
    */
    public function getControllerDirectory() {
        return $this->_controllerDirectory;
    }

    /*
    * Set the root directory of all the controllers
    *
    * @param string $dir
    * @return
    */
    public function setControllerDirectory($dir) {
        $dir = (string) $dir;
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->_controllerDirectory = $dir;
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
    * Set the root directory of all the templates
    *
    * @param string $dir
    * @return
    */
    public function setTemplateDirectory($dir) {
        $dir = (string) $dir;
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->_templateDirectory = $dir;
    }

    /*
    * Get the associated route
    *
    * @return Feather\Mvc\Route\AbstractRoute 
    */
    public function getRoute() {
        return $this->_route;
    }

    /*
    * Set the associated route
    *
    * @param Feather\Mvc\Route\AbstractRoute $route
    * @return
    */
    public function setRoute(AbstractRoute $route) {
        $this->_route = $route;
        return;
    }

    /*
    * Get the associated template
    *
    * @return Feather\Mvc\Template\AbstractTemplate
    */
    public function getTemplate() {
        return $this->_template;
    }

    /*
    * Set the associated template
    *
    * @param Feather\Mvc\Route\AbstractTemplate template
    * @return
    */
    public function setTemplate(AbstractTemplate $template) {
        $this->_template = $template;
        return;
    }

    /*
    * run the application to handle the request
    *
    * @param Feather\Mvc\Http\Request $request 
    * @param Feather\Mvc\Http\Reponse $response
    * @return Feather\Mvc\Http\Reponse
    */
    public function run(Request $request, Response $response) {

        $isMatched = $route->match($request);
        if (!$isMatched) {
            throw new Dispatcher\Exception($request->getRequestURI()." is not Found", Common::SC_NOT_FOUND);
        }

        $response = $this->loadClass($route);
        //handle error
        if ($response->isError()) {
            throw $response->getException();
        }

        //handle template
        if ($response->getNeedTemplate()) {
            $templatePath = $response->getTemplatePath();
            if (!empty($templateName)) {
                $this->loadTemplate($route);
            } else {
                $this->loadTemplateByPath($templateName);
            }
        }

        return $response;
    }

    /*
    * load the controller and execute the action
    *
    * @param Feather\Mvc\Route\AbstractRoute $route
    * @return Feather\Mvc\Http\Reponse
    */
    public function loadClass($route) {
        $controller = $route->getControllerClassName();
        $action = $route->getActionMethodName();

        $controllerFile = $this->getControllerDirectory().str_replace(DIRECTORY_SEPARATOR, "\\", $controller).".php";
        if (!file_exists($controllerFile)) {
            throw new Dispatcher\Exception("The controller source:".$controllerFile." doesn't exist");   
        }

        include($controllerFile);

        if (!class_exists($controller)) {
            throw new Dispatcher\Exception("The controller class:".$controller." doesn't exist");
        }

        $conObj = new $controller($this->_request, $this->_response);
        return $conObj->__call($action, null);
    }

    /*
    * load the template file and generate the output body
    *
    * @param Feather\Mvc\Route\AbstractRoute $route
    * @return Feather\Mvc\Http\Reponse
    */
    public function loadTemplate($route) {
        $controller = $route->getControllerClassName();
        $action = $route->getActionMethodName();

        $templateFile = $this->setTemplateDirectory().str_replace(DIRECTORY_SEPARATOR, "\\", $controller);
        $templateFile .= DIRECTORY_SEPARATOR.$action.".tpl";

        return $this->loadTemplateByPath($templateFile);
    }

    /*
    * load the template file and generate the output body
    *
    * @param string $path
    * @return Feather\Mvc\Http\Reponse
    */
    public function loadTemplateByPath($path) {
        $template->setTemplateFilePath($path);
        $template->setResponse($this->getResponse());
        return $template->load();
    }

}// END OF CLASS
