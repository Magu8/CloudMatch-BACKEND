<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);
}

$userId = $_GET["user_id"];
$teamId = $_GET["favTeam_id"];

$consult = "INSERT INTO favorite_teams (user, favorite_team) VALUES (? , ?)";

try {

    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "ii", $userId, $teamId);
        mysqli_stmt_execute($stmt);
        http_response_code(200);
        echo json_encode(["message" => "Team added to Favorites"]);

    } else {

        echo json_encode(["error" => "Error while preparing consult"]);

    }
} catch (\Throwable $th) {

    echo json_encode(["error" => "An error occurred: " . throw $th]);

}