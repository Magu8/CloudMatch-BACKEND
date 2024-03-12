<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");

require "db/redis.php";

$string = $_GET["string"];

$redis->set("word", $string);



