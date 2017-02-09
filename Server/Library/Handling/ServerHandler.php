<?php
/**
 * 通信信息处理类
 *
 * @author: memory<service@im050.com>
 */

namespace Handling;

use Auth\JWT;
use Cache\Cache;
use Client\User;
use Storages\ClientStorage;
use Storages\RecordStorage;
use Utils\Config;
use Utils\PacketCreator;

class ServerHandler
{

    private $frame = null;
    private $client = null;

    public function __construct()
    {
        $this->packet = new PacketCreator();
    }

    public function handlePacket($type, $content)
    {
        $method = "_{$type}";
        if (method_exists($this, $method)) {
            return $this->$method($content);
        } else {
            log_message("不存在的数据分析方法:" . $type);
            return false;
        }
    }

    protected function _ping() {
        return true;
    }

    /**
     * 切换频道
     *
     * @param $content
     */
    protected function _change_room($content) {
        $room_id = $content->room_id;
        $this->client->changeRoom($room_id);
        log_message("用户 " . $this->client->user->username . " 切换频道 [{$room_id}]");
    }

    /**
     * 发送消息
     *
     * @param $content
     * @return bool|void
     */
    protected function _send_message($content)
    {
        $packet = $this->packet;
        $packet->clear();
        $message = $content->message;
        if (stripos($message, '/') === 0 && $this->client->getUser()->username == 'lin050') {
            $message = explode(" ", $message);
            $args = [];
            foreach ($message as $key => $val) {
                if ($key == 0)
                    continue;
                $args[] = $val;
            }
            switch ($message[0]) {
                case '/flushall':
                    Cache::flushAll();
                    break;
            }
            $this->client->write($packet->make('pop_message', array('message' => '命令执行成功!')));
            return;
        }
        $record_storage = RecordStorage::getInstance($this->client->room_id);
        if ($this->client->getClientStatus() == 0) {
            $this->client->write($packet->setType('error')->setErrorCode('UNLOGIN')->toJSON());
        } else {
            $message = trim(($content->message));
            if ($message == '') {
                return false;
            }
            $frame = array(
                'message' => $message,
                'nickchen' => $this->client->getUser()->nickchen,
                'avatar' => $this->client->getUser()->avatar,
                'user_id' => $this->client->getUser()->user_id,
                'time' => time()
            );
            $final_message = $packet->receiveMessage($frame);
            //将信息存放到redis
            $record_storage->push($final_message);
            $this->client->broadcastRoom($this->client->getRoomId(), $final_message);
        }
    }

    /**
     * 获取在线列表
     *
     * @param $content
     */
    protected function _online_list($content)
    {

        $packet = $this->packet;
        $packet->clear();

        if (!isset($content->room_id)) {
            $msg = $packet->error("请求在线列表错误");
            $this->client->write($msg);
        }

        $client_storage = ClientStorage::getInstance($content->room_id);
        $clients = $client_storage->all();
        $data = [];
        foreach ($clients as $client) {
            $user = $client->getUser();
            if ($user == null)
                continue;
            $data[] = [
                'nickchen' => $user->nickchen,
                'fd' => $client->fd,
                'avatar' => $user->avatar,
                'user_id' => $user->user_id
            ];
        }
        $msg = $packet->make("online_list", $data);
        $this->client->write($msg);
    }

    /**
     * 登录处理
     *
     * @param $content
     */
    protected function _login($content)
    {
        $packet = $this->packet;
        $packet->clear();
        $secret = Config::get('auth.jwt.secret');
        $access_token = $content->access_token;
        if (JWT::verify($access_token, $secret)) {
            $payload = JWT::decode($access_token, $secret);
            if (isset($payload->exp) && time() > $payload->exp) {
                $msg = $packet->make('login', array('status' => false, 'msg' => '授权过期,请重新登录.'));
            } else {
                $client_storage = ClientStorage::getInstance(1);
                //判断该用户是否登录状态
                if (($fd = $client_storage->isLogin($payload->user_id)) !== FALSE) {
                    $this->client->getServer()->push($fd, $packet->make('pop_message', array('message' => '您的账号在别处登录.')));
                    $this->client->getServer()->close($fd);
                }
                //设置登录标记
                $client_storage->setLogin($payload->user_id, $this->client->fd);
                $this->client->setClientStatus(1);
                $this->client->setUser(new User($payload));
                //加入房间1
                $this->client->setRoomId(1);
                $data = [
                    'status' => true,
                    'msg' => '登录成功!',
                    'user' => [
                        'user_id' => $payload->user_id,
                        'nickchen' => $payload->nickchen
                    ]
                ];
                $msg = $packet->make('login', $data);
                $server = $this->client->getServer();
                $fd = $this->client->fd;
                $fd_info = $server->connection_info($fd);
                //登录成功,通知所有人
                $user_login = $packet->make('user_login', array(
                    'nickchen' => $payload->nickchen,
                    'avatar' => $payload->avatar,
                    'fd' => $this->client->fd,
                    'user_id' => $payload->user_id
                ));
                $this->client->broadcast($user_login);//, array($this->client->fd));
                log_message("WorkerID [{$server->worker_id}]: " . $fd_info['remote_ip'] . ":" . $fd_info['remote_port'] . " 用户 [{$payload->username}] 登录了服务器");
            }
        } else {
            $msg = $packet->make('login', array('status' => false, 'msg' => '授权错误!'));
        }
        //更新ClientStorage里的client
        $this->client->save();
        $this->client->write($msg);
    }

    /**
     * 填充并分解数据
     *
     * @param $client
     * @param $frame
     */
    public function hold($client, $frame)
    {
        $this->client = $client;
        $this->frame = $frame;
        $data = $frame->data;
        $data = json_decode($data);
        if ($data == null) {
            log_message("接收到非法的数据:" . $frame->data);
            return;
        }
        if (isset($data->type)) {
            $type = $data->type;
            if (!isset($data->content))
                $content = '';
            else
                $content = $data->content;
            $this->handlePacket($type, $content);
        } else {
            log_message("接收到未知的json数据");
        }
    }

    public function __destruct()
    {
        //log_message("消息处理完毕!");
        // TODO: Implement __destruct() method.
    }

}