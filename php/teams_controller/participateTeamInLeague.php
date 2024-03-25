<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$teamId = $_GET["team_id"];
$leagueId = $_GET["league_id"];

$consult = "INSERT INTO participants (league, participant_team) VALUES (?, ?)";

try {
    $stmt = mysqli_prepare($connection,$consult);

    if($stmt) {
        mysqli_stmt_bind_param($stmt,"ii", $leagueId, $teamId);
        mysqli_execute($stmt);
        http_response_code(201);
        echo json_encode(["message" => "Participation successfully added"]);
        
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Something went wrong: " . $ex->getMessage()]);

    }
    
} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;
    
}