<?php

namespace Feather\Db;

use Feather\Util\Registry;

class AdapterFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testPorduceOnlyOneMysqli() {
        $config = array(
            'host'       => '10.0.11.224',
            'username'       => 'yongche',
            'password'   => '',
            'dbname'   => 'feather_test',
            'charset'    => 'utf8'
        );
        
        $dbType = 'mysqli';
        $adapter = AdapterFactory::getAdapter($config, $dbType);
        
        $this->assertInstanceOf('Feather\Db\MysqliAdapter', $adapter);
    }

}// END OF CLASS
