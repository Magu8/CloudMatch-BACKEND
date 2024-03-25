<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);

}

//TODO Check if there's already a member with the same number in the team, so it creates another random number

$teamId = $_GET["team_id"];
$playerId = $_GET["player_id"];

$playerNumber = random_int(1, 99);

$consult = "INSERT INTO teamPlayer_association (team, player, player_number) VALUES (?, ?, ?)";

try {
    $stmt = mysqli_prepare($connection, $consult);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iii", $teamId, $playerId, $playerNumber);
        mysqli_execute($stmt);
        http_response_code(200);
        echo json_encode(["message" => "Player successfully added to team"]);

    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error while preparing consult"]);
    }

} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    echo json_encode(["error" => "Something went wrong: " . $ex->getMessage()]);

}


