<?php

use Feather\Mvc\Controller\AbstractController;

class UserController extends AbstractController {
    
    public function getListAction() {
        $response = $this->getResponse();
        $response->setNeedTemplate(false);
        $response->setBody("Hello World!");
    }

}// END OF CLASS
