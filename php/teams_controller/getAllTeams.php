<?php

require "../connection/connection_data.php";

if ($connection->connect_error) {
    die("Failed to connect to data base" . $connection->connect_error);
}

$consult = "SELECT * FROM teams";

try {

    $result = $connection->query($consult);

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            
            $rows[] = $row;
            
        }
    } else {

        http_response_code(404);
        echo json_encode(["error" => "No teams found"]);

    }

    echo json_encode($rows);

} catch (\Throwable $th) {

    http_response_code(500);
    echo "An error ocurred" . throw $th;
    
}