<?php

// Check composer vendor autoload
if (file_exists(dirname(__DIR__).'/vendor/autoload.php')) {
    require dirname(__DIR__).'/vendor/autoload.php';
} 

// Check if composer vendor contain feather-mvc
if (!file_exists(dirname(__DIR__).'/vendor/wangbai/feather-mvc')) {
    // Ensure library/ is in include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__DIR__))."/library/",
        get_include_path(),
    )));
    require 'Feather/Autoloader.php';
}

// Autoload class
$loader = Feather\Autoloader::getInstance();
$loader->init();
