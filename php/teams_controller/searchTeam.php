<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}
if (isset($_GET['search_name']) && !empty($_GET['search_name'])) {

    $teamName = $_GET['search_name'] . '%';

    $consult = "SELECT team_name FROM teams WHERE team_name LIKE ?";

    try {
        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $teamName);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result->num_rows > 0) {
                echo json_encode(mysqli_fetch_assoc($result));

            } else {
                http_response_code(404);
                echo json_encode(["message" => "No results"]);

            }

        }
    } catch (\Throwable $th) {
        echo "An error ocurred" . throw $th;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "No results"]);
}