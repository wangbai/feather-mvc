<?php

// Check composer vendor autoload
if (file_exists(dirname(__DIR__).'/vendor/composer')) {
    require dirname(__DIR__).'/vendor/composer/autoload_namespaces.php';
}

// Ensure library/ is in include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__))."/../library/",
    "/usr/share/pear/",
    get_include_path(),
)));

// Autoload class
require 'Feather/Autoloader.php';
$loader = Feather\Autoloader::getInstance();
$loader->init();
