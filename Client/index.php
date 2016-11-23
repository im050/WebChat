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
    <script src="static/js/jquery.nicescroll.min.js"></script>
    <link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="static/css/style.css" rel="stylesheet"/>
    <link href="static/css/iconfont.css" rel="stylesheet"/>
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/vue/2.0.3/vue.min.js"></script>
</head>
<body>
<section class="chat-navigation">
    <div class="menu top-menu">
        <ul>
            <!-- user -->
            <li id="menu_user"><span class="icon iconfont">&#xe6b8;</span></li>
            <!-- search -->
            <li id="menu_search">
                <span class="icon iconfont">&#xe6ac;</span>

            </li>
            <!-- message -->
            <li id="menu_message">
                <span class="icon iconfont">&#xe69b;</span>
                <span class="number-tip">1</span>
            </li>
        </ul>
    </div>
    <div class="menu bottom-menu">
        <ul>
            <!-- setting -->
            <li id="menu_setting"><span class="icon iconfont">&#xe6ae;</span></li>
            <!-- list -->
            <li id="menu_list"><span class="icon iconfont">&#xe699;</span></li>
        </ul>
    </div>
</section>
<div class="top-bar">
    Memory WebChat
</div>
<!-- sidebar -->
<div class="sidebar" id="menu_list_sidebar">
    <div class="sidebar-layer">
        <div id="room-list">
111111111
        </div>
    </div>
</div>

<div class="sidebar" id="menu_setting_sidebar">
    <div class="sidebar-layer">
        <div id="room-list">
22222222
        </div>
    </div>
</div>

<div class="sidebar" id="menu_message_sidebar">
    <div class="sidebar-layer">
        <div id="room-list">
<a href="javascript:alert('点我')">点我Click</a>
        </div>
    </div>
</div>

<div class="sidebar" id="menu_user_sidebar">
    <div class="sidebar-layer">
        <div id="room-list">
44444444
        </div>
    </div>
</div>

<div class="sidebar" id="menu_search_sidebar">
    <div class="sidebar-layer">
        <div>
            <a href="http://www.baidu.com/">哈哈是</a>
            <ul id="room_list">
                <li v-for="room in rooms">
                    {{ room.room_name }} <a href="javascript://" v-on:click="changeRoom(room.id)" >[进入]</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- online users -->
<div class="online-panel" id="online-list">
    <h4>Users<span>(Online: {{ user_count }} people)</span></h4>
    <ul class="online-list" >
        <li v-for="user in users" class="tips" :data-content="user.nickchen">
            <img :src="user.avatar" width="42" height="42" />
        </li>
    </ul>
</div>
<!-- main content -->
<div class="warp">
    <!-- chat message -->
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
    <!-- for user to send message -->
    <div class="chat-panel ">
        <div class="input-panel">
            <ul>
                <li><span class="icon iconfont"><a href="javascript:alert('click me')">&#xe61d;</a></span></li>
                <li><span class="icon iconfont">&#xe694;</span></li>
                <li><span class="icon iconfont">&#xe619;</span></li>
                <li><span class="icon iconfont">&#xe632;</span></li>
            </ul>
        </div>
        <div class="message-input input-group">
            <input type="text" name="msg" class="form-control">
            <span class="input-group-btn">
                <button name="send" class="btn btn-primary" type="button">Send</button>
            </span>
        </div>
    </div>
</div>
<!-- login container -->
<div class="login">
    <form name="login_form" method="post" action="login.php">
        <p><input placeholder="username" type="text" class="form-control" name="username"></p>

        <p><input placeholder="password" type="password" class="form-control" name="password"></p>

        <p><input type="button" name="login_btn" class="btn btn-primary" value="Login"/></p>
    </form>
</div>
<!-- scripts -->
<script src="static/js/server.js"></script>
<script src="static/js/chat.js"></script>
<script src="static/js/common.js"></script>
</body>
</html>