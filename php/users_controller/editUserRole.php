<?php


require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);

$userId = $_GET["user_id"];

if (isset($input_data["role"])) {
    $role = $input_data["role"];

    $consult = "UPDATE users SET role = ? WHERE user_id= ?";
   
    try {
        $stmt = mysqli_prepare($connection, $consult);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $role, $userId);

            if (mysqli_stmt_execute($stmt) && $stmt -> affected_rows > 0) {
                http_response_code(200);
                echo json_encode(["message" => "User successfully edited"]);

            } else {
                http_response_code(404);
                echo json_encode(["error" => "User doesn't exist"]);

            }
        } else {
            echo json_encode(["error" => "Error while preparing consult"]);

        }
    } catch (\Throwable $th) {
        echo "An error ocurred". throw $th;

    }

} else {
    http_response_code(400);
    echo json_encode(["error" => "Must write a role"]);
    
}
