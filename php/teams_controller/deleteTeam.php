<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);
}

$teamId = $_GET["team_id"];

$consult = "DELETE FROM teams WHERE team_id= $teamId";


try {

    $connection->query($consult);

    if ($connection->affected_rows > 0) {

        echo json_encode(["message" => "Team successfully deleted"]);

    } else {

        http_response_code(404);
        echo json_encode(["error" => "Team doesn't exist"]);

    }
} catch (\Throwable $th) {

    echo "An error ocurred" . throw $th;
    

}
