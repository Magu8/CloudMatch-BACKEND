<?php


require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);
}

$playerId = $_GET["player_id"];

$consult = "SELECT player_nickname, player_photo, player_name, player_surname, age, player_number, team_logo, team_name, member_since FROM players
INNER JOIN teamplayer_association ON player_id = player
INNER JOIN teams ON team = team_id
WHERE player_id = $playerId";

try {
    
    $result = $connection->query($consult);

    if ($result->num_rows > 0) {

        echo json_encode($result->fetch_assoc());

    } else {

        http_response_code(404);
        echo json_encode(["error" => "No player found"]);

    }

} catch (\Throwable $th) {

    http_response_code(500);
    echo "An error ocurred" . throw $th;
    
}