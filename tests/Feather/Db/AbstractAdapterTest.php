<?php

namespace Feather\Db;

class AbstractAdapterTest extends \PHPUnit_Framework_TestCase {

    public function testOperationExtract() {
        $sql = 'Select * from person;';
        $operation = AbstractAdapter::operationExtract($sql);

        $this->assertEquals("select", $operation); 
    }

}// END OF CLASS
