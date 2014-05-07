<?php

namespace \Feather\Db;

abstract class MysqliAdapter {
    
    abstract public function connect() {
    }

    abstract public function close();

    abstract protected function query($sql);

    abstract protected function _secureQuery($sql, $param);

    abstract protected function _throwDbException();

    abstract public function affectedRowsNum();

    abstract public function beginTransaction();

    abstract public function commit();

    abstract public function rollBack();

}// END OF CLASS
