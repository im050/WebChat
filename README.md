# WebChat
## 基于Swoole扩展+WebSocket开发的聊天室DEMO

[![Join the chat at https://gitter.im/im050/Lobby](https://badges.gitter.im/im050/Lobby.svg)](https://gitter.im/im050/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
> 采用JWT进行用户授权验证,功能逐步完善中.

### 更新记录:
#### 2016.10.27

* 增加配置文件管理
* 增加聊天记录功能

#### 2016.10.26

* 简单处理用户登录过程
* 增加JWT授权验证过程
* 修复部分BUG

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
