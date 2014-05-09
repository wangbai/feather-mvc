<?php

namespace Feather\Util;

class UUIDTest extends \PHPUnit_Framework_TestCase {

    public function testGetMacAddress() {
        $uuid = UUID::v4();
        $this->assertEquals(strlen($uuid), 36);
    }

}// END OF CLASS
