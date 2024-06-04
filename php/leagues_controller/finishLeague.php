<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$endDate = $_GET["end_date"];

$endDateCheckConsult = "SELECT * FROM leagues WHERE end_date = ?";
$finishConsult = "UPDATE leagues SET ended = true WHERE end_date = ?";

try {

    $endStmt = mysqli_prepare($connection, $endDateCheckConsult);

    if ($endStmt) {
        mysqli_stmt_bind_param($endStmt, "s", $endDate);
        mysqli_stmt_execute($endStmt);
        $result = mysqli_stmt_get_result($endStmt);
        if ($result->num_rows > 0) {
            $finishStmt = mysqli_prepare($connection, $finishConsult);

            if ($finishStmt) {
                mysqli_stmt_bind_param($finishStmt, "s", $endDate);
                mysqli_stmt_execute($finishStmt);
                http_response_code(200);
                echo json_encode(["message" => "League finished"]);

            } else {
                echo json_encode(["message" => "Error while preparing the finish consult"]);

            }
        }
    } else {
        echo json_encode(["message" => "Error while preparing the end-date check consult"]);

    }

} catch (\Throwable $th) {
    echo "An error ocurred" . throw $th;

}