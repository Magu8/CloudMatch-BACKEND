<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$local_fouls = $redis->get('local_fouls');

$local_fouls = intval($local_fouls) + 1;

$redis->set('local_fouls', $local_fouls);

echo json_encode($local_fouls);