<?php

namespace Feather\Util;

class UUIDTest extends \PHPUnit_Framework_TestCase {

    public function testGetMongoUUID() {
        $uuid = UUID::getMongoUUID();
        $this->assertEquals(strlen($uuid), 24);
    }

    public function testGetMacAddress() {
        $uuid = UUID::getCustomizedUUID("bj");
        echo $uuid."\n";
        $uuid = UUID::getCustomizedUUID("sh");
        echo $uuid."\n";
        $uuid = UUID::getCustomizedUUID("gz");
        echo $uuid."\n";
        $uuid = UUID::getCustomizedUUID("sz");
        echo $uuid."\n";

        echo strlen($uuid)."\n";
    }

}// END OF CLASS
