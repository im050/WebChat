<!DOCTYPE html>
<html charset="utf-8">
<head>
    <title>Chat Online</title>
    <meta charset="utf-8"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-app-status-bar-style" content="black"/>
    <meta name="apple-touch-fullscreen" content="YES"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,  minimum-scale=1.0, maximum-scale=1.0"/>
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
    <link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="static/css/style.css" rel="stylesheet"/>
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/vue/2.0.3/vue.min.js"></script>
</head>
<body>
<section class="chat-navigation">
    <div class="menu"><span class="glyphicon glyphicon-th-list"></span></div>
</section>
<div class="top-bar">
    Hello Memory
</div>
<div class="side-bar" id="online-list">
    <h4>Users<span>(当前房间在线人数: {{ user_count }})</span></h4>
    <ul class="online-list" >
        <li v-for="user in users" class="tips" :data-content="user.nickchen">
            <img :src="user.avatar" width="42" height="42" />
        </li>
    </ul>
</div>
<div class="warp">

    <div class="chat">
        <ul id="msg_list" class="message-list">
            <li v-for="msg in list" :class="{ owner : isOwner(msg.user_id) }">
                <div class="face">
                    <img :src="msg.avatar" width="32" height="32"/>
                </div>
                <div class="message-layer">
                    <div class="nickchen">{{ msg.nickchen }}</div>
                    <div class="content">{{ msg.message }}</div>
                    <div class="time">{{ msg.time }}</div>
                </div>
            </li>
        </ul>
    </div>
    <div class="clearfix"></div>
    <div class="chat-panel ">
        <div class="message-input input-group">
            <input type="text" name="msg" class="form-control">
            <span class="input-group-btn">
                <button name="send" class="btn btn-primary" type="button">Send</button>
            </span>
        </div>
    </div>
</div>
<div class="login">
    <form name="login_form" method="post" action="login.php">
        <p><input placeholder="username" type="text" class="form-control" name="username"></p>

        <p><input placeholder="password" type="password" class="form-control" name="password"></p>

        <p><input type="button" name="login_btn" class="btn btn-primary" value="Login"/></p>
    </form>
</div>
<script src="static/js/server.js"></script>
<script src="static/js/chat.js"></script>
<script src="static/js/common.js"></script>
</body>
</html>