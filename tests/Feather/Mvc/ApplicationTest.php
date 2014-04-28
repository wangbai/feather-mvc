<?php

namespace Feather\Mvc;

class ApplicationTest extends \PHPUnit_Framework_TestCase {

    private $_app;

    protected function setUp() {
        $this->_app = new Application("./");
    }

    public function testDefault() {
    }

}// END OF CLASS
