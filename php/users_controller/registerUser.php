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

mysqli_set_charset($connection, "utf8mb4");

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["name"]) && isset($input_data["surname"]) && isset($input_data["email"]) && isset($input_data["password"]) && !empty($input_data["name"]) && !empty($input_data["surname"]) && !empty($input_data["email"]) && !empty($input_data["password"])) {
    $name_input = $input_data["name"];
    $surname_input = $input_data["surname"];
    $email_input = $input_data["email"];
    $pass_input = $input_data["password"];

    $hashed_pass = password_hash($pass_input, PASSWORD_DEFAULT);

    $consult = "INSERT INTO users (user_name, user_surname, email_address, password) VALUES (?, ?, ?, ?)";

    try {
        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $name_input, $surname_input, $email_input, $hashed_pass);
            mysqli_stmt_execute($stmt);
            http_response_code(201);
            echo json_encode(["message" => "User successfully created"]);
            mysqli_stmt_close($stmt);

        } else {
            echo json_encode(["message" => "Error while preparing consult"]);

        }
    } catch (mysqli_sql_exception $ex) {
        $error_number = $ex->getCode();

        if ($error_number == 1062) {
            http_response_code(409);
            echo json_encode(["message" => "This email address is already in use"]);

        } else {
            http_response_code(500);
            echo json_encode(["message" => "Something went wrong: " . $ex->getMessage()]);

        }
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Some data is missing"]);

}


