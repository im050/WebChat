<?php
/**
 * WebSocket服务类
 *
 * @author: memory<service@im050.com>
 */
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

        $this->server = new swoole_websocket_server($this->ip, $this->port, SWOOLE_PROCESS);

        $this->server->set(
            array(
                'heartbeat_check_interval' => 60,
                'heartbeat_idle_time' => 600,
                'worker_num' => 4
            )
        );

        $this->server->on('Open', array($this, 'onOpen'));
        $this->server->on('Message', array($this, 'onMessage'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->server->on('Request', array($this, 'onRequest'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));

    }

    public function onOpen($server, $request) {

    }

    public function onMessage($server, $frame) {

    }

    public function onClose($server, $fd) {

    }

    public function onTimer($server, $interval) {

    }

    public function onRequest($request, $response) {

    }

    public function onWorkerStart($server, $worker_id) {

    }

    public function getServer() {
        return $this->server;
    }

    public function getIp() {
        return $this->ip;
    }

    public function getPort() {
        return $this->port;
    }

    public function start() {
        $this->server->start();
    }

}