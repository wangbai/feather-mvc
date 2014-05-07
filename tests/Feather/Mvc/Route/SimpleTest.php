<?php

namespace Feather\Mvc\Route;

class SimpleTest extends \PHPUnit_Framework_TestCase {

    private $_route;
    private $_request;

    protected function setUp() {
        $this->_route = new Simple;
        $this->_request = $this->getMock('Feather\Mvc\Http\Request');
    }

    public function testDefault() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("IndexController", $this->_route->getControllerClassName());
        $this->assertEquals("indexAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testNormal() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/example/list.php'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("ExampleController", $this->_route->getControllerClassName());
        $this->assertEquals("listAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testControllerUnderstore() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/example_user/comment_list.php'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("Example\\UserController", $this->_route->getControllerClassName());
        $this->assertEquals("commentListAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testOnlyController() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/example_user/'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("Example\\UserController", $this->_route->getControllerClassName());
        $this->assertEquals("indexAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testOnlyAction() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/comment_list.php'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("IndexController", $this->_route->getControllerClassName());
        $this->assertEquals("commentListAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testMultipartOfController() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/example/user/comment/rating_score/'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("Example\\User\\Comment\\Rating\\ScoreController", $this->_route->getControllerClassName());
        $this->assertEquals("indexAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testMultipartOfController2() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/example/user/comment/rating_score'));
        $isMatched = $this->_route->match($this->_request);

        $this->assertEquals("Example\\User\\CommentController", $this->_route->getControllerClassName());
        $this->assertEquals("ratingScoreAction", $this->_route->getActionMethodName());
        $this->assertEquals(array(), $this->_route->getParams());
        $this->assertEquals(true, $isMatched);
    }

    public function testWrongController() {
        $this->_request->expects($this->any())
             ->method('getRequestURI')
             ->will($this->returnValue('/example/user/1/comment/rating_score'));
        $isMatched = $this->_route->match($this->_request);
        $this->assertEquals(false, $isMatched);
    }

}// END OF CLASS
