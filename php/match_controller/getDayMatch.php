<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["match_date"])) {

    $date = $input_data["match_date"];

    $consult = "SELECT league_name, match_date, match_hour, 
    CONCAT(user_name, ' ', user_surname) AS arbitrator,
    local_teams.team_name AS local_team,
    score_local,
    faults_local,
    visitor_teams.team_name AS visitor_team,
    score_visitor,
    faults_visitor
    FROM play_match
    INNER JOIN leagues ON league = league_id
    INNER JOIN users ON arbitrator = user_id
    INNER JOIN teams AS local_teams ON localTeam = local_teams.team_id
    INNER JOIN teams AS visitor_teams ON visitorTeam = visitor_teams.team_id
    WHERE match_date = ?";

    $stmt = mysqli_prepare($connection, $consult);

    try {
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $date);
            mysqli_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }
                echo json_encode($rows);

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
} else {
    http_response_code(400);
    echo json_encode(["error" => "Date is necessary"]);

}