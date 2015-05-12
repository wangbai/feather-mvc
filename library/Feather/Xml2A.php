<?php
#########################################################################
# File Name: Xml2A.php
# Desc: 
# Author: hanhaoran
# mail: hanhaoran@yongche.com
# Created Time: Tue May 12 14:09:24 2015
#########################################################################

namespace Feather;
class Xml2A{

    public static function getArray($xml){
        $simpleXmlObj = simplexml_load_string($xml);
        $json = json_encode($simpleXmlObj);
        $array = json_decode($json,TRUE);
        return $array;
    }

}
