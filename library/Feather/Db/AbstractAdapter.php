<?php

namespace \Feather\Db;

abstract class AbstractAdapter {
    
    const FIELD_REPLACER = '?';
       
    //default configuration for the db connection
    protected $_config = array(
        'host'       => '',
        'port'       => 3306,
        'user'       => '',
        'password'   => '',
        'database'   => '',
        'charset'    => 'utf8'
    );

    //db connection
    protected $_connection = null;

    public function __construct($config) {
        $this->_config += $config;
    }

    public function query($sql) {
        $this->connect();

        $result = $this->_query($sql);
        if (!$result) {
            return $result;
        }

        $this->_throwDbException();

    }

    public function secureQuery($sql, $param = array()) {
        $this->connect();
        
        $finalSql = "";
        $remain = $sql;
        foreach ($param as $p) {
            $field = $this->escape($p);
            $replacePos = strpos($sql, FIELD_REPLACER);
            $finalSql .= substr($remain, 0, $replacePos)."'".$this->escape($p)."'";
            $remain =  substr($remain, $replacePos + 1);
        }
        $finalSql .= $remain;
            
        $result = $this->_query($sql);
        if (!$result) {
            return $result;
        }

        $this->_throwDbException();
    }


    abstract public function connect();

    abstract public function close();

    abstract protected function escape($string);

    abstract protected function _query($sql);

    abstract protected function _throwDbException();

    abstract public function affectedRowsNum();

    abstract public function beginTransaction();

    abstract public function commit();

    abstract public function rollback();

}// END OF CLASS
