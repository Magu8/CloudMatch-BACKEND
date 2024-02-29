<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to the database: " . $connection->connect_error);
}

$teamId = $_GET["team_id"];

$consult = "SELECT player_id, player_number, player_nickname, member_since, age, player_photo
FROM teamplayer_association
INNER JOIN players ON player = player_id
INNER JOIN teams ON team = team_id
WHERE team = $teamId";

try {

    $result = $connection->query($consult);

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()){ 
            
            $rows[] = $row;

        }
    } else {

        http_response_code(404);
        echo json_encode(["error" => "No player found"]);

    }

    echo json_encode($rows);
    
} catch (\Throwable $th) {

    http_response_code(500);
    echo "An error ocurred" . throw $th;
    
}