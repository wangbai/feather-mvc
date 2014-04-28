<?php

namespace Feather\Mvc\Http;

class Response {

    private $_body = null;

    //return from controller action
    private $_return = null;

    private $_httpCode = Common::SC_OK;

    private $_headers = array();
 
    //whether need a template or not
    private $_needTemplate = true;

    //specify a template
    private $_templateName = "";

    //params for template
    private $_templateParams = array();

    //cached exception
    private $_exception = null;

    /*
    * Get http output
    *
    * @return
    */
    public function getBody() {
        return $this->_body;
    }

    /*
    * Set http output
    *
    * @param string $body
    * @return
    */
    public function setBody($body) {
        $this->_body = $body;
        return;
    }

    /*
    * Get return value of the Controller
    *
    * @return
    */
    public function getReturn() {
        return $this->_return;
    }

    /*
    * Set return value of the Controller
    *
    * @param $ret Return value of the Controller
    * @return
    */
    public function setReturn($ret) {
        $this->_return = $ret;
        return;
    }

    /*
    * Get http status code
    * 
    * @return int http status code
    */
    public function getHttpCode() {
        return $this->_httpCode;
    }

    /*
    * Set http status code
    * 
    * @param int $httpCode http status code
    * @return
    */
    public function setHttpCode($httpCode) {
        $httpCode = intval($httpCode);
        $this->_httpCode = $httpCode;
        return;
    }

    /*
    * Get all header option
    *
    * @return
    */
    public function getHeaders() {
        return $this->_headers;
    }

    /*
    * Get a varible of headers
    *
    * @param string $key Name of the variable
    * @return
    */
    public function getHeader($key) {
        $key = (string) $key;
        if (isset($this->_headers[$key])) {
            return $this->_headers[$key];
        }

        return null;
    }

    /*
    * Set a varible of headers
    *
    * @param string $key Name of the variable
    * @param string $value Value of the variable
    * @return
    */
    public function setHeader($key, $value) {
        $key = (string) $key;
        $value = (string) $value;
        $this->_headers[$key] = $value;

        return;
    }
    
    /*
    * unset a varible of headers
    *
    * @param string $key Name of the variable
    * @return
    */
    public function unsetHeader($key) {
        $key = (string) $key;

        if (isset($this->_headers[$key])) {
            unset($this->_headers[$key]);
        }

        return;
    }

    /*
    * clear all headers
    *
    * @return
    */
    public function clearHeaders() {
        $this->_headers = array();
        return;
    }

    /*
    * flush the response to output
    */
    public function output() {
        //output header
        $headers = $this->getHeaders();
        if (!empty($headers)) {
            foreach($headers as $k => $v) {
                header($k . ': ' . $v);
            }
        }

        //output http code
        http_response_code($this->getHttpCode());

        //output body
        echo $this->getBody();
        return;
    }

    /*
    * Whether the template should be loaded
    *
    * @return bool
    */
    public function isNeedTemplate() {
        return $this->_needTemplate;
    }

    /*
    * Set whether the template should be loaded
    *
    * @param bool $needTemplate
    * @return
    */
    public function setNeedTemplate($needTemplate) {
        $needTemplate = (bool) $needTemplate;
        $this->_needTemplate = $needTemplate;
        return;
    }

    /*
    * Get the template name
    *
    * @return string
    */
    public function getTemplateName() {
        return $this->_templateName;
    }

    /*
    * Set the template name
    *
    * @param string $key Name of the template 
    * @return
    */
    public function setTemplateName($templateName) {
        $templateName = (string) $templateName;
        $this->_templateName = $templateName;
        return;
    }

    /*
    * Get all variables passed to the template
    *
    * @return array
    */ 
    public function getTemplateParams() {
        return $this->_templateParams;
    }

    /*
    * Get a variable passed to the template
    *
    * @param string $key Name of the variable
    * @return
    */
    public function getTemplateParam($key) {
        $key = (string) $key;
        if (isset($this->_templateParams[$key])) {
            return $this->_templateParams[$key];
        }

        return null;
    }

    /*
    * Set a variable passed to the template
    *
    * @param string $key Name of the variable
    * @param $vaule Value of the variable
    * @return
    */
    public function setTemplateParam($key, $value) {
        $key = (string)$key;
        if (null !== $key) {
            $this->_templateParams[$key] = $value;
        }
        return;
    }

    /*
    * clear all variables
    *
    * @return
    */
    public function clearTemplateParams() {
        $this->_templateParams = array();
        return;
    }

    /*
    * Get an unhandled exception
    *
    * @return Exception
    */
    public function getException() {
        return $this->_exception;    
    }

    /*
    * Whether the response has an unhandled exception
    *
    * @return bool
    */
    public function isExceptional() {
        return !empty($this->_exception);
    }

    /*
    * Set an unhandled exception
    *
    * @param Exception $e an unhandle exception
    * @return
    */
    public function setException(\Exception $e) {
        $this->_exception = $e;
        return;
    }

    /*
    * Clear an unhandled exception
    *
    * @return
    */
    public function clearException() {
        $this->_exception = null;
        return;
    }

}// END OF CLASS
