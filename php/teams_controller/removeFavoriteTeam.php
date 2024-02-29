<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);
}

$teamId = $_GET["favTeam_id"];
$userId = $_GET["user_id"];

$consult = "DELETE FROM favorite_teams WHERE user_id = $userId AND  favoriteTeam_id = $teamId";

try {

    $connection->query($consult);

    if ($connection->affected_rows > 0) {

        echo json_encode(["message" => "Team removed from favorites"]);

    } else {

        http_response_code(404);
        echo json_encode(["error" => "Team or user doesn't exist"]);

    }
} catch (\Throwable $th) {

    echo "An error ocurred" . throw $th;


}
