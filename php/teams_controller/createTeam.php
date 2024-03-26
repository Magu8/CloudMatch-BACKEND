<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}


$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["team_name"]) && isset($input_data["team_delegate"]) && !empty($input_data["team_name"]) && !empty($input_data["team_delegate"]) ) {
    $name_input = $input_data["team_name"];
    $delegate_input = $input_data["team_delegate"];
    $logo_input = !isset($input_data["team_logo"]) || $input_data["team_logo"] === "" ? "https://icon-library.com/images/basketball-icon-png/basketball-icon-png-1.jpg" : $input_data["team_logo"];
    
    $createTeamConsult = "INSERT INTO teams (team_name, team_logo) VALUES(?, ?)";
    $addTeamDelegateConsult = "INSERT INTO teamDelegate_association(team, team_delegate) VALUES (?,?)";

    $updateUserConsult = "UPDATE users SET role = 'Team Delegate' WHERE user_id = ?";
   
    try {
        $createStmt = mysqli_prepare($connection, $createTeamConsult);
        if ($createStmt) {
            mysqli_stmt_bind_param($createStmt, "ss", $name_input, $logo_input);
            mysqli_stmt_execute($createStmt);
            $teamId = mysqli_insert_id($connection);

            $addStmt = mysqli_prepare($connection, $addTeamDelegateConsult);

            if ($addStmt) {
                mysqli_stmt_bind_param($addStmt, "ii", $teamId, $delegate_input);
                mysqli_stmt_execute($addStmt);

                $updateStmt = mysqli_prepare($connection, $updateUserConsult);

                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "i", $delegate_input);
                    mysqli_stmt_execute($updateStmt);
                    http_response_code(201);
                    echo json_encode(["message" => "Team successfully created"]);
                    
                } else {
                    echo json_encode(["error" => "Error while preparing the update consult"]);

                }
                
            } else {
                echo json_encode(["error" => "Error while preparing the add consult"]);

            }
            
        }  else {
            echo json_encode(["error" => "Error while preparing the create consult"]);

        }

    } catch (mysqli_sql_exception $ex) {
        $error_number = $ex->getCode();

        if ($error_number == 1062) {
            http_response_code(409);
            echo json_encode(["error" => "This team-name is already taken"]);

        } else {
            http_response_code(500);
            echo json_encode(["error" => "Something went wrong: " . $ex->getMessage()]);

        }
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Some data is missing"]);

}