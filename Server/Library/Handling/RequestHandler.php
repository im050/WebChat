<?php
/**
 * Http协议请求处理类
 *
 * @author: memory<service@im050.com>
 */
namespace Handling;


use Storages\RecordStorage;

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
            default;
        }
    }
}