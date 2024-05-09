<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

require "../../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data['local_score']) && isset($input_data['local_fouls']) && isset($input_data['visitor_score']) && isset($input_data['visitor_fouls']) && isset($input_data['match_id'])) {
    $local_score = $input_data['local_score'];
    $local_fouls = $input_data['local_fouls'];

    $visitor_score = $input_data['visitor_score'];
    $visitor_fouls = $input_data['visitor_fouls'];

    $match_id = $input_data['match_id'];

    $consult = "UPDATE play_match SET local_score = ?, local_fouls = ?, visitor_score = ?, visitor_fouls = ?, finished = true WHERE match_id = ?";


    try {
        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iiiii", $local_score, $local_fouls, $visitor_score, $visitor_fouls, $match_id);
            mysqli_stmt_execute($stmt);
            http_response_code(200);
            echo json_encode(['message' => 'Succesfully saved']);

        } else {
            echo json_encode(['message' => 'Error while preparing statement']);

        }
    } catch (\Throwable $th) {
        http_response_code(500);
        echo "An error ocurred" . throw $th;
    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Some data is missing"]);
}



