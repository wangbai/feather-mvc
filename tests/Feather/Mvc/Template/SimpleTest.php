<?php

namespace Feather\Mvc\Template;

class SimpleTest extends \PHPUnit_Framework_TestCase {

    private $_simpleTemplate;
    private $_templateFilePath;
    private $_response;

    protected function setUp() {
        $this->_simpleTemplate = new Simple;
        $this->_response = $this->getMock('Feather\Mvc\Http\Response');
    }

    public function testDefault() {
        $this->_templateFilePath = realpath(dirname(__FILE__))."/default.tpl";

        $this->_response->expects($this->any())
             ->method('getTemplateParams')
             ->will($this->returnValue(array("user_id" => 1, "user_name" => "wangbai")));

        $this->_simpleTemplate->setResponse($this->_response);
        $this->_simpleTemplate->setTemplateFilePath($this->_templateFilePath);


        $expectBody=<<<HTML
user_id=1,user_name=wangbai
HTML;

        $this->_response->expects($this->once())
             ->method('setBody')
             ->with($this->equalTo($expectBody));

        $resultReponse = $this->_simpleTemplate->load();  
    }

    public function testStatic() {
        $this->_templateFilePath = realpath(dirname(__FILE__))."/static.tpl";

        $this->_simpleTemplate->setResponse($this->_response);
        $this->_simpleTemplate->setTemplateFilePath($this->_templateFilePath);


        $expectBody=<<<HTML
Hello World!\n
HTML;

        $this->_response->expects($this->once())
             ->method('setBody')
             ->with($this->equalTo($expectBody));

        $resultReponse = $this->_simpleTemplate->load();
    }

    public function testNoResponse() {
        $this->_templateFilePath = realpath(dirname(__FILE__))."/static.tpl";
        $this->_simpleTemplate->setTemplateFilePath($this->_templateFilePath);
        
        $this->setExpectedException(
          'Feather\Mvc\Template\Exception', 'No response is set'
        );
        $resultReponse = $this->_simpleTemplate->load();
    }
}// END OF CLASS
