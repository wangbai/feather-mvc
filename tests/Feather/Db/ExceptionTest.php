<?php

namespace Feather\Db;

class ExceptionTest extends \PHPUnit_Framework_TestCase {

    public function testNewException() {
        $this->setExpectedException(
          'Feather\Db\Exception'
        );
        throw new Exception("Connection failed");
    }

}// END OF CLASS
