<?php

require "db/redis.php";

$redis->set("clave","valorsito");

echo $redis->get("clave");