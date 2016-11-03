<?php
/**
 * 通信信息处理类
 *
 * @author: memory<service@im050.com>
 */

namespace Handling;

use Auth\JWT;
use Storages\ClientStorage;
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
            //初始化
            case 'init':
                if ($this->client->getClientStatus() == 0) {

                }
                break;
            //发送消息
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
            //获取在线列表
            case 'online_list':
                    $clientStorage = ClientStorage::getInstance(1);
                    $clients = $clientStorage->all();
                    $data = [];
                    foreach($clients as $client) {
                        $user = $client->getUser();
                        if ($user == null)
                            continue;
                        $data[] = [
                            'nickchen'=>$user->nickchen,
                            'fd'=>$client->fd,
                            'avatar'=>$user->avatar,
                            'user_id'=>$user->user_id
                        ];
                    }
                    $msg = $packet->make("online_list", $data);
                    $this->client->write($msg);
                break;
            //用户登录
            case 'login':
                $secret = Config::get('auth.jwt.secret');
                $access_token = $content->access_token;
                if (JWT::verify($access_token, $secret)) {
                    $payload = JWT::decode($access_token, $secret);
                    if (isset($payload->exp) && time() > $payload->exp) {
                        $msg = $packet->make('login', array('status' => false, 'msg' => '授权过期,请重新登录.'));
                    } else {
                        $clientStorage = ClientStorage::getInstance();
                        //判断该用户是否登录状态
                        if (($fd = $clientStorage->isLogin($payload->user_id)) !== FALSE) {
                            $this->client->getServer()->close($fd);
                        }
                        //设置登录标记
                        $clientStorage->setLogin($payload->user_id, $this->client->fd);
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
                        $server = $this->client->getServer();
                        $fdInfo = $server->connection_info($fd);
                        //登录成功,通知所有人
                        $user_login = $packet->make('user_login', array(
                            'nickchen'=>$payload->nickchen,
                            'avatar'=>$payload->avatar,
                            'fd'=>$this->client->fd,
                            'user_id'=>$payload->user_id
                        ));

                        $this->client->broadcast($user_login);
                        print_ln("WorkerID [{$server->worker_id}]: " . $fdInfo['remote_ip'].":".$fdInfo['remote_port'] . " 用户 [{$payload->username}] 登录了服务器");
                    }
                } else {
                    $msg = $packet->make('login', array('status' => false, 'msg' => '授权错误!'));
                }
                //更新ClientStorage里的client
                $this->client->save();
                $this->client->write($msg);

                break;
            //退出
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