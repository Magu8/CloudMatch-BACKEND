<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$leagueId = $_GET["league_id"];

$consult = "DELETE FROM leagues WHERE league_id = ?";

try {
    $stmt = mysqli_prepare($connection, $consult);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $leagueId);
        mysqli_execute($stmt);

        if ($connection->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(["message" => "League successfully deleted"]);

        } else {
            http_response_code(404);
            echo json_encode(["error" => "League doesn't exist"]);

        }
    }
    mysqli_stmt_close($stmt);

} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;

}