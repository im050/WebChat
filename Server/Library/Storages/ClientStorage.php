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

    protected $_storage;
    private static $instance = null;
    protected $_cache = null;

    public function __construct($room_id)
    {
        if ($this->_storage == NULL) {
            $this->_storage = Storage::getInstance('ClientStorage_Room_' . $room_id);
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
        $list = $this->_storage->all();
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

    public function all_fd() {
        return $this->_storage->all();
    }

    public function push(Client $client) {
        $fd = $client->fd;
        $this->_storage->push($fd);
        $this->_cache->set('client_' . $fd, serialize($client));
    }

    public function remove($fd) {
        $this->_storage->del($fd);
        $this->_cache->del('client_' . $fd);
    }

    public function update(Client $client) {
        $fd = $client->fd;
        return $this->_cache->set('client_' . $fd, serialize($client));
    }

    public function get($fd) {
        return unserialize($this->_cache->get('client_' . $fd));
    }

}