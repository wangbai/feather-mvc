<?php

namespace Feather\Db;

use Feather\Util\Registry;

class AdapterFactory {

    /**
    * Create a db adapter
    *
    * @param array $config db connect params
    * @param string $dbType
    * @return Feather\Db\AbstractAdapter
    */
    public static function getAdapter($config, $dbType = 'mysqli') {
        $className = "Feather\\Db\\".ucfirst($dbType)."Adapter";
        return new $className($config);
    }

}// END OF CLASS
