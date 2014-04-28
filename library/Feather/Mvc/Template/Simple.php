<?php

namespace Feather\Mvc\Template;

use Feather\Mvc\Http\Response;

class Simple extends AbstractTemplate {
    
    public function load() {
        $response = $this->getResponse();
        if (empty($response)) {
            throw new Exception("No response is set");
        }

        $params = $response->getTemplateParams();
        if (!empty($params)) {
            extract($params);
        }

        $templateFilePath = $this->getTemplateFilePath();
        if (empty($templateFilePath) || !file_exists($templateFilePath)) {
            throw new Exception("Template File:".$templateFilePath." is invalid");
        }

        //parse the template       
        ob_start();
        include $templateFilePath;
        $content = ob_get_clean();

        $response->setBody($content);
        return $response;
    }

}// END OF CLASS
