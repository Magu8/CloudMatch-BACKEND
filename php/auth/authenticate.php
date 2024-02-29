<?php


require_once'../../vendor/autoload.php';
require"../connection/connection_data.php";
require"secret/secret_key.php";


use Firebase\JWT\JWT;

$input_data = json_decode(file_get_contents("php://input"), true);

if (isset($input_data["email"]) && isset($input_data["password"])) {
    $email_input = $input_data["email"];
    $pass_input = $input_data["password"];

    $consult = "SELECT * FROM users WHERE email_address = ?";
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email_input);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($pass_input, $row["password"])) {
                $issuedAt = new DateTimeImmutable();
                $payload = [
                    "user_id" => $row["user_id"],
                    "user_name" => $row["user_name"],
                    "email_address" => $row["email_address"],
                    "role" => $row["role"],
                    "iat" => $issuedAt
                ];
                $token = JWT::encode($payload, $secret_key, 'HS256');
                echo json_encode(["token" => $token]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Wrong e-mail or password"]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Wrong e-mail or password"]);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["error" => "Error while preparing consult"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "E-mail and password are required"]);
}



