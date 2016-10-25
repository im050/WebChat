<?php
$server = new swoole_websocket_server('0.0.0.0', 8888, SWOOLE_PROCESS);

$server->set(
    array(
        'worker_num' => 1
    )
);

$clients = 0;

$server->on('open', function($server, $request) {
    echo $server->worker_pid . " / " . $server->worker_id . "\r\n";
});

$server->on('message', function($server, $frame) {
    global $clients;
    echo "Server: " . $server->worker_id . " FD:" .$frame->fd . " clients:" . ($clients++);
    echo "\r\n";
    //echo memory_get_usage() . "\r\n";
    //echo "Receive: " . $frame->data . "\r\n";
});

$server->on('close', function($server, $fd){
    echo $fd . " Closed.\r\n";
});

$server->on('request', function($request, $response) use($server){
    //var_dump($server->connections);
    $count = 0;
    if (strpos($request->server['request_uri'], '.ico') !== FALSE) {
        return;
    }
    foreach($server->connections as $fd) {
        $count ++;
        $fdInfo = $server->connection_info($fd);
        if ($fdInfo['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
            echo "对 $fd 发送了一条数据\r\n";
            $server->push($fd,"{type:'receive_message', content:'Gateway bind!'}");
        }
    }
    echo "count $count \r\n";

    $response->end("Hello!");
});

$server->start();