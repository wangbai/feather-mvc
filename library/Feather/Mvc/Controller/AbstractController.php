<?php

namespace Feather\Mvc\Controller;

use Feather\Mvc\Http\Common;
use Feather\Mvc\Http\Request;
use Feather\Mvc\Http\Response;

abstract class AbstractController {

    private $_request = null;

    private $_response = null;

    /**
    * Create a controller
    * 
    * @param Feather\Mvc\Http\Request $request
    * @param Feather\Mvc\Http\Response $response
    */
    public function __construct(Request $request, Response $response) {
        $this->_request = $request;
        $this->_response = $response;
    }

    /**
    * callback function before action is executed
    */
    public function init() {
    }

    /**
    * callback function after action has been executed
    */
    public function shutdown() {
    }

    /**
    * Get the request associated to the controller
    * 
    * @return Feather\Mvc\Http\Request 
    */
    public function getRequest() {
        return $this->_request;
    }

    /**
    * Get the response associated to the controller
    *
    * @return Feather\Mvc\Http\Response
    */
    public function getResponse() {
        return $this->_response;
    }

    /**
    * handle the controller behaviour before and after action
    *
    * @return Feather\Mvc\Http\Response
    */
    public function __call($method, $args) {
        $response = $this->getResponse();

        try {
            $this->init();

            if (!method_exists($this, $method)) {
                throw new Exception("Method: ".$method." doesn't exist", Common::SC_NOT_FOUND);
            }

            $result = $this->$method();
            $response->setReturn($result);
        } catch (Exception $e) {
            $response->setException($e);   
        }

        $this->shutdown();
 
        return $response;
    }

}//END OF CLASS
