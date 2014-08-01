<?php

namespace Feather\Db;

class MysqliAdapterTest extends \PHPUnit_Framework_TestCase {

    private $_mysqlAdapter = null;

    public function testConnectSucceed() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
    }

    public function testConnectFailed() {
        $config = array(
            'host'       => '10.0.11.1',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->setExpectedException(
          'Feather\Db\Exception'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
    }

    public function testQuery() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $sql = 'select * from person where id = 1';
        $result = $this->_mysqlAdapter->query($sql);
    }

    public function testSecureQuery() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
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
            'username'       => 'yongche',
            'password'   => '', 
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        ); 
        $adapter1 = new MysqliAdapter($config);
        
        $config1 = $config;
        $config1['dbname'] = 'test';
        $adapter2 = new MysqliAdapter($config1);
     
        $this->assertNotEquals((string)$adapter1, (string)$adapter2);       
    }

    public function testInsert() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $sql = 'insert into person set name = ? , age = ?;';
        $params = array('Jane', 100);
        $result = $this->_mysqlAdapter->secureQuery($sql, $params);
    }

    public function testInsert2() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $sql = 'insert into person set id = ?, name = ? , age = ?;';
        $params = array(1000, 'Jane', 100);
        $result = $this->_mysqlAdapter->secureQuery($sql, $params);
        $this->assertEquals(1000, $result);
    }

    public function testUpdate() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );

        $this->_mysqlAdapter = new MysqliAdapter($config);
        $sql = 'update person set age = ? where name = ?;';
        $params = array(100, 'Jane');
        $result = $this->_mysqlAdapter->secureQuery($sql, $params);
    }
    
}// END OF CLASS
