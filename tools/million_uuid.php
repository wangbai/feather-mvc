<?php
include "../application/bootstrap.php";

for($i = 0; $i < 1000000; ++$i) {
    $uuid = Feather\Util\UUID::getV4UUID();
    echo $uuid."\n";
}
