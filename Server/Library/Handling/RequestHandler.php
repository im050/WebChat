<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/12
 * Time: 下午9:32
 */

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
        //测试用,未完成
        $packet = new PacketCreator();
        $string = $packet->receiveMessage("Gateway run")->toJSON();
        foreach($this->server->connections as $fd) {
            $fdInfo = $this->server->connection_info($fd);
            if ($fdInfo['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                $this->server->push($fd, $string);
            }
        }
    }
}