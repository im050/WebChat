<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/11
 * Time: 下午11:40
 */

namespace Server;


class HttpServer
{
    private $server;
    protected $ip = '0.0.0.0';
    protected $port = 9501;

    public function __construct()
    {
        $this->server = new \swoole_http_server($this->ip, $this->port);
        $this->server->on('request', array($this, 'onRequest'));
    }

    public function onRequest($request, $response) {

    }

    public function start() {
        $this->server->start();
    }

}