<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$visitor_score = $redis->get('visitor_score');

$visitor_score = intval($visitor_score) + 1;

$redis->set('visitor_score', $visitor_score);

echo json_encode($visitor_score);