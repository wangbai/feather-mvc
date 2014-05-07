<?php

namespace Feather\Mvc\Template;

class SimpleTest extends \PHPUnit_Framework_TestCase {

    private $_simpleTemplate;
    private $_baseDir;
    private $_request;
    private $_response;

    protected function setUp() {
        $this->_request = $this->getMock('Feather\Mvc\Http\Request');
        $this->_response = $this->getMock('Feather\Mvc\Http\Response');
        $this->_baseDir = realpath(dirname(__FILE__))."/tpl";
        $this->_simpleTemplate = new Simple($this->_baseDir, $this->_request, $this->_response);
    }

    public function testRealTemplatePath() {
        $templateFilePath = "/user/default.tpl";
        $realpath = $this->_simpleTemplate->getRealPathOfTemplateFile($templateFilePath);

        $this->assertEquals($realpath, realpath(dirname(__FILE__))."/tpl/user/default.tpl");
    }

    public function testDefault() {
        $templateFilePath = "/user/default.tpl";

        $this->_response->expects($this->any())
             ->method('getTemplateParams')
             ->will($this->returnValue(array("user_id" => 1, "user_name" => "wangbai")));

        $expectBody=<<<HTML
user_id=1,user_name=wangbai
HTML;

        $result = $this->_simpleTemplate->render($templateFilePath);  
        $this->assertEquals($expectBody, $result);
    }

    public function testStatic() {
        $templateFilePath = "/static.tpl";

        $expectBody=<<<HTML
Hello World!\n
HTML;
        $result = $this->_simpleTemplate->render($templateFilePath);
        $this->assertEquals($expectBody, $result);
    }

    public function testTemplateFileNotExist() {
        $templateFilePath = "/user/static.tpl";

        $this->setExpectedException(
          'Feather\Mvc\Template\Exception'
        );

        $result = $this->_simpleTemplate->render($templateFilePath);
    }

    public function testEmbedTemplate() {
        $templateFilePath = "/user/main.tpl";
        $this->_response->expects($this->any())
             ->method('getTemplateParams')
             ->will($this->returnValue(array("user_id" => 1, "user_name" => "wangbai")));

        $expectBody=<<<HTML
Hello wangbai!
user_id=1,user_name=wangbai
HTML;

        $result = $this->_simpleTemplate->render($templateFilePath);
        $this->assertEquals($expectBody, $result);
    }

}// END OF CLASS
