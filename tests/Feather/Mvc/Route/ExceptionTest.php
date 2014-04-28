<?php

namespace Feather\Mvc\Route;

class ExceptionTest extends \PHPUnit_Framework_TestCase {

    public function testNewException() {
        $this->setExpectedException(
          'Feather\Mvc\Route\Exception'
        );
        throw new Exception("Bad Request", 200);
    }

}// END OF CLASS
