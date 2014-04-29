<?php

namespace Feather;

/*
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

    private function __contruct() {
    }

    public function init() {
        spl_autoload_register(array($this, "autoload"));
        return;
    }

    private function autoload($className) {
        $fileName  = '';
        $namespace = '';

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
