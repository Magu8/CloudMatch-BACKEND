<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

mysqli_set_charset($connection, "utf8mb4");

$teamId = $_GET["team_id"];

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["player_name"]) && isset($input_data["player_surname"]) && isset($input_data["age"])) {
    $name_input = $input_data["player_name"];
    $surname_input = $input_data["player_surname"];
    $age_input = $input_data["age"];
    $nickname_input = !isset($input_data["player_nickname"]) || $input_data["player_nickname"] === "" ? $surname_input : $input_data["player_nickname"];
    $photo_input = !isset($input_data["player_photo"]) || $input_data["player_photo"] === "" ? "https://th.bing.com/th/id/OIP.htfjaYinoTlfsNTLTJtL8QHaHa?pid=ImgDet&w=474&h=474&rs=1" : $input_data["photo"];

    $createPlayerConsult = "INSERT INTO players (player_nickname, player_name, player_surname, player_photo, age) VALUES (?, ?, ?, ?, ?)";
    $addPlayerConsult = "INSERT INTO teamPlayer_association (team, player, player_number) VALUES (?, ?, ?)";

    $numberCheckConsult = "SELECT * FROM teamPlayer_association WHERE team = ? AND player_number = ?";

    if ($age_input < 13) {
        http_response_code(400);
        echo json_encode(["error" => "Underaged player"]);

    } else {
        try {
            $createStmt = mysqli_prepare($connection, $createPlayerConsult);

            if ($createStmt) {
                mysqli_stmt_bind_param($createStmt, "ssssi", $nickname_input, $name_input, $surname_input, $photo_input, $age_input);
                mysqli_execute($createStmt);
                $playerId = mysqli_insert_id($connection);

                $playerNumber = random_int(1, 99);
                $result = null;

                do {
                    $checkStmt = mysqli_prepare($connection, $numberCheckConsult);
                    mysqli_stmt_bind_param($checkStmt, "ii", $teamId, $playerNumber);
                    mysqli_stmt_execute($checkStmt);
                    $result = mysqli_stmt_get_result($checkStmt);
                    mysqli_stmt_close($checkStmt);

                    if ($result && $result->num_rows > 0) {
                        $playerNumber = random_int(1, 99);

                    }
                } while ($result && $result->num_rows > 0);

                $addStmt = mysqli_prepare($connection, $addPlayerConsult);

                if ($addStmt) {
                    mysqli_stmt_bind_param($addStmt, "iii", $teamId, $playerId, $playerNumber);
                    mysqli_stmt_execute($addStmt);
                    http_response_code(201);
                    echo json_encode(["message" => "Player successfully added"]);

                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error while preparing the add consult"]);

                }
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error while preparing the create consult"]);

            }
            mysqli_stmt_close($createStmt);
            mysqli_stmt_close($addStmt);

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
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Some data is missing"]);

}