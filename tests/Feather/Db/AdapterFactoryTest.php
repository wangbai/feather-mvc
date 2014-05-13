<?php

namespace Feather\Db;

use Feather\Util\Registry;

class MysqliAdapterTest extends \PHPUnit_Framework_TestCase {

    public function testPorduceOnlyOneMysqli() {
        $config = array(
            'host'       => '10.0.11.224',
            'user'       => 'yongche',
            'password'   => '',
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        );
        
        $dbType = 'mysqli';
        AdapterFactory::getAdapter($config, $dbType);
        
        $config_1 = $config;
        $config_1['port'] = 3306;
        AdapterFactory::getAdapter($config_1, $dbType);        

        $num = count(Registry::getAll());
        $this->assertEquals($num, 1);
    }

    public function testPorduceMultiMysqli() {
        $config = array(
            'host'       => '10.0.11.224',
            'user'       => 'yongche',
            'password'   => '',
            'database'   => 'feather_test',
            'charset'    => 'utf8'
        );
        
        $dbType = 'mysqli';
        AdapterFactory::getAdapter($config, $dbType);
        
        $config_1 = $config;
        $config_1['database'] = 'test';
        AdapterFactory::getAdapter($config_1, $dbType);        

        $num = count(Registry::getAll());
        $this->assertEquals($num, 2);
    }

}// END OF CLASS
