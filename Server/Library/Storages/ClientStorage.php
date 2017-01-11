<?php
/**
 * 客户端仓库类
 *
 * @author: memory<service@im050.com>
 */

namespace Storages;


use Cache\Cache;
use Client\Client;

class ClientStorage
{

    //用于存放fd
    protected $_online_fd = null;
    protected static $_instance = [];
    protected $_prefix = 'ClientStorage_fd_Room_';
    protected $_name = '';
    protected $_room_id = '';

    public function __construct($room_id)
    {
        $this->_room_id = $room_id;
        if ($this->_online_fd == NULL) {
            $this->_online_fd = Storage::getInstance($this->getName());
        }
    }

    public function getName($room_id = '') {
        if (empty($room_id))
            $room_id = $this->_room_id;
        return $this->_prefix . $room_id;
    }

    public static function getInstance($room_id = 1)
    {
        if (!isset(self::$_instance[$room_id])) {
            self::$_instance[$room_id] = new self($room_id = 1);
        }
        return self::$_instance[$room_id];
    }

    public function all()
    {
        $list = $this->_online_fd->all();
        $client_keys = [];
        foreach ($list as $fd) {
            $client_keys[] = "client_{$fd}";
        }
        $clients = Cache::getMulti($client_keys);
        array_walk($clients, function (&$val) {
            $val = msgpack_unpack($val);
        });
        return $clients;
    }

    public function allFd()
    {
        return $this->_online_fd->all();
    }

    public function push(Client $client)
    {
        $fd = $client->fd;
        $this->_online_fd->push($fd);
        Cache::set('client_' . $fd, msgpack_pack($client));
    }

    public function changeRoom($fd, $room_id) {
        $source = $this->getName();
        $destination = $this->getName($room_id);
        $this->_online_fd->smove($source, $destination, $fd);
    }

    public function remove($fd)
    {
        $this->_online_fd->del($fd);
        Cache::del('client_' . $fd);
    }

    /**
     * 判断用户是否登录
     *
     * @param string $user_id
     * @return boolean|int
     */
    public function isLogin($user_id)
    {
        $fd = Cache::get('user_' . $user_id);
        if (intval($fd) > 0) {
            return $fd;
        } else {
            return false;
        }
    }

    public function setLogin($user_id, $fd)
    {
        Cache::set('user_' . $user_id, $fd);
    }

    public function logout($user_id, $fd)
    {
        $this->remove($fd);
        Cache::del('user_' . $user_id);
    }

    public function update(Client $client)
    {
        $fd = $client->fd;
        return Cache::set('client_' . $fd, msgpack_pack($client));
    }

    public function get($fd)
    {
        return msgpack_unpack(Cache::get('client_' . $fd));
    }

}