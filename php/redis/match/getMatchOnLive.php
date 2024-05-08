<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require "../db/redis.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

$match = [
    'league_id' => $redis->get('league_id'),
    'match_id' => $redis->get('match_id'),
    'local_id' => $redis->get('local_id'),
    'local_team' => $redis->get('local_team'),
    'local_score' => $redis->get('local_score'),
    'local_fouls' => $redis->get('local_fouls'),
    'visitor_id' => $redis->get('visitor_id'),
    'visitor_team' => $redis->get('visitor_team'),
    'visitor_score' => $redis->get('visitor_score'),
    'visitor_fouls' => $redis->get('visitor_fouls'),
    'TIME' => $redis->get('TIME'),
    'PERIOD' => $redis->get('PERIOD')
];

echo json_encode($match);