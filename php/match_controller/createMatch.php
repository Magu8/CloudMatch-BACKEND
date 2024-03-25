<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

//TODO change ARBITRATOR to REFEREE

$input_data = json_decode(file_get_contents("php://input"), true);

$leagueId = $_GET["league_id"];
$referee = $_GET["referee_id"];
$localTeam = $_GET["localTeam_id"];
$visitorTeam = $_GET["visitorTeam_id"];

if (isset($input_data["match_date"]) && isset($input_data["match_hour"])) {
    $matchDate = $input_data["match_date"];
    $matchHour = $input_data["match_hour"];

    $refereeCheck = "SELECT role FROM users WHERE user_id = ? AND role = 'Referee' ";
    $dateHourConflictCheck = "SELECT * FROM play_match WHERE match_date = ? AND match_hour = ?";

    $consult = "INSERT INTO play_match (league, match_date, match_hour, referee, localTeam, visitorTeam) VALUES (?, ?, ?, ?, ?, ?)";

    $stmtRefereeCheck = mysqli_prepare($connection, $refereeCheck);
    $stmtDateHourConflictCheck = mysqli_prepare($connection, $dateHourConflictCheck);
    $stmt = mysqli_prepare($connection, $consult);

    try {
        if ($stmtDateHourConflictCheck) {
            mysqli_stmt_bind_param($stmtDateHourConflictCheck, "ss", $matchDate, $matchHour);
            mysqli_execute($stmtDateHourConflictCheck);
            $checkDateHourConflictResults = mysqli_stmt_get_result($stmtDateHourConflictCheck);

            if (mysqli_num_rows($checkDateHourConflictResults) == 1) {
                http_response_code(409);
                echo json_encode(["error" => "Can't create a match on this day or hour"]);

            } else {
                if ($stmtRefereeCheck) {
                    mysqli_stmt_bind_param($stmtRefereeCheck, "i", $referee);
                    mysqli_stmt_execute($stmtRefereeCheck);
                    $checkRefereeResults = mysqli_stmt_get_result($stmtRefereeCheck);

                    if (mysqli_num_rows($checkRefereeResults) > 0) {
                        mysqli_stmt_bind_param($stmt, "issiii", $leagueId, $matchDate, $matchHour, $referee, $localTeam, $visitorTeam);

                        if (mysqli_stmt_execute($stmt)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Match day has been successfully created"]);

                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Execution failed"]);
                        }
                    } else {
                        http_response_code(404);
                        echo json_encode(["error" => "This user is not a referee"]);

                    }

                } else {
                    echo json_encode(["error" => "Error while preparing the refereeCheck consult"]);

                }
            }
        } else {
            echo json_encode(["error" => "Error while preparing the dateHourConflict consult"]);

        }

    } catch (\Throwable $th) {
        echo "An error ocurred" . throw $th;

    }

} else {
    http_response_code(400);
    echo json_encode(["error" => "Some data is missing"]);

}
