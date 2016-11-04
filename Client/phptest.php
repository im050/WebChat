<?php
$redis = new Redis();

$redis->connect('127.0.0.1', 6379);

$match = "key:*";
$it = 0;

print_r($redis->scan($it, $match, 1000));