<?php

namespace Feather\Db;

use Feather\Util\Registry;

class AdapterFactory {

    public static function getAdapter($config, $db = 'mysqli') {
        $className = ucfirst($db)."Adapter";
        $footprint = "Feather-Db-$className-".(string)$adapter;
        
        $adapter = Registry::get($footprint);
        if (empty($adapter)) {
            $adapter = new $className($config);
            Registry::set($footprint, $adapter);
        }        

        return $adapter;            
    }

}// END OF CLASS
