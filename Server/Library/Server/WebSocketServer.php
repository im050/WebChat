<?php
/**
 * WebSocket服务类
 *
 * @author: memory<service@im050.com>
 */
namespace Server;

use swoole_websocket_server;
use Utils\Config;

class WebSocketServer
{
    protected $port = 8888;
    protected $ip = '0.0.0.0';

    protected $server = null;

    public function __construct($ip = '', $port = '')
    {

        !empty($ip) && $this->ip = $ip;
        !empty($port) && $this->port = $port;

        $this->server = new swoole_websocket_server($this->ip, $this->port, SWOOLE_PROCESS);

        $this->server->set(
            Config::get('websocket.swoole_config')
        );

        $this->server->on('Open', array($this, 'onOpen'));
        $this->server->on('Message', array($this, 'onMessage'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->server->on('Request', array($this, 'onRequest'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onTaskFinish'));

    }

    public function onOpen($server, $request)
    {
        log_message('未调用 onOpen ...');
    }

    public function onMessage($server, $frame)
    {
        log_message('未调用 onMessage ...');
    }

    public function onClose($server, $fd)
    {
        log_message('未调用 onClose ...');
    }

    public function onTimer($server, $interval)
    {
        log_message('未调用 onTimer ...');
    }

    public function onRequest($request, $response)
    {
        log_message('未调用 onRequest ...');
    }

    public function onWorkerStart($server, $worker_id)
    {
        log_message('未调用 onWorkerStart ...');
    }

    public function onTask($server, $task_id, $from_id, $data)
    {
        log_message('未调用 onTask ...');
    }

    public function onTaskFinish($server, $task_id, $data)
    {
        log_message('未调用 onTaskFinish ...');
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function start()
    {
        $this->server->start();
    }

}