<?php

namespace Feather\Db;

class AdapterFactory {

    public static function getAdapter($config, $db = 'mysqli') {
        $className = ucfirst($db)."Adapter";
        return new $className($config);
    }

}// END OF CLASS
