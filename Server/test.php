<?php
class Test1 {
    public static $hello  = self::class;

    public $world = self::class;

    public static function getName() {
        $class = self::$hello;
        echo $class . "\r\n";
    }

    public function getWorld() {
        echo $this->world . "\r\n";
    }
}

class Test2 extends  Test1 {
    //public static $hello = "Test2";
    public $world = "Test2";


}

/* 通过方法输出 */

Test1::getName(); // Test1
Test1::$hello = 'Tes22 ' . " \r\n";
Test2::getName(); // Test1
/* 直接输出 */
echo Test1::$hello . "\r\n";  // Test1

echo Test1::$hello . "\r\n";  // Test1
echo Test2::$hello . "\r\n";  // Test2
echo "====================\r\n";
/* function echo */
$t1 = new Test1();
$t2 = new Test2();

$t1->getWorld();
$t2->getWorld();

echo $t1->world . "\r\n";
echo $t2->world . "\r\n";


/*
$server = new swoole_websocket_server('0.0.0.0', 8888, SWOOLE_PROCESS);

$server->set(
    array(
        'worker_num' => 3
    )
);

$clients = [];

$string = "abcd";
$server->on('open', function($server, $request) {
    echo "Connection: " . $request->fd . "\r\n";
    global $clients;
    global $string;
    $string .= "1";
    $clients[$request->fd] = $server;
    //echo memory_get_usage() . "\r\n";
    //echo $server->reactor_num;
    echo $string . "\r\n";
    echo "Clients Count : " . count($clients). " Worker_id :" . $server->worker_id . "\r\n";
    //print_r($server);
});

$server->on('message', function($server, $frame){
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

$server->start();*/