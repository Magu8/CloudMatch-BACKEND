<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$leagueId = $_GET["league_id"];

$consult = "SELECT * FROM leagues WHERE league_id= ?";

try {
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $leagueId);
        mysqli_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows > 0) {
            echo json_encode(mysqli_fetch_assoc($result));

        } else {
            http_response_code(404);
            echo json_encode(["message" => "No league found"]);

        }
    }
    mysqli_stmt_close($stmt);

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}

