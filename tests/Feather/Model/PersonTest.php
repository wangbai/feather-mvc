<?php

namespace Feather\Db;

require "Person.php";

use Feather\Model\Person;

class PersonTest extends \PHPUnit_Framework_TestCase {

    private $_mysqlAdapter = null;
    private $_personModel = null;

    protected function setUp() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );
        $this->_mysqlAdapter = new MysqliAdapter($config);
        $this->_personModel = new Person($this->_mysqlAdapter);
    }

    public function testFindById() {
        $result = $this->_personModel->findById(1);
        var_dump($result);
    }

    public function testInsert() {
        $data = array(
            "name" => "wow",
            "age" => 50,
        );
        $result = $this->_personModel->insert($data);
        var_dump($result);
    }

    public function testUpdate() {
        $data = array(
            "name" => "wow",
            "age" => 50,
        );
        $result = $this->_personModel->updateById($data, 1);
        var_dump($result);
    }

    public function testDelete() {
        $result = $this->_personModel->deleteById(1000);
        var_dump($result);
    }
}// END OF CLASS
