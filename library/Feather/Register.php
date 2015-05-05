<?php
#########################################################################
# File Name: Feather/Register.php
# Desc: 
# Author: wangshaolin
# mail: wangshaolin@yongche.com
# Created Time: 2015年01月20日 星期二 15时03分25秒
#########################################################################

namespace Feather;

class Register{

    static private $_data = array();

    static public function set($name, $value){
        self::$_data[$name] = $value;
    }

    static public function get($name, $default = null){
        if(isset(self::$_data[$name])){
            $value = self::$_data[$name];
            if(is_object($value) && get_class($value) === 'Closure'){
                self::$_data[$name] = $value();
            }
            return self::$_data[$name];
        }
        return $default;
    }
}
