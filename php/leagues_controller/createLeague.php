<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../connection/connection_data.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();

}

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

$input_data = json_decode(file_get_contents("php://input"), true);



if (isset($input_data["league_name"]) && isset($input_data["start_date"]) && isset($input_data["end_date"]) && !empty($input_data["league_name"]) && !empty($input_data["start_date"]) && !empty($input_data["end_date"])) {
    $leagueName = $input_data["league_name"];
    $start = $input_data["start_date"];
    $end = $input_data["end_date"];
    $leagueLogo = $input_data["league_logo"];
   


    $current = date("Y-m-d");
    $start_timestamp = strtotime($start);
    $end_timestamp = strtotime($end);
    $current_date_timestamp = strtotime($current);


    if ($start_timestamp < $current_date_timestamp || $end_timestamp < $current_date_timestamp || $end_timestamp < $start_timestamp) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid dates"]);

    } else {

            $consult = "INSERT INTO leagues (league_name, league_logo, start_date, end_date) VALUES (?, ?, ?, ?)";

            try {
                $stmt = mysqli_prepare($connection, $consult);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssss", $leagueName, $leagueLogo, $start, $end);
                    mysqli_execute($stmt);
                    http_response_code(201);
                    echo json_encode(["message" => "League succesfully created"]);

                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Something went wrong: " . $ex->getMessage()]);

                }
                mysqli_stmt_close($stmt);

            } catch (\Throwable $th) {
                echo "An error ocurred" . throw $th;
            }

    }


} else {
    http_response_code(400);
    echo json_encode(["message" => "Some data is missing"]);

}