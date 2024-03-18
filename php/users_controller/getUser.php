<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$userId = $_GET["user_id"];

$consult = "SELECT * FROM users WHERE user_id=$userId";

try {
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);

    } else {
        http_response_code(404);
        echo json_encode(["error" => "No user found"]);

    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;
    
}
