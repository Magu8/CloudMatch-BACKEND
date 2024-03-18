<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$token = $_GET["TOKEN"];

if ($redis->set("token", $token)) {
    echo json_encode(["message" =>"token saved"]);
} else {
    echo json_encode(["message" =>"token not saved"]);
}


