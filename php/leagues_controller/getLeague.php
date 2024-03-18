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

$leagueId = $_GET["league_id"];

$consult = "SELECT * FROM leagues WHERE league_id= $leagueId";

try {
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());

    } else {
        http_response_code(404);
        echo json_encode(["error" => "No league found"]);

    }

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;
    
}

