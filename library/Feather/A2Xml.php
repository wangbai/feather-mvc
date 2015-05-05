<?php
#########################################################################
# File Name: Feather/A2Xml.php
# Desc: 
# Author: Shaolin Wang
# mail: wangshaolin@yongche.com
# Created Time: 2015年05月05日 星期二 17时02分41秒
#########################################################################

namespace Feather;
class A2Xml {
    private $i = 0;
    private $version    = '1.0';
    private $encoding   = 'UTF-8';
    private $root       = 'result';
    private $xml        = null;
    function __construct() {
        $this->i = 0;
        $this->xml = new XmlWriter();
    }
    public function setHeader(){
        $this->xml->openMemory();
        $this->xml->startDocument($this->version, $this->encoding);
        $this->xml->startElement($this->root);

    }
    public function setEnd(){
        $this->xml->endElement();
        $this->xml->endDocument();
        return $this->xml->outputMemory(true);
    }

    function toXml($data) {
        foreach($data as $key => $value){
            if(is_numeric($key)) {
                $key = 'value';
            }
            if(!is_array($value)){
                $this->xml->writeElement($key, $value);
                continue;
            } else {
                $this->xml->startElement($key);
                $this->toXml($value);
                $this->xml->endElement();
            }
        }
    }
}
