<?php
/**
 * 客户端
 *
 * @author: memory<service@im050.com>
 */
namespace Client;


use Server\MainServer;
use Storages\ClientStorage;
use Cache\Cache;

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

    private $server = null;

    /**
     * 客户端用户
     * @var \Client\User
     */
    private $user = null;

    /**
     * 当前客户端所在房间ID
     * @var int
     */
    private $room_id = 0;

    /**
     * Client constructor.
     * @param string $client_id 文件描述符
     */
    public function __construct($client_id)
    {
        $this->server = MainServer::getInstance();
        $this->client_id = $client_id;
        $this->fd = $client_id;
        $this->room_id = 1;
    }

    public static function get($fd) {
        return msgpack_unpack(Cache::get('client_' . $fd));
    }


    /**
     * 获得客户端状态
     * @return int
     */
    public function getClientStatus()
    {
        return $this->client_status;
    }

    /**
     * 设置客户端状态
     * @param $status
     */
    public function setClientStatus($status)
    {
        $this->client_status = $status;
    }

    /**
     * 获得主服务的websocket_server
     * @return null|\swoole_websocket_server
     */
    public function getServer()
    {
        return $this->server->getServer();
    }

    /**
     * 发送数据
     * @param array $object
     */
    public function writeObject($object)
    {
        $string = json_encode($object);
        $this->write($string);
    }

    /**
     * 发送数据
     * @param string $string
     */
    public function write($string)
    {
        try {
            //改用Task
            $data = [];
            $data['task_type'] = 'send';
            $data['fd'] = $this->fd;
            //log_message("data[fd] = " . $this->fd);
            $data['message_packet'] = $string;
            $this->getServer()->task($data);
            //$this->getServer()->push($this->fd, $string);
        } catch (\Exception $e) {
            log_message("FD:[{$this->fd}] 发送消息失败.");
        }
    }

    /**
     * 广播消息
     * @param string $string
     */
    public function broadcast($string, $exclude_fd = array())
    {
        $this->server->broadcast($string, $exclude_fd);
    }

    public function broadcastRoom($room_id, $string, $exclude_fd = array())
    {
        $this->server->broadcastRoom($room_id, $string, $exclude_fd);
    }

    /**
     * 设置用户
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * 获取当前客户端对应用户
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getRoomId()
    {
        return $this->room_id;
    }

    public function setRoomId($room_id)
    {
        $this->room_id = $room_id;
    }

    public function changeRoom($room_id) {
        $cs = ClientStorage::getInstance($this->room_id);
        $cs->changeRoom($this, $room_id);
    }

    /**
     * 更新ClientStorage里的Client
     * @return boolean
     */
    public function save()
    {
        $cs = ClientStorage::getInstance($this->room_id);
        return $cs->update($this);
    }

    public function __set($name, $value)
    {
        if (isset($this->$name))
            $this->$name = $value;
        else
            throw new \Exception("Set attributes failed.");
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return NULL;
    }

}