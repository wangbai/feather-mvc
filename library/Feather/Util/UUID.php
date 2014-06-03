<?php

namespace Feather\Util;

class UUID {
    
    /** 
    * Mongo Object ID
    * time - 4 byte(char)
    * machine id - 3 byte(char)
    * pid - 2 byte(char)
    * inc - 3 byte(char)
    *
    * @return string 12 bytes length string
    */
    static public function getMongoUUID() {
        static $incBase = false;

        if ($incBase === false) {
            $incBase = mt_rand(0, 0xffffff);
        }

        $time = time();
        $machineId = mt_rand(0, 0xffffff);
        $processId = getmypid();
        $inc = ++$incBase;

        return sprintf(
            "%08x%06x%04x%06x",
            $time,
            $machineId,
            $processId,
            $inc
        );  
    }

    /**
    * customized uuid
    *
    * @param string $namespace Namespace for the UUID
    * @return 21 bytes length string
    */
    static public function getCustomizedUUID($namespace) {
        $prefix = hash('crc32', $namespace, false);
        return $prefix."-".self::getMongoUUID();
    }

}// END OF CLASS
