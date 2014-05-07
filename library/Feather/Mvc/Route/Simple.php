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
        $action = self::_normalizeAction($action);    
        $controller = self::_normalizeController($controller);    
    
        if (empty($controller) || empty($action)) {
            return false;
        }

        $this->setActionName($action);
        $this->setControllerName($controller);
        return true;
    }

    protected static function _normalizeController($controller) {
        $controller = strtolower($controller);
        if (empty($controller)) {
            return self::DEFAULT_CONTROLLER;
        }

        $controller = str_replace('_', DIRECTORY_SEPARATOR, $controller);
        $parts = explode(DIRECTORY_SEPARATOR, $controller);

        $clazzParts = array();
        foreach ($parts as $p) {
            if (!self::isCharFirst($p)) {
                return false;
            }
            $clazzParts[] = ucfirst($p);
        }

        return implode("\\", $clazzParts);
    }

    protected static function _normalizeAction($action) {
        $action = strtolower($action);
        $action = explode(".", $action);
        $action = $action[0];

        if (empty($action)) {
            return self::DEFAULT_ACTION;
        }

        $parts = explode('_', $action);
        $actionNor = array_shift($parts);

        foreach ($parts as $p) {
            if (!self::isCharFirst($p)) {
                return false;
            }
            $actionNor .= ucfirst($p);
        }

        return $actionNor;
    }

    protected static function isCharFirst($text) {
        $first = substr($text,0,1);
        return true === ctype_alpha($first);
    }

}// END OF CLASS
