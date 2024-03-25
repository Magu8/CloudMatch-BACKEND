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

$userId = $_GET["user_id"];
$teamId = $_GET["team_id"];

$findUserConsult = "SELECT user_name FROM users WHERE user_id = ?";
$checkUserRole = "SELECT user_name FROM users WHERE user_id = ? AND role = 'Team Delegate'";

$findTeamConsult = "SELECT team_name FROM teams WHERE team_id = ?";
$checkTeamDelegate = "SELECT * FROM teams WHERE team_id = ? AND team_delegate IS NOT NULL";

$updateTeam = "UPDATE teams SET team_delegate = ? WHERE team_id = ?";
$updateUser = "UPDATE users SET role = 'Team Delegate' WHERE user_id = ?";



try {
    $stmtUser = $connection->prepare($findUserConsult);
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $userResult = $stmtUser->get_result();

    $stmtUserRoleCheck = $connection->prepare($checkUserRole);
    $stmtUserRoleCheck->bind_param("i", $userId);
    $stmtUserRoleCheck->execute();
    $roleCheckResult = $stmtUserRoleCheck->get_result();

    if ($roleCheckResult->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "This user is already a Team Delegate"]);

    } else {
        if ($userResult->num_rows > 0) {
            $stmtTeam = $connection->prepare($findTeamConsult);
            $stmtTeam->bind_param("i", $teamId);
            $stmtTeam->execute();
            $teamResult = $stmtTeam->get_result();

            $stmtTeamDelegateCheck = $connection->prepare($checkTeamDelegate);
            $stmtTeamDelegateCheck->bind_param("i", $teamId);
            $stmtTeamDelegateCheck->execute();
            $teamDelegateCheck = $stmtTeamDelegateCheck->get_result();

            if ($teamDelegateCheck->num_rows > 0) {
                http_response_code(409);
                echo json_encode(["error" => "This team has already a Team Delegate"]);

            } else {
                if ($teamResult->num_rows > 0) {
                    $stmtTeamUpdate = $connection->prepare($updateTeam);
                    $stmtTeamUpdate->bind_param("ii", $userId, $teamId);

                    $stmtUserUpdate = $connection->prepare($updateUser);
                    $stmtUserUpdate->bind_param("i", $userId);

                    if ($stmtTeamUpdate->execute() && $stmtUserUpdate->execute()) {
                        http_response_code(200);
                        echo json_encode(["message" => "Team Delegate assigned to Team"]);

                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Something went wrong"]);

                    }
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Team doesn't exist"]);

                }
            }
        } else {
            http_response_code(404);
            echo json_encode(["error" => "User doesn't exist"]);

        }

    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(["error" => "An error occurred: " . $th->getMessage()]);

}
