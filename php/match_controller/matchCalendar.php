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

$leagueId = $_GET["league_id"];

$consult = "SELECT match_id, league_name, match_date, match_time,
local.team_logo AS local_team_logo,
local.team_name AS local_team,
visitor.team_logo AS visitor_team_logo,
visitor.team_name AS visitor_team
FROM play_match
INNER JOIN leagues ON league = league_id
INNER JOIN teams AS local ON local_team = local.team_id
INNER JOIN teams AS visitor ON visitor_team = visitor.team_id
WHERE league = ?";

try {
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $leagueId);
        mysqli_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            echo json_encode($rows);

        } else {
            http_response_code(404);
            echo json_encode(["message" => "No matches in this league"]);

        }
    }
    mysqli_stmt_close($stmt);

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;
}