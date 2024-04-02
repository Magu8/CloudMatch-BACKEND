<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}
if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);

$userId = $_GET["user_id"];

if (isset($input_data["role"]) && !empty($input_data["role"] && $userId)) {
    $role_input = $input_data["role"];

    $consult = "UPDATE users SET role = ? WHERE user_id= ?";

    try {
        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $role_input, $userId);
            mysqli_stmt_execute($stmt);
            http_response_code(200);
            echo json_encode(["message" => "User successfully edited"]);

        } else {
            echo json_encode(["message" => "Error while preparing consult"]);

        }
    } catch (\Throwable $th) {
        echo "An error ocurred" . throw $th;

    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Some data is missing"]);

}
