<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

$addparticipantConsult = "INSERT INTO participants (league, participant_team) VALUES (?, ?)";
$checkParticipantConsult = "SELECT * FROM participants WHERE participant_team= ? AND league= ?";

try {
    $checkStmt = mysqli_prepare($connection, $checkParticipantConsult);

    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "ii", $teamId, $leagueId);
        mysqli_execute($checkStmt);

        $result = mysqli_stmt_get_result($checkStmt);

        if ($result->num_rows > 0) {
            http_response_code(409);
            echo json_encode(["error" => "This team is already participating in this league"]);
            mysqli_stmt_close($checkStmt);

        } else {
            $addStmt = mysqli_prepare($connection, $addparticipantConsult);

            if ($addStmt) {
                mysqli_stmt_bind_param($addStmt, "ii", $leagueId, $teamId);
                mysqli_execute($addStmt);
                http_response_code(201);
                echo json_encode(["message" => "Participation successfully added"]);

            } else {
                http_response_code(500);
                echo json_encode(["message" => "Something went wrong: " . $ex->getMessage()]);

            }
        }
    }

} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;

}