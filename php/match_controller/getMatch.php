<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$matchId = $_GET["match_id"];

$consult = "SELECT league_name, match_date, match_hour, 
CONCAT(user_name, ' ', user_surname) AS referee,
local_teams.team_name AS local_team,
score_local,
faults_local,
visitor_teams.team_name AS visitor_team,
score_visitor,
faults_visitor
FROM play_match
INNER JOIN leagues ON league = league_id
INNER JOIN users ON referee = user_id
INNER JOIN teams AS local_teams ON localTeam = local_teams.team_id
INNER JOIN teams AS visitor_teams ON visitorTeam = visitor_teams.team_id
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

