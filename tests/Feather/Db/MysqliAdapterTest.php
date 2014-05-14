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

    public function testAdapterToString() {
        $config = array(
            'host'       => '10.0.11.224',
            'user'       => 'yongche',
            'password'   => '', 
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        ); 
        $adapter1 = new MysqliAdapter($config);
        
        $config1 = $config;
        $config1['database'] = 'test';
        $adapter2 = new MysqliAdapter($config1);
     
        $this->assertNotEquals((string)$adapter1, (string)$adapter2);       
    }
}// END OF CLASS
