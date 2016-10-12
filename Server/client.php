<?php

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function($cli) {
    if ($cli->isConnected()) {
        echo "yes!";
    }
    $cli->send(json_encode(array('type'=>"send_message", 'content'=>'hahaha!')));
});

$client->on("receive", function($cli, $data = ""){
    //$data = $cli->recv(); //1.6.10+ 不需要
    if(empty($data)){
        $cli->close();
        echo "closed\n";
    } else {
        echo "received: $data\n";
        sleep(1);
        $cli->send("hello\n");
    }
});

$client->on("close", function($cli){
    //$cli->close(); // 1.6.10+ 不需要
    echo "close\n";
});

$client->on("error", function($cli){
    exit("error\n");
});
$client->connect('127.0.0.1', 9502, 10);
