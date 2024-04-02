<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);
}

$consult = "SELECT * FROM teams";

try {
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $rows[] = $row;

        }
        echo json_encode($rows);
        
    } else {
        http_response_code(404);
        echo json_encode(["message" => "No teams found"]);

    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}