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

}
