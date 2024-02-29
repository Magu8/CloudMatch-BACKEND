<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);
}

$leagueId = $_GET["league_id"];

$consult = "DELETE FROM leagues WHERE league_id = $leagueId";

try {

    $connection->query($consult);

    if ($connection->affected_rows > 0) {

        echo json_encode(["message" => "League successfully deleted"]);

    } else {

        http_response_code(404);
        echo json_encode(["error" => "League doesn't exist"]);

    }
} catch (\Throwable $th) {

    echo "An error ocurred" . throw $th;
    

}