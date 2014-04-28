<?php

namespace Feather\Mvc\Template;

class DispatcherTest extends \PHPUnit_Framework_TestCase {

    private $_request;
    private $_response;

    protected function setUp() {
        $this->_request = $this->getMock('Feather\Mvc\Http\Request');
        $this->_response = $this->getMock('Feather\Mvc\Http\Response');
    }

    public function testDefault() {
    }

}// END OF CLASS
