<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$score = $_GET['score'];

$visitor_score = $redis->get('visitor_score');

$visitor_score = intval($visitor_score) + intval($score);

$redis->set('visitor_score', $visitor_score);

echo json_encode($visitor_score);