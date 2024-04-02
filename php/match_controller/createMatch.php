<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

$input_data = json_decode(file_get_contents("php://input"), true);

$leagueId = $_GET["league_id"];
$refereeId = $_GET["referee_id"];
$localId = $_GET["local_id"];
$visitorId = $_GET["visitor_id"];


if (isset($input_data["match_date"]) && isset($input_data["match_time"]) && $leagueId && $refereeId && $localId && $visitorId) {
    $matchDate_input = $input_data["match_date"];
    $matchTime_input = $input_data["match_time"];


    $refereeCheck = "SELECT role FROM users WHERE user_id = ? AND role = 'Referee' ";
    $dateTimeConflictCheck = "SELECT * FROM play_match WHERE match_date = ? AND match_time = ?";

    $consult = "INSERT INTO play_match (match_date, match_time, league, referee, local_team, visitor_team) VALUES (?, ?, ?, ?, ?, ?)";

    $stmtRefereeCheck = mysqli_prepare($connection, $refereeCheck);
    $stmtDateTimeConflictCheck = mysqli_prepare($connection, $dateTimeConflictCheck);
    $stmt = mysqli_prepare($connection, $consult);

    try {
        if ($stmtDateTimeConflictCheck) {
            mysqli_stmt_bind_param($stmtDateTimeConflictCheck, "ss", $matchDate_input, $matchTime_input);
            mysqli_execute($stmtDateTimeConflictCheck);
            $checkDateTimeConflictResults = mysqli_stmt_get_result($stmtDateTimeConflictCheck);

            if (mysqli_num_rows($checkDateTimeConflictResults) == 1) {
                http_response_code(409);
                echo json_encode(["message" => "Can't create a match on this day or Time"]);

            } else {
                if ($stmtRefereeCheck) {
                    mysqli_stmt_bind_param($stmtRefereeCheck, "i", $refereeId);
                    mysqli_stmt_execute($stmtRefereeCheck);
                    $checkRefereeResults = mysqli_stmt_get_result($stmtRefereeCheck);

                    if (mysqli_num_rows($checkRefereeResults) > 0) {
                        mysqli_stmt_bind_param($stmt, "ssiiii", $matchDate_input, $matchTime_input, $leagueId, $refereeId, $localId, $visitorId);

                        if (mysqli_stmt_execute($stmt)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Match day has been successfully created"]);

                        } else {
                            http_response_code(500);
                            echo json_encode(["message" => "Execution failed"]);
                        }
                    } else {
                        http_response_code(404);
                        echo json_encode(["message" => "This user is not a referee"]);

                    }

                } else {
                    echo json_encode(["message" => "Error while preparing the refereeCheck consult"]);

                }
            }
        } else {
            echo json_encode(["message" => "Error while preparing the dateTimeConflict consult"]);

        }

    } catch (\Throwable $th) {
        echo "An error ocurred" . throw $th;

    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Some data is missing"]);

}
