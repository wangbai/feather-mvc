<?php

namespace Feather\Util;

class UUID {
    
    /** 
    * Mongo Object ID
    * time - 4 byte
    * machine id - 3 byte
    * pid - 2 byte
    * inc - 3 byte
    *
    * @return string 24 bytes length string
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


    /*
    * Long Integer UUID
    * timestamp - 40 bit
    * machineId - 12 bit
    * increment - 12 bit
    * not recommend
    *
    * @return long 8 byte big int UUID
    */

    const START_TIMESTAMP = 1388505600000; //2014-01-01 00:00:00

    static public function getBigIntUUID() {
        static $incBase = false;
        if ($incBase === false) {
            $incBase = mt_rand(0, 0xfff);
        }

        //timestamp
        list($msec, $sec) = explode(" ", microtime());
        $mtime = $sec * 1000 + round($msec * 1000) - self::START_TIMESTAMP;
        $id = ($mtime & 0xffffffffff) << (64-40);

        //machine id
        $machineId = mt_rand(0, 0xfff);
        $id |= ($machineId & 0xfff) << (64-40-12);

        //increment
        $inc = ++$incBase;
        $inc = $inc % 0xfff;
        $id |= $inc;

        return sprintf('%u', $id)."-".round($msec * 1000)."-".$inc;
    }

}// END OF CLASS
