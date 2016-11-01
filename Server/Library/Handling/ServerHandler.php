<?php
/**
 * 通信信息处理类
 *
 * @author: memory<service@im050.com>
 */

namespace Handling;

use Auth\JWT;
use Storages\RecordStorage;
use Utils\PacketCreator;
use Client\User;
use Utils\Config;

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
                $recordStorage = RecordStorage::getInstance(1);

                if ($this->client->getClientStatus() == 0) {
                    $this->client->write($packet->setType('error')->setErrorCode('UNLOGIN')->toJSON());
                } else {
                    $message = trim(safeStr($content));

                    if ($message == '') {
                        return false;
                    }

                    $frame = array(
                        'message'=>$message,
                        'nickchen'=>$this->client->getUser()->nickchen,
                        'avatar'=>$this->client->getUser()->avatar,
                        'user_id'=>$this->client->getUser()->user_id,
                        'time'=>time()
                    );

                    $finalMessage = $packet->receiveMessage($frame);
                    $recordStorage->push($finalMessage);
                    $this->client->broadcast($finalMessage);
                }
                break;
            case 'ping':

                break;
            case 'login':
                $secret = Config::get('auth.jwt.secret');
                $access_token = $content->access_token;
                if (JWT::verify($access_token, $secret)) {
                    $payload = JWT::decode($access_token, $secret);
                    if (isset($payload->exp) && time() > $payload->exp) {
                        $msg = $packet->make('login', array('status' => false, 'msg' => '授权过期,请重新登录.'));
                    } else {
                        $this->client->setClientStatus(1);
                        $this->client->setUser(new User($payload));
                        $data = [
                            'status'=>true,
                            'msg'=>'登录成功!',
                            'user'=>[
                                'user_id'=>$payload->user_id,
                                'nickchen'=>$payload->nickchen
                            ]
                        ];
                        $msg = $packet->make('login', $data);
                    }
                } else {
                    $msg = $packet->make('login', array('status' => false, 'msg' => '授权错误!'));
                }
                //更新ClientStorage里的client
                $this->client->save();
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