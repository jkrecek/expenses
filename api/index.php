<?php

define('BASEPATH', '.');

spl_autoload_register(function ($class_name) {
    include "classes/" . $class_name . '.php';
});

$api = new Api();
$api->handle();
$api->sendResponse();
