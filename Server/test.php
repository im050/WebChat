<?php
$server = new swoole_http_server('0.0.0.0', 9501);

$server->on('request', function($server, $response){
    $response->end("1");
});

$server->start();