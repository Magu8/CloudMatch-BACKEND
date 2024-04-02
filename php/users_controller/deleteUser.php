<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$userId = $_GET["user_id"];

$consult = "DELETE FROM users WHERE user_id= $userId";

try {
    $connection->query($consult);

    if ($connection->affected_rows > 0) {
        echo json_encode(["message" => "User successfully deleted"]);

    } else {
        http_response_code(404);
        echo json_encode(["message" => "User doesn't exist"]);

    }
} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;

}
