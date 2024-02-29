<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);
}

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["name"]) && isset($input_data["surname"]) && isset($input_data["age"])) {

    $name_input = $input_data["name"];
    $surname_input = $input_data["surname"];
    $age_input = $input_data["age"];
    $nickname_input = !isset($input_data["nickname"]) || $input_data["nickname"] === "" ? $surname_input : $input_data["nickname"];
    $photo_input = !isset($input_data["photo"]) ? "https://th.bing.com/th/id/OIP.htfjaYinoTlfsNTLTJtL8QHaHa?pid=ImgDet&w=474&h=474&rs=1" : $input_data["photo"];

    $consult = "INSERT INTO players (player_nickname, player_name, player_surname, player_photo, age) VALUES (?, ?, ?, ?, ?)";

    try {

        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "ssssi", $nickname_input, $name_input, $surname_input, $photo_input, $age_input);
            mysqli_execute($stmt);
            http_response_code(201);
            echo json_encode(["message" => "Player successfully added"]);

        } else {

            http_response_code(500);
            echo json_encode(["error" => "Error while preparing consult"]);

        }

    } catch (mysqli_sql_exception $ex) {

        $error_number = $ex->getCode();

        if ($error_number == 1062) {

            http_response_code(409);
            echo json_encode(["error" => "This player's nickname already exists"]);

        } else {

            http_response_code(500);
            echo json_encode(["error" => "Something went wrong: " . $ex->getMessage()]);

        }
    }
} else {

    http_response_code(400);
    echo json_encode(["error" => "Some data is missing"]);

}