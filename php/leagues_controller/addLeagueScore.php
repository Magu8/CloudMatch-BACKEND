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

$leagueId = $_GET['league_id'];
$teamId = $_GET['participant_id'];

$consult = "UPDATE participants SET score = score + 3 WHERE league = ? AND participant_team = ?";

try {
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $leagueId, $teamId);
        mysqli_stmt_execute($stmt);
        http_response_code(200);
        echo json_encode(["message" => "Score successfully added"]);

    } else {
        echo json_encode(["message" => "Error while preparing consult"]);

    }

} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;
}