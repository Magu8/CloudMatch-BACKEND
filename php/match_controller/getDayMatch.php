<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$leagueId = $_GET["league"];

$date = $_GET["match_date"];

$consult = "SELECT match_id, league_name, league_id, match_date, match_time, finished,
CONCAT(user_name, ' ', user_surname) AS referee,
local_team AS local_id, 
local.team_name AS local_team,
local.team_logo AS local_team_logo,
local_score,
visitor_team AS visitor_id,
visitor.team_name AS visitor_team,
visitor.team_logo AS visitor_team_logo,
visitor_score
FROM play_match
INNER JOIN leagues ON league = league_id
INNER JOIN users ON referee = user_id
INNER JOIN teams AS local ON local_team = local.team_id
INNER JOIN teams AS visitor ON visitor_team = visitor.team_id
WHERE match_date = ? AND league= ?";

$stmt = mysqli_prepare($connection, $consult);

try {
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $date, $leagueId);
        mysqli_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());

        } else {
            http_response_code(404);
            echo json_encode(["error" => "No match found on this day"]);

        }
        
    } else {
        echo json_encode(["error" => "Error while preparing consult"]);
    }

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}
