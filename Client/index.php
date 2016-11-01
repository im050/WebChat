<!DOCTYPE html>
<html charset="utf-8">
<head>
    <title>Chat Online</title>
    <meta charset="utf-8"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-app-status-bar-style" content="black" />
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0,  minimum-scale=1.0, maximum-scale=1.0" />
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
    <link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/vue/2.0.3/vue.min.js"></script>

    <style>
        body {
            margin: 0;
            background-color: #ebebeb;
            padding: 0px;
            font: normal 12px "Microsoft yahei";
        }

        .warp {
            margin: 0 auto;
        }

        .top {
            background: #f1f1f1;
            border-bottom: #ddd 1px solid;
            text-align: center;
            font: normal 16px/50px "Microsoft yahei";
            height: 50px;
            top: 30px;
            line-height: 50px;
        }

        .chat {
            width: auto;
            left: 10px;
            right: 10px;
            position: absolute;
            top: 50px;
            bottom: 50px;
            -webkit-box-sizing: border-box;
            overflow: auto;
        }

        .chat-panel {
            position: fixed;
            bottom: 0px;
            height: 50px;
            border-top: #ddd 1px solid;
            width: 100%;
            background: #f4f4f6;
        }

        .login {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #ececec;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300px;
            height: 200px;
            z-index: 999;
            padding: 20px;
            margin: -150px 0 0 -150px;
        }

        .message-list {
            margin: 0;
            padding: 0;
        }

        .message-list li {
            list-style: none;
            margin: 10px 0;

            position: relative;

        }

        .message-list .face {
            width: 32px;
            height: 32px;
            position: absolute;
        }

        .content {
            display: inline-block;
            width: auto;
            background: #fff;
            max-width: 60%;
            word-break: break-all;
            word-wrap: break-word;
            color: #000;
            line-height: 32px;
            padding: 0 10px;
            border-radius: 5px;
            position: relative;
        }

        .owner .face {
            right: 0px;
        }

        .owner .message-layer {
            text-align: right;
            margin: 0 42px 0 0;
        }

        .owner .content {
            background: #a2e759;
        }

        .content:after {
            content: "";
            border-color: transparent #fff transparent  transparent ;
            top:  10px;
            border-style: solid;
            border-width: 5px;
            position: absolute;
            left: -9px;
        }

        .owner .content:after {
            content: "";
            border-color: transparent transparent transparent #a2e759;
            right: -9px;
            left: auto;
        }

        .nickchen {
            padding-left: 3px;
            margin-bottom: 2px;
            color: #666;
            font-weight: bold;
        }

        .time {
            display: none;
        }

        .message-layer {
            margin: 0 0 0 42px;
            width: auto;
        }

        .clearfix {
            zoom: 1;
            visibility: hidden;
            clear:both;
        }

    </style>
</head>
<body>
<div class="warp">
    <div class="top">
        Hello Memory
    </div>
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
    <div class="chat-panel input-group">
        <input type="text" name="msg" class="form-control">
            <span class="input-group-btn">
                <button name="send" class="btn btn-default" type="button">Send</button>
            </span>
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