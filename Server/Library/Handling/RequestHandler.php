<?php
/**
 * Http协议请求处理类
 *
 * @author: memory<service@im050.com>
 */
namespace Handling;


use Storages\RecordStorage;
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
                var_dump($this->server);
                break;
            case 'record':
                $recordStorage = RecordStorage::getInstance(1);
                $recordData = ($recordStorage->range());
                $callback = $this->request->get['callback'];
                $html = '[';
                foreach($recordData as $val) {
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