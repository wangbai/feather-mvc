<?php

namespace Feather\Db;

use Feather\Util\Registry;

class AdapterFactory {

    public static function getAdapter($config, $dbType = 'mysqli') {
        $className = "Feather\\Db\\".ucfirst($dbType)."Adapter";

        //fill the default fields
        $config = array_merge(AbstractAdapter::$defaultConfig, $config);

        $footprint = md5("Feather-Db-$className-".serialize($config));
        $adapter = Registry::get($footprint);
        
        if (empty($adapter)) {
            $adapter = new $className($config);
            Registry::set($footprint, $adapter);
        }        

        return $adapter;            
    }

}// END OF CLASS
