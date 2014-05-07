<?php

namespace Feather\Mvc\Template;

use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;

abstract class AbstractTemplate {

    private $_baseDirectory = "";

    private $_request = null;

    private $_response = null;

    /*
    * Create a template handler instance
    *
    * @param string $baseDirectory
    * @param Feather\Mvc\Http\Request $request
    * @param Feather\Mvc\Http\Response $response
    */
    public function __construct($baseDirectory, Request $request, Response $response) {
        $this->setBaseDirectory($baseDirectory);
        $this->setRequest($request);
        $this->setResponse($response);
    }

    /*
    * Get the directory of all the template files
    *
    * @return string
    */
    public function getBaseDirectory() {
        return $this->_baseDirectory;
    }

    /*
    * Set the directory of all the template files
    *
    * @param string $baseDirectory
    * @return
    */
    public function setBaseDirectory($baseDirectory) {
        $baseDirectory = (string) $baseDirectory;
        $baseDirectory = rtrim($baseDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR; 

        $this->_baseDirectory = $baseDirectory;
        return;
    }

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
    * Get the real path of the template handler in the application
    *
    * @param $templateFilePath
    * @return string
    */
    public function getRealPathOfTemplateFile($templateFilePath) {
        return $this->_baseDirectory.ltrim($templateFilePath, DIRECTORY_SEPARATOR);
    }

    /*
    * Parse the template file, finally return the whole content
    *
    * @return string 
    */
    abstract public function render($templateFilePath);

}// END OF CLASS
