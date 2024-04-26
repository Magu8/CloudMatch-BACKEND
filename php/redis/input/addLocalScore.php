<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$score = $_GET['score'];

$local_score = $redis->get('local_score');

$local_score = intval($local_score) + intval($score);

$redis->set('local_score', $local_score);

echo json_encode($local_score);