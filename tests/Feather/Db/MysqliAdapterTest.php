<?php

namespace Feather\Db;

class MysqliAdapterTest extends \PHPUnit_Framework_TestCase {

    private $_mysqlAdapter = null;

    public function testConnectSucceed() {
        $config = array(
            'host'       => '10.0.11.224',
            'user'       => 'yongche',
            'password'   => '',
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $this->_mysqlAdapter->connect();
    }

    public function testConnectFailed() {
        $config = array(
            'host'       => '10.0.11.1',
            'user'       => 'yongche',
            'password'   => '',
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->setExpectedException(
          'Feather\Db\Exception'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $this->_mysqlAdapter->connect();
    }

    public function testQuery() {
        $config = array(
            'host'       => '10.0.11.224',
            'user'       => 'yongche',
            'password'   => '',
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $sql = 'select * from person where id = 1';
        $result = $this->_mysqlAdapter->query($sql);
    }

    public function testSecureQuery() {
        $config = array(
            'host'       => '10.0.11.224',
            'user'       => 'yongche',
            'password'   => '',
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $sql = 'select * from person where name = ? and age = ?';
        $params = array('wangbai', 1, 'haha');
        $result = $this->_mysqlAdapter->secureQuery($sql, $params);
    }

}// END OF CLASS
