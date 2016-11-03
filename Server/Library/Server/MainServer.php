<?php
/**
 * 主服务器
 *
 * @author: memory<service@im050.com>
 */

namespace Server;

use Handling\RequestHandler;
use Handling\ServerHandler;
use Client\Client;
use Storages\ClientStorage;
use Utils\Config;

class MainServer extends WebSocketServer
{
    protected $port = 8888;
    protected $ip = '0.0.0.0';
    private $_server_handler = null;
    private $clients = [];

    public function __construct($ip = '', $port = '')
    {
        if ($ip == '')
            $ip = Config::get('websocket.host', '0.0.0.0');

        if ($port == '') {
            $port = Config::get('websocket.port');
        }

        parent::__construct($ip, $port);
        $this->_server_handler = new ServerHandler();

    }

    public function onOpen($server, $request)
    {
        $fd = $request->fd;
        if ($this->is_websocket($fd)) {
            $fdInfo = $this->server->connection_info($fd);
            $this->clients[$fd] = new Client($fd, $this);
            $clientStorage = ClientStorage::getInstance(1);
            $clientStorage->push($this->clients[$fd]);
            print_ln("WorkerID [{$server->worker_id}]: " . $fdInfo['remote_ip'].":".$fdInfo['remote_port'] . " Connection.");
        }
    }

    public function onMessage($server, $frame) {
        $fd = $frame->fd;
        $this->_server_handler->hold($this->clients[$fd], $frame);
    }

    public function onClose($server, $fd)
    {
        if ($this->is_websocket($fd)) {
            $clientStorage = ClientStorage::getInstance(1);
            $client = $clientStorage->get($fd);
            if ($client != null) {
                $user = $client->getUser();
                if ($user != null) {
                    $username = $client->getUser()->username;
                    print_ln("WorkerID [{$server->worker_id}]: 用户 [{$username}] 断开连接...");
                } else {
                    $fdInfo = $this->server->connection_info($fd);
                    print_ln("WorkerID [{$server->worker_id}]: " . $fdInfo['remote_ip'] . ":" . $fdInfo['remote_port'] . " 断开连接");
                }
                $clientStorage->remove($fd);
            }
        }
    }

    public function is_websocket($fd) {
        $fdInfo = $this->server->connection_info($fd);
        return ($fdInfo['websocket_status'] == WEBSOCKET_STATUS_FRAME);
    }

    public function getServer() {
        return $this->server;
    }

    public function broadcast($string) {
        foreach($this->server->connections as $fd) {
            if ($this->is_websocket($fd)) {
                $this->server->push($fd, $string);
            }
        }
    }

    public function onRequest($request, $response) {
        if (strpos($request->server['request_uri'], '.ico') !== FALSE) {
            return;
        }
        $requestHandler = new RequestHandler($this->server, $request, $response);
        $requestHandler->handleRequest();
    }

    public function onWorkerStart($server, $worker_id)
    {
        //print_r($server);
        print_ln("进程 [{$worker_id}] 启动成功.");
        //parent::onWorkerStart($server, $worker_id); // TODO: Change the autogenerated stub
    }


    public function start()
    {
        print_ln("服务端启动成功...");
        print_ln("监听端口: {$this->port}");
        parent::start(); // TODO: Change the autogenerated stub
    }
}