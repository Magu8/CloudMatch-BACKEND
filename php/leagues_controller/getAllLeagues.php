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

$consult = "SELECT * FROM leagues WHERE ended != 1";

try {
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            echo json_encode($rows);

        } else {
            http_response_code(404);
            echo json_encode(["message" => "No leagues found"]);

        }
    }
    mysqli_stmt_close($stmt);

} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}

