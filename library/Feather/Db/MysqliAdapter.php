<?php

namespace Feather\Db;

class MysqliAdapter extends AbstractAdapter {

    public function connect() {
        if (!empty($this->_connection)) {
            return;
        }

        if (!extension_loaded('mysqli')) {
            throw new Exception('No mysqli extension installed');
        }

        $connection = mysqli_init();

        $config = $this->_config;
        $host = $config['host'];
        $port = $config['port'];
        $user = $config['username'];
        $password = $config['password'];
        $database = $config['dbname'];
        $charset = $config['charset'];

        $ret = @mysqli_real_connect($connection, $host, $user, $password, $database, $port);
        if (!$ret) {
            $this->_throwDbException();
        }

        $this->_connection = $connection;
        $ret = $this->_connection->set_charset($charset);
        if (!$ret) {
            $this->_connection = null;
            $this->_throwDbException();
        }

        return;
    }

    public function close() {
        return $this->_connection->close();
    }

    public function escape($str) {
        return $this->_connection->real_escape_string($str);
    }

    protected function _query($sql) {
        $result = $this->_connection->query($sql);        
       
        if($result === false){
            return false;
        }
        
        if (parent::operationExtract($sql) == 'insert') {
            return $this->_connection->insert_id;
        }

        if ($result === true) {
            return $result;
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    protected function _throwDbException() {
        if ($this->_connection) {
            throw new Exception($this->_connection->error,
                            $this->_connection->errno);
        } else {
            throw new Exception(mysqli_connect_error(),
                           mysqli_connect_errno());
        }
    }

    public function affectedRowsNum() {
        return $this->_connection->affected_rows;
    }

    public function beginTransaction() {
        $ret = $this->_connection->autocommit(false);
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
