<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["team_name"])) {
    $team_name = $input_data["team_name"];
    
    $consult = "INSERT INTO teams (team_name) VALUES(?)";

    try {
        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $team_name);
            mysqli_stmt_execute($stmt);
            http_response_code(201);
            echo json_encode(["message" => "Team successfully created"]);

        } else {
            echo json_encode(["error" => "Error while preparing consult"]);

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
    echo json_encode(["error" => "Team-name is required"]);

}