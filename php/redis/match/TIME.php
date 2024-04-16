<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$time = $redis->get('TIME');

$timeObj = DateTime::createFromFormat('H:i', $time);

$timeObj->modify('-1 second');

$newTime = $timeObj->format('H:i');

$redis->set('TIME', $newTime);


echo json_encode($newTime);