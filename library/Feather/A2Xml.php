<?php
#########################################################################
# File Name: Feather/A2Xml.php
# Desc: Convert Array To Xml
# Author: Shaolin Wang
# mail: wangshaolin@yongche.com
# Created Time: 2015年05月05日 星期二 17时02分41秒
#########################################################################

namespace Feather;
class A2Xml {

    private $version    = '1.0';      //Version Of Xml
    private $encoding   = null;       //Character Encoding Of Xml
    private $xml        = null;

    const DEFAULT_ENCODING = 'UTF-8'; //Default Character Encoding Of Xml

    public function __construct($encoding = self::DEFAULT_ENCODING) {
        $this->xml = new XmlWriter();
        $this->encoding = $encoding;
    }

    public function getXml( $data = [] ){
        $this->_setHeader();
        $this->_toXml($data);
        return $this->_setEnd();
    }

    private function _setHeader(){
        $this->xml->openMemory();
        $this->xml->startDocument($this->version, $this->encoding);
    }

    private function _setEnd(){
        $this->xml->endDocument();
        return $this->xml->outputMemory(true);
    }

    private function _toXml($data) {
        foreach($data as $key => $value){
            if(is_numeric($key)) {
                $key = 'value';
            }
            if(!is_array($value)){
                $this->xml->writeElement($key, $value);
                continue;
            } else {
                $this->xml->startElement($key);
                $this->_toXml($value);
                $this->xml->endElement();
            }
        }
    }
}
