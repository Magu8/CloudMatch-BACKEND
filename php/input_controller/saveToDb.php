<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


require "db/redis.php";
require "../connection/connection_data.php";

$string = $redis->get("word");


$consult = "INSERT INTO pruebaredis (string) VALUES (?) ";

$stmt = mysqli_prepare($connection, $consult);

try {
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $string);
        mysqli_execute($stmt);
        echo json_encode(["message" => "did it work?"]);
        exit;
    }
} catch (Exception $e) {
    echo "somth went wrong" . $e->getMessage() . "";
}


