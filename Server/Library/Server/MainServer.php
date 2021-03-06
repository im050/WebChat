<?php
/**
 * 主服务器
 *
 * @author: memory<service@im050.com>
 */

namespace Server;

use Cache\Cache;
use Client\Client;
use Connections\DatabaseConnection;
use Connections\RedisConnection;
use Handling\RequestHandler;
use Handling\ServerHandler;
use Storages\ClientStorage;
use Utils\Config;
use Utils\PacketCreator;

class MainServer extends WebSocketServer
{

    private $clients = [];
    protected static $_instance = null;

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
    }

    public static function getInstance($ip = '', $port = '')
    {
        if (self::$_instance == null) {
            self::$_instance = new self($ip, $port);
        }
        return self::$_instance;
    }

    public function onTask($server, $task_id, $from_id, $data)
    {
        switch ($data['task_type']) {
            case "broadcast":
                $exclude_fd = $data['exclude_fd'];
                $string = $data['message_packet'];
                foreach ($this->server->connections as $fd) {
                    if ($this->isWebsocket($fd)) {
                        if (in_array($fd, $exclude_fd)) {
                            continue;
                        }
                        $this->server->push($fd, $string);
                    }
                }
                break;
            case "broadcast_room":
                $room_id = $data['room_id'];
                $exclude_fd = $data['exclude_fd'];
                $string = $data['message_packet'];
                log_message($room_id);
                $client_storage = ClientStorage::getInstance($room_id);
                $fd_list = $client_storage->allFd();
                foreach ($fd_list as $fd) {
                    if ($this->isWebsocket($fd)) {
                        if (in_array($fd, $exclude_fd))
                            continue;
                        $this->server->push($fd, $string);
                    }
                }
                break;
            case "send":
                $fd = $data['fd'];
                $message_packet = $data['message_packet'];
                if ($this->isWebsocket($fd)) {
                    $this->server->push($fd, $message_packet);
                }
                break;
        }
    }

    public function onTaskFinish($server, $task_id, $data)
    {

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
        if ($this->isWebsocket($fd)) {
            $fd_info = $this->server->connection_info($fd);
            $this->clients[$fd] = new Client($fd);
            $client_storage = ClientStorage::getInstance(1);
            $client_storage->push($this->clients[$fd]);
            log_message("WorkerID [{$server->worker_id}]: " . $fd_info['remote_ip'] . ":" . $fd_info['remote_port'] . " Connection.");
        }
    }

    /**
     * 消息接受事件
     *
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        $fd = $frame->fd;
        $server_handler = new ServerHandler();
        $server_handler->hold($this->clients[$fd], $frame);
        unset($server_handler);
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
        if ($this->isWebsocket($fd)) {
            $client = Client::get($fd);
            $room_id = $client->room_id;
            $client_storage = ClientStorage::getInstance($room_id);
            if ($client != null) {
                $user = $client->getUser();
                if ($user != null) {
                    $packet = new PacketCreator();
                    try {
                        $this->broadcast($packet->make('user_logout', array(
                            'user_id' => $user->user_id
                        )), array($client->fd));
                    } catch (\Exception $e) {
                        log_message("session closed.");
                    }
                    $client_storage->logout($user->user_id, $fd);
                    $username = $client->getUser()->username;
                    log_message("WorkerID [{$server->worker_id}]: 用户 [{$username}] 断开连接...");
                } else {
                    $client_storage->remove($fd);
                    $fd_info = $this->server->connection_info($fd);
                    log_message("WorkerID [{$server->worker_id}]: " . $fd_info['remote_ip'] . ":" . $fd_info['remote_port'] . " 断开连接");
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
    public function isWebsocket($fd)
    {
        $fd_info = $this->server->connection_info($fd);
        return ($fd_info['websocket_status'] == WEBSOCKET_STATUS_FRAME);
    }

    /**
     * 得到Swoole Websocket Server
     *
     * @return null|\swoole_websocket_server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * 全部广播消息
     *
     * @param $string
     */
    public function broadcast($string, $exclude_fd = array())
    {
        $data = [];
        $data['task_type'] = 'broadcast';
        $data['message_packet'] = $string;
        $data['exclude_fd'] = $exclude_fd;
        $this->server->task($data);
    }

    /**
     * 房间广播
     *
     * @param $room_id
     * @param $string
     * @param array $exclude_fd
     */
    public function broadcastRoom($room_id, $string, $exclude_fd = array())
    {
        $data = [];
        $data['task_type'] = "broadcast_room";
        $data['room_id'] = $room_id;
        $data['message_packet'] = $string;
        $data['exclude_fd'] = $exclude_fd;
        $this->server->task($data);
    }

    /**
     * Http请求事件
     * 当使用HTTP协议访问时触发该事件
     *
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        if (strpos($request->server['request_uri'], '.ico') !== FALSE) {
            return;
        }
        $request_handler = new RequestHandler($this->server, $request, $response);
        $request_handler->handleRequest();
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
        log_message("进程 [{$worker_id}] 正在启动...");
        Cache::init();
        log_message("初始化缓存模块...");
        DatabaseConnection::getInstance();
        if ($worker_id == 0) {
            log_message("正在清空缓存数据...");
            $redis = RedisConnection::getInstance();
            $redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
            while (($keys = $redis->scan($it, '', 1000))) {
                foreach ($keys as $key) {
                    if (!preg_match("/^record\_[0-9]+/", $key)) {
                        $redis->del($key);
                    }
                }
            }
            log_message("缓存数据清空完毕...");
        }
        log_message("进程 [{$worker_id}] 启动完毕.");
    }


    /**
     * 启动服务端
     */
    public function start()
    {
        try {
            log_message("服务端正在启动...");
            parent::start();
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }
}