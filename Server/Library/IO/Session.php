<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/10
 * Time: 上午11:01
 */

namespace IO;


class Session
{
    protected $_session_id;
    protected $_session_data;
    protected $_fd;

    public function __construct($session_id, $session_data = array()) {
        $this->_session_id = $session_id;
        $this->_session_data = $session_data;
    }

    public function get($key) {
        if (isset($this->_session_data[$key]))
            return $this->_session_data[$key];
        return NULL;
    }

    public function getSessionId() {
        return $this->_session_id;
    }

    public function set($key, $value) {
        $this->_session_data[$key] = $value;
    }

    public function bind($fd) {
        $this->$fd = $fd;
        return SessionManager::bind($fd, $this);
    }

    public function getFd() {
        return $this->_fd;
    }

    public function setMulti(array $keys, array $values) {
        foreach($keys as $k => $key) {
            $this->_session_data[$key] = $values[$k];
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close() {
        SessionManager::closeSession($this->_session_id);
    }


}