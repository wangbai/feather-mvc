<?php

use Feather\Mvc\Controller\AbstractController;

class IndexController extends AbstractController {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $response = $this->getResponse();
        $response->setTemplateParam("content", "wangbai");
    }

    public function noTemplateAction() {
        $response = $this->getResponse();
        $response->setNeedTemplate(false);
        $response->setBody("Hello World!");
    }

    public function errorAction() {
        throw new Exception("Error");
    }

}//END OF CLASS
