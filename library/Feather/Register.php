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
        $this->_data[$name] = $value;
    }

    static public function get($name, $default = null){
        if(isset($this->_data[$name])){
            return $this->_data[$name];
        }
        return $default;
    }
}
