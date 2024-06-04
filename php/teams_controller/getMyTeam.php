<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die ("Failed to connect to the database: " . $connection->connect_error);

}

$delegateId = $_GET["delegate_id"];

$consult = "SELECT team_id, team_name, team_logo, CONCAT (user_name, ' ', user_surname) AS team_delegate 
FROM teams
INNER JOIN teamdelegate_association ON team_id = team
INNER JOIN users ON team_delegate = user_id
WHERE team_delegate =  $delegateId";

try {
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());

    } else {
        http_response_code(404);
        echo json_encode(["message" => "No team found"]);

    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}
