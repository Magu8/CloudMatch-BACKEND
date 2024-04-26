<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$input_data = json_decode(file_get_contents("php://input"), true);

$local_team = $input_data['local_team'];
$local_id = $input_data['local_id'];
$visitor_team = $input_data['visitor_team'];
$visitor_id = $input_data['visitor_id'];
$match_id = $input_data['match_id'];


if (isset($local_team) && !empty($local_team) && isset($visitor_team) && !empty($visitor_team) && isset($match_id) && !empty($match_id) && isset($local_id) && !empty($local_id) && isset($visitor_id) && !empty($visitor_id)) {
    $redis->set('match_id', $match_id);
    $redis->set('local_id', $local_id);
    $redis->set('local_team', $local_team);
    $redis->set('visitor_id', $visitor_id);
    $redis->set('visitor_team', $visitor_team);
    echo json_encode(['message' => 'Data settled']);
} else {
    http_response_code(409);
    echo json_encode(['message' => 'Must fill all fields']);
}