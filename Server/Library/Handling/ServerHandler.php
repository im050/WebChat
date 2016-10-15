<?php
/**
 * 通信信息处理类
 *
 * @author: memory<service@im050.com>
 */

namespace Handling;
use IO\SessionManager;
use Utils\PacketCreator;

class ServerHandler
{

    private $frame = null;
    private $client = null;

    public function __construct() {}

    public function handlePacket($fd, $type, $content) {

        $packet = new PacketCreator();

//        if ($this->client->getClientStatus() == 0) {
//            $this->client->write($packet->setType('error')->setErrorCode('UNLOGIN')->toJSON());
//            return FALSE;
//        }

        switch($type) {
            case 'init':
                if ($this->client->getClientStatus() == 0) {

                }
                break;
            case 'send_message':
                $this->client->broadcast($packet->receiveMessage($content)->toJSON());
                break;
            case 'ping':
                $this->client->lastPingTime = time();
                break;
        }

    }


    public function hold($client, $frame) {
        $this->client = $client;
        $this->frame = $frame;

        $data = $frame->data;
        $data = json_decode($data);

        $fd = $frame->fd;
        $type = $data->type;

        if (!isset($data->content))
            $content = '';
        else
            $content = $data->content;

        $this->handlePacket($fd, $type, $content);
    }

}