<?php
namespace Handling;


use Utils\PacketCreator;

class RequestHandler
{

    private $server, $request, $response;

    public function __construct($server, $request, $response)
    {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    public function handleRequest() {
        $route = strtolower(trim($this->request->server['request_uri'], '/'));
        switch($route) {
            //绑定客户端信息
            case 'bind':

                break;
            default;
        }
    }
}