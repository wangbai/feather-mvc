<?php

namespace Feather\Mvc\Route;

use Feather\Mvc\Http\Request;

class Simple extends AbstractRoute {
    
    const DEFAULT_CONTROLLER = "Index";

    const DEFAULT_ACTION = "index";    

    public function match(Request $request) {
        $uri = $request->getRequestURI();

        $uri = ltrim($uri, DIRECTORY_SEPARATOR);
        $delimiterPos = strrpos($uri, DIRECTORY_SEPARATOR);

        if ($delimiterPos === false) {
            $action = $uri;
            $controller = "";
        } else {
            $action = substr($uri, $delimiterPos + 1);
            $controller = substr($uri, 0, $delimiterPos);
        }
        
        //normalize $controller
        $controller = $this->_normalizeController($controller);    
        $action = $this->_normalizeAction($action);    
    
        $this->setControllerClassName($controller);
        $this->setActionMethodName($action);

        return true;
    }

    private function _normalizeController($controller) {
        $controller = strtolower($controller);
        if (empty($controller)) {
            return self::DEFAULT_CONTROLLER.self::CONTROLLER_SUFFIX;
        }

        $controller = str_replace('_', DIRECTORY_SEPARATOR, $controller);
        $parts = explode(DIRECTORY_SEPARATOR, $controller);

        $clazzParts = array();
        foreach ($parts as $p) {
            $clazzParts[] = ucfirst($p);
        }
        
        return implode("\\", $clazzParts).self::CONTROLLER_SUFFIX;
    }    

    private function _normalizeAction($action) {
        $action = strtolower($action);
        $action = explode(".", $action);
        $action = $action[0];

        if (empty($action)) {
            return self::DEFAULT_ACTION.self::ACTION_SUFFIX;
        }
        
        $parts = explode('_', $action);
        $actionNor = array_shift($parts);

        foreach ($parts as $p) {
            $actionNor .= ucfirst($p);
        }

        return $actionNor.self::ACTION_SUFFIX;
    }

}// END OF CLASS
