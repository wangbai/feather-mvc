<?php

namespace Feather\Mvc\Template;

use Feather\Mvc\Http\Response;

class Simple extends AbstractTemplate {
    
    public function render($templateFilePath) {
        $templateFilePath = $this->getRealPathOfTemplateFile($templateFilePath);
        if (empty($templateFilePath) || !file_exists($templateFilePath)) {
            throw new Exception("Template File:".$templateFilePath." is invalid");
        }

        //release the input params
        $response = $this->getResponse();
        $params = $response->getTemplateParams();
        if (!empty($params)) {
            extract($params);
        }

        //parse the template       
        ob_start();
        ob_implicit_flush(0);
        include $templateFilePath;
        $content = ob_get_clean();

        return $content;
    }

}// END OF CLASS
