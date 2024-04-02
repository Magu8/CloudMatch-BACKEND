<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$matchId = $_GET["match_id"];

$consult = "SELECT match_id, league_name, match_date, match_time, 
CONCAT(user_name, ' ', user_surname) AS referee, 
local.team_name AS local_team,
local_score,
local_fouls,
visitor.team_name AS visitor_team,
visitor_score,
visitor_fouls
FROM play_match
INNER JOIN leagues ON league = league_id
INNER JOIN users ON referee = user_id
INNER JOIN teams AS local ON local_team = local.team_id
INNER JOIN teams AS visitor ON visitor_team = visitor.team_id
WHERE match_id = $matchId";

try {
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());

    } else {
        http_response_code(404);
        echo json_encode(["error" => "No match found"]);

    }

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;
}

