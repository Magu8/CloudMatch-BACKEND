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

$userId = $_GET["user_id"];

$consult = "SELECT team_id, team_name, team_logo FROM favorite_teams
INNER JOIN teams
ON favorite_team = team_id
WHERE user = ? ";

try {
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;

            }
            echo json_encode($rows);

        } else {
            http_response_code(404);
            echo json_encode(["message" => "No favorites yet"]);

        }
    } else {
        echo json_encode(["message" => "Error while preparing the consult"]);

    }
} catch (\Throwable $th) {
    http_response_code(500);
    echo "An error ocurred" . throw $th;

}

