<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$redis->set('TIME', "10:00");
$redis->set('PERIOD', "1");
$redis->set('local_score', '0');
$redis->set('local_fouls', '0');
$redis->set('visitor_score', '0');
$redis->set('visitor_fouls', '0');

echo json_encode(['message' => 'Match reseted']);