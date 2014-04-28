<?php

namespace Feather\Mvc\Http; 

class Request {
 
    const QUERY_DELIMITER = "?";
 
    //customized variables   
    private $_params = array();

    //cache for body
    private $_rawBody = "";

    //cache for headers
    private $_headers = array();
 
    /*
    * retrieve $_GET and $_POST
    *
    * @return array
    */ 
    public function getParams() {
        $ret = $this->_params;
        $ret += $_GET;
        $ret += $_POST;
        return $ret;
    }

    /*
    * retrieve a variable of $_GET or $_POST
    *
    * @param string $key Name of the variable
    * @param string $default 
    * @return string
    */
    public function getParam($key, $default = null) {
        $params = $this->_params;
        if (isset($params[$key])) {
            return $params[$key];
        } elseif (isset($_GET[$key])) {
            return $_GET[$key];
        } elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }

    /*
    * set a variable that is cached in request
    *
    * @param string $key Name of the variable
    * @param string $value Value of the variable
    * @return
    */
    public function setParam($key, $value) {
        $key = (string) $key;
        $this->_params[$key] = $value;

        return;
    }

    /*
    * unset a variable that is cached in request
    *
    * @param string $key Name of the variable
    * @return string
    */
    public function unsetParam($key) {
        $key = (string) $key;
        if (isset($this->_params[$key])) {
            unset($this->_params[$key]);
        }

        return;
    }

    /*
    * retrieve $_COOKIE
    *
    * @return array
    */
    public function getCookies() {
        return $_COOKIE;
    }

    /*
    * retrieve a variable of $_COOKIE
    * 
    * @param string $key Name of the variable
    * @param string $default
    * @return string
    */
    public function getCookie($key, $default = null) {
        $key = (string) $key;

        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        
        return $default;
    }

    /*
    * retrieve all headers
    *
    * @return array
    */
    public function getHeaders() {
        //read cache
        if (!empty($this->_headers)) {
            return $this->_headers;
        }

        $ret = array();
        $headers = array();

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = array_merge($_ENV, $_SERVER);

            foreach ($headers as $key => $val) {
                if (strpos(strtolower($key), 'content-type') !== FALSE) {
                    continue;
                }
                if (strtoupper(substr($key, 0, 5)) != "HTTP_") {
                    unset($headers[$key]);
                }
            }
        }

        foreach ($headers AS $key => $value) {
            $key = preg_replace('/^HTTP_/i', '', $key);
            $key = str_replace(
                    " ",
                    "-",
                    ucwords(strtolower(str_replace(array("-", "_"), " ", $key)))
                );
            $ret[$key] = $value;
        }
        ksort($ret);

        //cache headers
        $this->_headers = $ret;
        return $ret;
    }

    /*
    * retrieve a variable of header
    *
    * @param string $key Name of the variable
    * @param string $default
    * @return string
    */
    public function getHeader($key, $default = null) {
        $headers = $this->getHeaders();
        $key = (string) $key;

        if (isset($headers[$key])) {
            return $headers[$key];
        }

        return $default;
    }

    /*
    * Get the raw post body
    *
    * @return string
    */
    public function getRawBody() {
        if (empty($this->_rawBody)) {
            $body = file_get_contents('php://input');

            if (strlen(trim($body)) > 0) {
                $this->_rawBody = $body;
            }
        }
        return $this->_rawBody;
    }

    /*
    * Get $_SERVER
    *
    * @return array
    */
    public function getServers() {
        return $_SERVER;
    }

    /*
    * Get a variable of $_SERVER
    *
    * @param string $key Name of the varible
    * @param $default
    * @return string
    */
    public function getServer($key, $default = null) {
        $key = (string) $key;

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }

    /*
    * Get the request uri
    *
    * @return string
    */
    public function getRequestURI() {
        $uri = $this->getServer("REQUEST_URI");
        $tmpUri = strstr($uri, self::QUERY_DELIMITER, true);

        if ($tmpUri) {
            return $tmpUri;
        }

        return $uri;
    }

    /**
     * Get the client's IP addres
     *
     * @param  boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true) {
        if ($checkProxy && $this->getServer('HTTP_CLIENT_IP') != null) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } else if ($checkProxy && $this->getServer('HTTP_X_FORWARDED_FOR') != null) {
            $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = $this->getServer('REMOTE_ADDR');
        }

        return $ip;
    }

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure() {
        return $this->getServer('HTTPS') == 'on';
    }
    
    /*
    * Get Http Method: GET, POST, PUT, DELETE
    *
    * @return string
    */
    public function getHttpMethod() {
        return $this->getServer('REQUEST_METHOD');
    }

}//END OF CLASS
