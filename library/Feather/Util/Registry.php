<?php

namespace Feather\Util;

class Registry {
 
    private static $instances = array();

    private function construct() {
    }

    private function __clone() {
    }

    /**
    * Get all regstered variables
    *
    * @return array
    */
    public static function getAll() {
        return self::$instances;
    }

    /**
    * Get a regstered variable by a key
    *
    * @param string $key
    * @return mixed
    */
    public static function get($key) {
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }
        return false;
    }

    /** 
    * Set a regstered variable by a key
    *
    * @param string $key
    * @param mixed $value
    * @return
    */
    public static function set($key, $value = null) {
        if (!empty($key) 
                && !empty($value)) {
            self::$instances[$key] = $value;
        }
    }

    /** 
    * Clear a regstered variable by a key
    *
    * @param string $key
    * @return
    */
    public static function clear($key) {
        unset(self::$instances[$key]);
    }   

}// END OF CLASS
