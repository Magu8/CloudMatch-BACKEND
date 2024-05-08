<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

require "../../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}


if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$score = $_GET['score'];
$player = $_GET['player'];
$match = $_GET['match'];


$consult = "INSERT INTO score_player (match_score, player, score) VALUES ( ?, ?, ? )";

try {
    $stmt = mysqli_prepare($connection, $consult);
    if ($stmt) {

        $visitor_score = $redis->get('visitor_score');

        $visitor_score = intval($visitor_score) + intval($score);

        $redis->set('visitor_score', $visitor_score);

        mysqli_stmt_bind_param($stmt, "iii", $match, $player, $score);
        mysqli_stmt_execute($stmt);
        echo json_encode($visitor_score);
    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;
}
