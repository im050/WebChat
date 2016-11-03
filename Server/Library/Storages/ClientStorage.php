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
    private static $instance = null;
    protected $_cache = null;

    public function __construct($room_id)
    {
        if ($this->_online_fd == NULL) {
            $this->_online_fd = Storage::getInstance('ClientStorage_fd_Room_' . $room_id);
        }
        if ($this->_cache == NULL) {
            $this->_cache = Cache::getInstance();
        }
    }

    public static function getInstance($room_id = 1) {
        if (self::$instance == null) {
            self::$instance = new self($room_id = 1);
        }
        return self::$instance;
    }

    public function all() {
        $list = $this->_online_fd->all();
        $client_keys = [];
        foreach($list as $fd) {
            $client_keys[] = "client_{$fd}";
        }
        $clients = $this->_cache->mget($client_keys);
        array_walk($clients, function(&$val){
            $val = unserialize($val);
        });
        return $clients;
    }

    public function allFd() {
        return $this->_online_fd->all();
    }

    public function push(Client $client) {
        $fd = $client->fd;
        $this->_online_fd->push($fd);
        $this->_cache->set('client_' . $fd, serialize($client));
    }

    public function remove($fd) {
        $this->_online_fd->del($fd);
        $this->_cache->del('client_' . $fd);
    }

    /**
     * 判断用户是否登录
     *
     * @param string $user_id
     * @return boolean|int
     */
    public function isLogin($user_id) {
        $fd = $this->_cache->get('user_'.$user_id);
        if (intval($fd) > 0) {
            return $fd;
        } else {
            return false;
        }
    }

    public function setLogin($user_id, $fd) {
        $this->_cache->set('user_'.$user_id, $fd);
    }

    public function update(Client $client) {
        $fd = $client->fd;
        return $this->_cache->set('client_' . $fd, serialize($client));
    }

    public function get($fd) {
        return unserialize($this->_cache->get('client_' . $fd));
    }

}