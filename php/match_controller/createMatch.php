<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);

$leagueId = $_GET["league_id"];
$arbitrator = $_GET["arbitrator_id"];
$localTeam = $_GET["localTeam_id"];
$visitorTeam = $_GET["visitorTeam_id"];

if (isset($input_data["match_date"]) && isset($input_data["match_hour"])) {
    $matchDate = $input_data["match_date"];
    $matchHour = $input_data["match_hour"];

    $arbitratorCheck = "SELECT role FROM users WHERE user_id = ? AND role = 'Arbitrator' ";
    $dateHourConflictCheck = "SELECT * FROM play_match WHERE match_date = ? AND match_hour = ?";

    $consult = "INSERT INTO play_match (league, match_date, match_hour, arbitrator, localTeam, visitorTeam) VALUES (?, ?, ?, ?, ?, ?)";

    $stmtArbitratorCheck = mysqli_prepare($connection, $arbitratorCheck);
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
                if ($stmtArbitratorCheck) {
                    mysqli_stmt_bind_param($stmtArbitratorCheck, "i", $arbitrator);
                    mysqli_stmt_execute($stmtArbitratorCheck);
                    $checkArbitratorResults = mysqli_stmt_get_result($stmtArbitratorCheck);

                    if (mysqli_num_rows($checkArbitratorResults) > 0) {
                        mysqli_stmt_bind_param($stmt, "issiii", $leagueId, $matchDate, $matchHour, $arbitrator, $localTeam, $visitorTeam);

                        if (mysqli_stmt_execute($stmt)) {
                            http_response_code(201);
                            echo json_encode(["message" => "Match has been successfully created"]);

                        } else {
                            http_response_code(500);
                            echo json_encode(["error" => "Execution failed"]);
                        }
                    } else {
                        http_response_code(404);
                        echo json_encode(["error" => "This user is not an arbitrator"]);

                    }

                } else {
                    echo json_encode(["error" => "Error while preparing the arbitratorCheck consult"]);

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
