<?php

namespace Feather\Mvc\Template;

use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;

abstract class AbstractTemplate {

    private $_request = null;

    private $_response = null;

    private $_templateFilePath = "";

    /*
    * Get the request associated with the template
    *
    * @return Feather\Mvc\Http\Request
    */
    public function getRequest() {
        return $this->_request;
    }

    /*
    * Set the request associated with the template
    *
    * @param Feather\Mvc\Http\Request $request
    * @return
    */
    public function setRequest(Request $request) {
        $this->_request = $request;
        return;
    }

    /*
    * Get the response associated with the template
    *
    * @return Feather\Mvc\Http\Response
    */
    public function getResponse() {
        return $this->_response;
    }

    /*
    * Set the response associated with the template
    *
    * @param Feather\Mvc\Http\Response $response
    * @return
    */
    public function setResponse(Response $response) {
        $this->_response = $response;
        return;
    }

    /*
    * Get the template file path
    *
    * @return string
    */
    public function getTemplateFilePath() {
        return $this->_templateFilePath;
    }

    /*
    * Set the template file path
    *
    * @param string $templateFilePath
    * @return string
    */
    public function setTemplateFilePath($templateFilePath) {
        $templateFilePath = (string) $templateFilePath;

        $this->_templateFilePath = $templateFilePath;
        return;
    }

    /*
    * load and parse the template file, finally put the body into response
    *
    * @return Feather\Mvc\Http\Response
    */
    abstract public function load();

}// END OF CLASS
