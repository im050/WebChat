<?php
/**
 * 客户端
 *
 * @author: memory<service@im050.com>
 */
namespace Client;


use Storages\ClientStorage;

class Client
{
    /**
     * 客户端ID
     * 此处目前记录文件描述符
     * @var int
     */
    private $client_id;

    /**
     * 客户端状态
     * 用来表示登录状态
     * @var int
     */
    private $client_status = 0;

    /**
     * 文件描述符
     * @var int
     */
    private $fd = 0;

    /**
     * 主通信服务
     * @var \Server\MainServer
     */
    private $server;

    /**
     * 客户端用户
     * @var \Client\User
     */
    private $user = null;

    /**
     * Client constructor.
     * @param string $client_id 文件描述符
     * @param \Server\MainServer $server 主服务器
     */
    public function __construct($client_id, $server) {
        $this->server = $server;
        $this->client_id = $client_id;
        $this->fd = $client_id;
    }

    /**
     * 获得客户端状态
     * @return int
     */
    public function getClientStatus() {
        return $this->client_status;
    }

    /**
     * 设置客户端状态
     * @param $status
     */
    public function setClientStatus($status) {
        $this->client_status = $status;
    }

    /**
     * 获得主服务的websocket_server
     * @return null|\swoole_websocket_server
     */
    public function getServer() {
        return $this->server->getServer();
    }

    /**
     * 发送数据
     * @param array $object
     */
    public function writeObject($object) {
        $string = json_encode($object);
        $this->write($string);
    }

    /**
     * 发送数据
     * @param string $string
     */
    public function write($string) {
        try {
            $this->getServer()->push($this->fd, $string);
        }catch (\Exception $e) {
            print_ln("FD:[{$this->fd}] 发送消息失败.");
        }
    }

    /**
     * 广播消息
     * @param string $string
     */
    public function broadcast($string) {
        $this->server->broadcast($string);
    }

    /**
     * 设置用户
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * 获取当前客户端对应用户
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * 更新ClientStorage里的Client
     * @return boolean
     */
    public function save() {
        $cs = ClientStorage::getInstance();
        return $cs->update($this);
    }

    public function __set($name, $value) {
        if (isset($this->$name))
            $this->$name = $value;
        else
            throw new \Exception("Set attributes failed.");
    }

    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return NULL;
    }

}