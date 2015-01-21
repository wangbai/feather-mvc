<?php
#########################################################################
# File Name: Feather/Config.php
# Desc: 
# Author: wangshaolin
# mail: wangshaolin@yongche.com
# Created Time: 2015年01月20日 星期二 14时39分04秒
#########################################################################

namespace Feather;

class Config{

    private $_data;

    public function __construct(array $config){
        $this->_data = array();
        foreach($config as $key => $val){
            if(is_array($val)){
                $this->_data[$key] = new self($val); 
            }else{
                $this->_data[$key] = $val;
            }
        }
    }

    public function get($name, $default = null){
        if(isset($this->_data[$name])){
            return $this->_data[$name];
        }

        return $default;
    } 

    public function __get($name){
        return $this->get($name);
    }

    public function __set($name, $val){
        return $this->set($name, $val);
    }

    public function set($name, $val){
        if(is_array($val)){
            $this->_data[$name] = new self($val);
        }else{
            $this->_data[$name] = $val;
        }
    }


    public function toArray(){
        $array = array();
        $data = $this->_data;
        foreach ($data as $key => $value) {
            if ($value instanceof \Feather\Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    
    }
}
