<?php

namespace Feather\Mvc\Dispatcher;

class ExceptionTest extends \PHPUnit_Framework_TestCase {

    public function testNewException() {
        $this->setExpectedException(
          'Feather\Mvc\Dispatcher\Exception'
        );

        throw new Exception("Bad Request");
    }

}// END OF CLASS
