<?php
/**
 * 通信信息处理类
 *
 * @author: memory<service@im050.com>
 */

namespace Handling;

use Auth\JWT;
use Utils\PacketCreator;
use Client\User;

class ServerHandler
{

    private $frame = null;
    private $client = null;

    public function __construct()
    {
    }

    public function handlePacket($fd, $type, $content)
    {

        $packet = new PacketCreator();

        switch ($type) {
            case 'init':
                if ($this->client->getClientStatus() == 0) {

                }
                break;
            case 'send_message':
                if ($this->client->getClientStatus() == 0) {
                    $this->client->write($packet->setType('error')->setErrorCode('UNLOGIN')->toJSON());
                } else {
                    $msg = array(
                        'message'=>$content,
                        'nickchen'=>$this->client->getUser()->nickchen
                    );
                    $this->client->broadcast($packet->receiveMessage($msg));
                }
                break;
            case 'ping':

                break;
            case 'login':
                $secret = 'memory';
                $access_token = $content->access_token;
                if (JWT::verify($access_token, $secret)) {
                    $payload = JWT::decode($access_token, $secret);
                    if (isset($payload->exp) && time() > $payload->exp) {
                        $msg = $packet->make('login', array('status' => false, 'msg' => '授权过期,请重新登录.'));
                    } else {
                        $this->client->setClientStatus(1);
                        $this->client->setUser(new User());
                        $this->client->getUser()->nickchen = $payload->nickchen;
                        $this->client->getUser()->username = $payload->username;

                        $msg = $packet->make('login', array('status' => true, 'msg' => '登录成功!'));
                    }
                } else {
                    $msg = $packet->make('login', array('status' => false, 'msg' => '授权错误!'));
                }
                $this->client->write($msg);
                break;

            case 'quit':
                $this->client->getServer()->close($fd);
                break;
        }

    }


    public function hold($client, $frame)
    {
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