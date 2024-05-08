<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

require "../../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$player = $_GET['player'];
$match = $_GET['match'];


$consult = "INSERT INTO fouls_player (match_fouls, player) VALUES ( ?, ? )";

try {
    $stmt = mysqli_prepare($connection, $consult);
    if ($stmt) {

        $local_fouls = $redis->get('local_fouls');

        $local_fouls = intval($local_fouls) + 1;
        
        $redis->set('local_fouls', $local_fouls);
        
        mysqli_stmt_bind_param($stmt, "ii", $match, $player);
        mysqli_stmt_execute($stmt);
        echo json_encode($local_fouls);
    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;
}