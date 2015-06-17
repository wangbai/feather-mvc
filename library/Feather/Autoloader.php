<?php

namespace Feather;

/**
* An autoloader conforms to PSR-0 
*/
class Autoloader {

    private static $_LOADER;    

    public static function getInstance() {    
        if (self::$_LOADER == NULL) {
            self::$_LOADER = new self;
        }
            
        return self::$_LOADER;    
    }

    private function __construct() {
    }

    public function init() {
        self::register();
        return;
    }

    public function register() {
        spl_autoload_register(array($this, "_autoload"));
        return;
    }

    private $_namespaces = array();
    public function setNamespaces(array $arr){
        foreach($arr as $key => $path){
            $key = trim($this->_spacename($key), '\\');
            $this->_namespaces[$key] = $path;
        }
        return $this;
    }

    private function _spacename($name){
        if($name == str_replace('\\\\', '\\', $name)){
            return $name;
        }else{
            return $this->_spacename($name);
        }
    }

    private function _autoload($className) {
        $fileName  = '';
        $namespace = '';

        $className = $this->_spacename($className);
        foreach($this->_namespaces as $key => $path){
            $pos = stripos($className, $key);
            if($pos === 0){
                $fileName = $path . str_replace('\\', '/', substr($className, strlen($key))) . '.php';
                include $fileName;
                return;
            }
        }

        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $paths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $path) {           
            $combined = $path.DIRECTORY_SEPARATOR.$fileName;
            if (is_file($combined)) {       
                include $combined;
                return;
            }
        }
    }

}// END OF CLASS
