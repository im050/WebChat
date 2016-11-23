<?php
/**
 * Http协议请求处理类
 *
 * @author: memory<service@im050.com>
 */
namespace Handling;


use Connections\DatabaseConnection;
use Storages\RecordStorage;
use \PDO;
class RequestHandler
{

    private $server, $request, $response;

    public function __construct($server, $request, $response)
    {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * 分段处理HTTP请求
     */
    public function handleRequest()
    {
        $route = strtolower(trim($this->request->server['request_uri'], '/'));
        switch ($route) {
            //绑定客户端信息
            case 'bind':
                var_dump($this->server);
                break;
            case 'record':
                print_ln("进程 [".$this->server->worker_id."] 读取聊天记录");
                //sleep(1);
                $id = isset($this->request->get['room_id']) ? $this->request->get['room_id'] : 1;
                $recordStorage = RecordStorage::getInstance($id);
                $recordData = ($recordStorage->range());
                $callback = isset($this->request->get['callback']) ? $this->request->get['callback'] : '';
                $html = '[';
                foreach ($recordData as $val) {
                    $html .= $val . ",";
                }
                $html = rtrim($html, ",");
                $html .= "]";
                $this->response->end($callback . "(" . $html . ")");
                break;
            case 'rooms':
                $db = DatabaseConnection::getInstance();
                $sql = "SELECT * FROM wc_rooms";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->ajaxReturn($result, 'jsonp');
                break;
            case 'test':
                //print_r($this->request);
                print_ln("WorkerID: " . $this->server->worker_id . " FD: " . $this->request->fd);
                //sleep(1);
                break;
        }
    }

    public function ajaxReturn($data, $type = 'json') {
        if (!is_string($data)) {
            $data = json_encode($data);
        }
        switch($type) {
            case 'json':
                $this->response->end($data);
                break;
            case 'jsonp':
                $callback = isset($this->request->get['callback']) ? $this->request->get['callback'] : '';
                if ($callback == '') {
                    $this->response->end("{msg:\"参数错误\"}");
                } else {

                    $this->response->end($callback . "(" . $data . ")");
                }
                break;
        }
    }
}