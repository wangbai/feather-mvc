<?php

namespace \Feather\Db;

abstract class AbstractAdapter {
    
    //default configuration for the db connection
    private $_config = array(
        'host'       => '',
        'port'       => 3306,
        'user'       => '',
        'password'   => '',
        'database'   => '',
        'charset'    => 'utf8',
        'persistent' => false,
        'options'    => array()
    );

    //db connection
    protected $_connection = null;

    public function __construct($config) {
        $this->_config += $config;
    }

    public function secureQuery($sql, $param = array()) {
        $this->connect();
        
        $result = $this->_secureQuery($sql, $param);
        if (!$result) {
            return $result;
        }

        $this->_throwDbException();
    }

    abstract public function connect();

    abstract public function close();

    abstract protected function query($sql);

    abstract protected function _secureQuery($sql, $param);

    abstract protected function _throwDbException();

    abstract public function affectedRowsNum();

    abstract public function beginTransaction();

    abstract public function commit();

    abstract public function rollBack();

}// END OF CLASS
