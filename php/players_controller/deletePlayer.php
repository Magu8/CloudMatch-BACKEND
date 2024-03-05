<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$playerId = $_GET["player_id"];

$consult = "DELETE FROM players WHERE player_id= $playerId";

try {
    $connection->query($consult);

    if ($connection->affected_rows > 0) {
        echo json_encode(["message" => "Player successfully deleted"]);

    } else {
        http_response_code(404);
        echo json_encode(["error" => "Player doesn't exist"]);

    }
} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;

}