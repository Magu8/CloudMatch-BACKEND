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
$score = $_GET['score'];
$date = $_GET['match_date'];

$scoreConsult = "UPDATE participants SET score = score + 3 WHERE league = ? AND participant_team = ?";
$winConsult = "INSERT INTO team_wins (team, win_date, score) VALUES (?, ?, ?)";


try {
    $scoreStmt = mysqli_prepare($connection, $scoreConsult);

    if ($scoreStmt) {
        mysqli_stmt_bind_param($scoreStmt, "ii", $leagueId, $teamId);
        mysqli_stmt_execute($scoreStmt);

        $winStmt = mysqli_prepare($connection, $winConsult);
        if ($winStmt) {
            mysqli_stmt_bind_param($winStmt, "isi", $teamId, $date, $score);
            mysqli_stmt_execute($winStmt);
            http_response_code(200);
            echo json_encode(["message" => "Score successfully added"]);

        } else {
            echo json_encode(["message" => "Error while preparing first consult"]);

        }

    } else {
        echo json_encode(["message" => "Error while preparing first consult"]);

    }

} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;
}