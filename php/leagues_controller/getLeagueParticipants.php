<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

$leagueId = $_GET["league_id"];

if ($connection->connect_error) {
    die ("Failed to connect to the database: " . $connection->connect_error);

}

$consult = "SELECT team_id, team_logo, team_name, league_name AS 'league', start_date, end_date
FROM leagues
INNER JOIN participants ON league_id = league
INNER JOIN teams ON participant_team = team_id
WHERE league_id = $leagueId";

try {
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        
    } else {
        http_response_code(404);
        echo json_encode(["error" => "No participants found"]);

    }

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}