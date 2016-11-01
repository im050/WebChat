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

    public function __construct()
    {
        if ($this->_storage == NULL) {
            $this->_storage = Storage::getInstance('ClientStorage');
        }
        if ($this->_cache == NULL) {
            $this->_cache = Cache::getInstance();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
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