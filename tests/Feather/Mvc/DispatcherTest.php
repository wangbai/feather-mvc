<?php

namespace Feather\Mvc;

class DispatcherTest extends \PHPUnit_Framework_TestCase {

    private $_baseDir;
    private $_route;
    private $_template;
    private $_request;
    private $_response;
    private $_dispatcher;

    protected function setUp() {
        $this->_baseDir = realpath(dirname(__FILE__))."/Controller/controller";
        $this->_route = $this->getMock('Feather\Mvc\Route\Simple');
        $this->_request = $this->getMock('Feather\Mvc\Http\Request');
        $this->_response = $this->getMock('Feather\Mvc\Http\Response');
        $this->_template = $this->getMock('Feather\Mvc\Template\Simple', null, array("", $this->_request, $this->_response));
        $this->_dispatcher = new Dispatcher($this->_baseDir, $this->_route, $this->_template, $this->_request, $this->_response);
    }

    public function testRouteSucceed() {
        $this->_route->expects($this->once())
             ->method('match')
             ->will($this->returnValue(true));

        $this->_route->expects($this->once())
             ->method('getControllerClassName')
             ->will($this->returnValue('UserController'));

        $this->_route->expects($this->once())
             ->method('getActionMethodName')
             ->will($this->returnValue('getListAction'));

        $this->_dispatcher->run();
    }

    public function testControllerException() {
        $this->_route->expects($this->once())
             ->method('match')
             ->will($this->returnValue(true));

        $this->_route->expects($this->once())
             ->method('getControllerClassName')
             ->will($this->returnValue('UserController'));

        $this->_route->expects($this->once())
             ->method('getActionMethodName')
             ->will($this->returnValue('getListAction'));

        $this->_response->expects($this->once())
             ->method('isExceptional')
             ->will($this->returnValue(true));

        $this->_response->expects($this->once())
             ->method('getException')
             ->will($this->returnValue(new \Exception('Haha')));

        $this->setExpectedException(
          '\Exception', 'Haha'
        );

        $this->_dispatcher->run();   
    }

    public function testRouteFailed() {
        $this->_route->expects($this->once())
             ->method('match')
             ->will($this->returnValue(false));

        $this->setExpectedException(
          'Feather\Mvc\Dispatcher\Exception'
        );

        $this->_dispatcher->run();
    }

    public function testNotFoundController() {
        $this->_route->expects($this->once())
             ->method('match')
             ->will($this->returnValue(true));

        $this->_route->expects($this->once())
             ->method('getControllerClassName')
             ->will($this->returnValue('CustomerController'));

        $this->_route->expects($this->once())
             ->method('getActionMethodName')
             ->will($this->returnValue('getListAction'));

        $this->setExpectedException(
          'Feather\Mvc\Dispatcher\Exception'
        );

        $this->_dispatcher->run();

    }

}// END OF CLASS
