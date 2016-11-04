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
use Cache\Cache;

class MainServer extends WebSocketServer
{
    protected $port = 8888;
    protected $ip = '0.0.0.0';
    private $_server_handler = null;
    private $clients = [];

    /**
     * MainServer constructor.
     * @param string $ip
     * @param string $port
     */
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

    /**
     * socket连接事件
     * 握手成功后触发该事件
     *
     * @param $server
     * @param $request
     */
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

    /**
     * 消息接受事件
     *
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame) {
        $fd = $frame->fd;
        $this->_server_handler->hold($this->clients[$fd], $frame);
    }

    /**
     * 连接关闭事件
     * 包括主动关闭和被动关闭都会触发该事件
     *
     * @param $server
     * @param $fd
     */
    public function onClose($server, $fd)
    {
        if ($this->is_websocket($fd)) {
            $clientStorage = ClientStorage::getInstance(1);
            $client = $clientStorage->get($fd);
            if ($client != null) {
                $user = $client->getUser();
                if ($user != null) {
                    $clientStorage->logout($user->user_id, $fd);
                    $username = $client->getUser()->username;
                    print_ln("WorkerID [{$server->worker_id}]: 用户 [{$username}] 断开连接...");
                } else {
                    $clientStorage->remove($fd);
                    $fdInfo = $this->server->connection_info($fd);
                    print_ln("WorkerID [{$server->worker_id}]: " . $fdInfo['remote_ip'] . ":" . $fdInfo['remote_port'] . " 断开连接");
                }
            }
        }
    }

    /**
     * 根据FD文件描述符
     * 判断该连接是否是websocket客户端
     *
     * @param $fd
     * @return bool
     */
    public function is_websocket($fd) {
        $fdInfo = $this->server->connection_info($fd);
        return ($fdInfo['websocket_status'] == WEBSOCKET_STATUS_FRAME);
    }

    /**
     * 得到Swoole Websocket Server
     *
     * @return null|\swoole_websocket_server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * 广播消息
     *
     * @param $string
     */
    public function broadcast($string) {
        foreach($this->server->connections as $fd) {
            if ($this->is_websocket($fd)) {
                $this->server->push($fd, $string);
            }
        }
    }

    /**
     * Http请求事件
     * 当使用HTTP协议访问时触发该事件
     *
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response) {
        if (strpos($request->server['request_uri'], '.ico') !== FALSE) {
            return;
        }
        $requestHandler = new RequestHandler($this->server, $request, $response);
        $requestHandler->handleRequest();
    }

    /**
     * 启动事件
     * 当进程启动时触发该事件
     *
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        print_ln("进程 [{$worker_id}] 启动成功.");
        Cache::init();
    }


    /**
     * 启动服务端
     */
    public function start()
    {
        print_ln("服务端启动成功...");
        print_ln("监听端口: {$this->port}");
        parent::start();
    }
}