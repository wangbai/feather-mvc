<?php

namespace V1;

use Feather\Mvc\Controller\AbstractController;

class UserController extends AbstractController {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $response = $this->getResponse();
        $response->setTemplateParam("content", "wangbai");
    }

    public function loginAction() {
        $response = $this->getResponse();
        $response->setTemplateParam("name", "wangbai");
    }

}// END OF CLASS
