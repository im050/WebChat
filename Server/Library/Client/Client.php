<?php
/**
 * 客户端
 *
 * @author: memory<service@im050.com>
 */
namespace Client;


class Client
{

    private $client_id;
    private $client_status = 0;
    private $fd = 0;
    private $lastPingTime = 0;
    private $server;
    private $user;

    public function __construct($client_id, $server) {
        $this->server = $server;
        $this->client_id = $client_id;
        $this->fd = $client_id;
    }

    public function getClientStatus() {
        return $this->client_status;
    }

    public function setClientStatus($status) {
        $this->client_status = $status;
    }

    public function getServer() {
        return $this->server->getServer();
    }

    public function writeObject($object) {
        $string = json_encode($object);
        $this->write($string);
    }

    public function write($string) {
        $this->getServer()->push($this->fd, $string);
    }

    public function broadcast($string) {
        $this->server->broadcast($string);
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
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