<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$visitor_fouls = $redis->get('visitor_fouls');

$visitor_fouls = intval($visitor_fouls) - 1;

$redis->set('visitor_fouls', $visitor_fouls);

echo json_encode($visitor_fouls);