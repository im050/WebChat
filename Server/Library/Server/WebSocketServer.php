<?php
namespace Server;

use \swoole_websocket_server;
class WebSocketServer
{
    protected $port = 8888;
    protected $ip = '0.0.0.0';

    protected $server = null;

    public function __construct($ip = '', $port = '') {

        !empty($ip) && $this->ip = $ip;
        !empty($port) && $this->port = $port;



        $this->server = new swoole_websocket_server($this->ip, $this->port);

        $this->server->set(
            array(
                'heartbeat_check_interval' => 1,
                'heartbeat_idle_time' => 10
            )
        );

        $this->server->on('Open', array($this, 'onOpen'));
        $this->server->on('Message', array($this, 'onMessage'));
        $this->server->on('Close', array($this, 'onClose'));

    }

    public function onOpen($server, $request) {

    }

    public function onMessage($server, $frame) {

    }

    public function onClose($server, $fd) {

    }

    public function onTimer($server, $interval) {

    }

    public function onWorkerStart($server, $worker_id) {

    }

    public function start() {
        $this->server->start();
    }

}