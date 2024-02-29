<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);
}


$teamId = $_GET["team_id"];

$consult = "SELECT * FROM teams WHERE team_id= $teamId";

try {

    $result = $connection->query($consult);

    if ($result->num_rows > 0) {

        echo json_encode($result->fetch_assoc());

    } else {

        http_response_code(404);
        echo json_encode(["error" => "No team found"]);

    }
} catch (\Throwable $th) {

    http_response_code(500);
    echo "An error ocurred" . throw $th;
    
}