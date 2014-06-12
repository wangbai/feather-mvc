<?php
include "../application/bootstrap.php";

for($i = 0; $i < 1000; ++$i) {
    $uuid = Feather\Util\UUID::getBigIntUUID();
    echo $uuid."\n";
}
