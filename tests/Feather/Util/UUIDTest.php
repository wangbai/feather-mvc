<?php

namespace Feather\Util;

class UUIDTest extends \PHPUnit_Framework_TestCase {

    public function testGetV4UUID() {
        $uuid = UUID::getV4UUID();
        $this->assertEquals(strlen($uuid), 36);
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
