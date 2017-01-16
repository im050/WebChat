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
                log_message("进程 [".$this->server->worker_id."] 读取聊天记录");
                $id = isset($this->request->get['room_id']) ? $this->request->get['room_id'] : 1;
                $record_storage = RecordStorage::getInstance($id);
                $record_data = ($record_storage->range());
                $callback = isset($this->request->get['callback']) ? $this->request->get['callback'] : '';
                $html = '[';
                foreach ($record_data as $val) {
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
                log_message("WorkerID: " . $this->server->worker_id . " FD: " . $this->request->fd);
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