<?php
#########################################################################
# File Name: AbstractTableTest.php
# Desc: 
# Author: liufeng
# mail: liufeng1@yongche.com
# Created Time: 2014年09月25日 星期四 18时34分36秒
#########################################################################
namespace Feather\Table;

class MysqlTableTest extends \PHPUnit_Framework_TestCase {

    private $_mysqlAdapter = null;
    private $_person = null;

    protected function setUp() {
        require_once(__DIR__.'/../../../application/bootstrap.php');
        require_once("Person.php");
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );
        $this->_mysqlAdapter = new \Feather\Table\Mysqli($config);
        $this->_person = new Person($config);
    }

    public function testGet(){
    
        $this->_person->where("age",11);
        $res = $this->_person->get(null,array('name','id'));

        echo var_export($res,true);
    }
    /**
    public function testQuery(){
        $sql = "select * from person where id = 1";
        $params = array(1);
        $res = $this->_mysqlAdapter->query($sql);
    }

    public function testInsert(){
        $data = array(
            'name' => "wangwu",
        );
        $res = $this->_person->insert($data);
        echo $res;
    }

    public function testUpdate(){
        $data = array(
            'name' => "lisi",
        );
        $this->_person->where('id',1144);
        $res = $this->_person->update($data);
        echo $this->_person->count;
    }

    public function testDelete(){
        $this->_person->where('id',1144);
        $res = $this->_person->delete();
        echo $res;
    }

    public function testOrder(){
        $this->_person->where('age',11)->orderBy("id","desc");
        $res = $this->_person->get();
        echo var_export($res,true);
    }
    public function testGroup(){
        $this->_person->groupBy("age");
        $res = $this->_person->get();
        echo var_export($res,true);
    }

    public function testError(){
    
        echo $this->_person->getLastError();
    }
    */
}
