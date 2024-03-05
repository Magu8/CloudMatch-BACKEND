<?php

require '../../vendor/predis/predis/autoload.php';

try {
Predis\Autoloader::register();

$redis = new Predis\Client();

} catch (Exception $e) {
    die($e->getMessage());
}