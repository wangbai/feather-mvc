<?php

namespace \Feather\Db;

abstract class MysqliAdapter {

    public function connect() {
        if (!empty($this->_connection)) {
            return;
        }

        if (!extension_loaded('mysqli')) {
            throw new Exception('No mysqli extension installed');
        }

        $this->_connection = mysqli_init();

        $config = $this->_config;
        $host = $config['host'];
        $port = $config['port'];
        $user = $config['user'];
        $password = $config['password'];
        $database = $config['database'];
        $charset = $config['charset'];

        $ret = mysqli_real_connect($host, $user, $password, $database, $port);
        if (!$ret) {
            $this->_throwDbException();
        }

        $ret = $this->_connection->set_charset($charset);
        if (!$ret) {
            $this->_throwDbException();
        }

        return;
    }

    public function close() {
        return $this->_connection->close();
    }

    protected function escape($string) {
        return $this->_connection->real_escape_string($string);
    }
    protected function _query($sql) {
        return $this->_connection->query($sql);
    }

    protected function _throwDbException() {
        if ($this->_connection) {
            throw new Exception($this->_connection->error,
                            $this->_connection->errno);
        } else {
            throw new Exception($this->_connection->connect_error,
                            $this->_connection->connect_errno);
        }
    }

    public function affectedRowsNum() {
        return $this->_connection->affected_rows;
    }

    public function beginTransaction() {
        $ret = $this->_connection->begin_transaction();
        if (!$ret) {
            throw new Exception('Begin transaction failed');       
        }
    }

    public function commit() {
        $ret = $this->_connection->commit();
        if (!$ret) {
            throw new Exception('Transaction commit failed');
        }
    }

    public function rollback() {
        $ret = $this->_connection->rollback();
        if (!$ret) {
            throw new Exception('Transaction rollback failed');
        }
    }
  
}// END OF CLASS
