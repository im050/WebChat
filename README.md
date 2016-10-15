# WebChat
## 基于Swoole扩展+WebSocket开发的聊天室DEMO
> 为了保证通信时数据安全,拟通过php模拟http请求到httpserver后通知socketserver端,再以websocket方式返回结果信息给客户端.

### 更新记录:
#### 2016.10.13

* 增加Redis连接类
* 增加Storage类

#### 2016.10.12

* 增加了Request回调处理类，用于处理用户HTTP请求
* 去除无用代码，拟重写Session类
* 增加了对客户端的握手状态验证
* 重写MainServer对客户端对象的处理

#### 2016.10.11

* 开启新旅程

### 启动：
> 进入到Server目录下,执行 `php server.php`
