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
        $res = ($this->_connection) ? @mysqli_close($this->_connection) : true;
        $this->_connection = $res ? null : $this->_connection;
        return $res;
    }

    public function escape($str) {
        return $this->_connection->real_escape_string($str);
    }

    protected function _query($sql) {
        $begin = microtime(true);
        $result = $this->_connection->query($sql);        
        $time = microtime(true) - $begin;
        if($time >= (defined("SLOW_QUERY_TIME") ? constant("SLOW_QUERY_TIME") : 2)) {
            $time = sprintf("%.3f", $time);
            $dt = date("Y-m-d H:i:s", $begin);
            $str = "====== $dt ======\n";
            if(isset($_SERVER['HTTP_X_FROM_HOST'])) {
                $str .= "FromHost: " . $_SERVER['HTTP_X_FROM_HOST'] . "\n";
            }
            if(isset($_SERVER['HTTP_X_FROM_URL'])) {
                $str .= "FromUrl: " . $_SERVER['HTTP_X_FROM_URL'] . "\n";
            }
            $str .= "Spend: $time s\n";
            $str .= "SQL: " . substr($sql, 0, 1024) . "\n";
            foreach(debug_backtrace() as $k => $r) {
                $str .= "$k: {$r['file']}:{$r['line']}\n";
            }
            $old = umask(0111);
            @file_put_contents("/var/log/httpd/slow_query.log", $str, FILE_APPEND);
            umask($old);
        }
       
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
        $this->_connection->autocommit(true);
    }

    public function rollback() {
        $ret = $this->_connection->rollback();
        if (!$ret) {
            throw new Exception('Transaction rollback failed');
        }
        $this->_connection->autocommit(true);
    }
}// END OF CLASS
