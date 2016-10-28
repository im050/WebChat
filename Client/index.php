<!DOCTYPE html>
<html charset="utf-8">
<head>
    <title>Chat Online</title>
    <meta charset="utf-8"/>
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
    <link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js"></script>
    <style>
        .chat {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            padding: 15px;
            -webkit-box-sizing: border-box;
            margin-top: 20px;
            overflow: auto;
            margin-bottom: 30px;
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
    </style>
</head>
<body>
<div class="container">
    <div class="chat">
        <ul id="msg_list">

        </ul>
    </div>
    <div class="input-group">
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
<script type="text/javascript">
    $(function () {

        var recordUrl = 'http://localhost:8888/record/';
        var storage = window.localStorage;

        function loadMessageRecord(room_id) {
            $.ajax({
                url: recordUrl,
                dataType: 'jsonp',
                data: {
                    room_id : room_id
                },
                type: 'get',
                success: function(msg){
                    var length = msg.length;
                    for(var i = (length - 1); i>=0; i--) {
                        var nickchen = msg[i].content.nickchen;
                        var content = msg[i].content.message;
                        var msg_time = new Date(parseInt(msg[i].content.time * 1000)).format("yyyy-MM-dd h:m:s");
                        $("#msg_list").append("<li>" + nickchen + ":" + content + " 时间:" + msg_time + "</li>");
                    }
                }
            });
        }

        if (window.WebSocket) {
            var socket_address = 'ws://127.0.0.1:8888';
            var socket = new WebSocket(socket_address);
            var heartPacketInterval = null;
            socket.onopen = function () {
                if (storage.getItem("access_token") != '') {
                    socket.login(storage.getItem('access_token'));
                }
                heartPacketInterval = setInterval(function () {
                    socket.send(JSON.stringify({type: 'ping'}));
                }, 5000);
            }

            WebSocket.prototype.login = function (token) {
                this.send(JSON.stringify({type: 'login', content: {access_token: token}}));
            }

            socket.onmessage = function (event) {
                var data = {};
                try {
                    data = JSON.parse(event.data);
                } catch (e) {
                    data.type = 'null';
                    data.content = event.data;
                }
                switch (data.type) {
                    case 'receive_message':
                        var content = data.content.message;
                        var nickchen = data.content.nickchen;
                        var time = data.content.time;
                        var date = new Date(parseInt(time) * 1000);
                        var msg_time = date.format("yyyy-MM-dd h:m:s");
                        $("#msg_list").append("<li>" + nickchen + ":" + content + " 时间:" + msg_time + "</li>");
                        break;
                    case 'error':
                        var error_code = data.error_code;
                        if (error_code == 'UNLOGIN') {
                            alert('Please Login!');
                        }
                        break;
                    case 'login':
                        if (data.content.status == true) {
                            loadMessageRecord(1);
                            $(".login").fadeOut();
                        }
                }
            }

            $("[name=send]").click(function () {
                var msg = $("[name=msg]").val();
                var data = {
                    type: 'send_message',
                    content: msg
                }
                $("[name=msg]").val('');
                socket.send(JSON.stringify(data));
            });
        }

        $("input[name=login_btn]").click(function () {
            var loginUrl = $("form[name=login_form]").attr('action');

            $.ajax({
                url: loginUrl,
                data: {
                    username: $("input[name=username]").val(),
                    password: $("input[name=password]").val()
                },
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.status == true) {
                        var token = data.access_token
                        storage.setItem("access_token", token);
                        $(".login").fadeOut();
                        socket.login(token);
                    } else {
                        alert(data.msg);
                    }
                },
                error: function (e) {
                    alert(e);
                }
            });
        });

        Date.prototype.format = function (format) {
            var date = {
                "M+": this.getMonth() + 1,
                "d+": this.getDate(),
                "h+": this.getHours(),
                "m+": this.getMinutes(),
                "s+": this.getSeconds(),
                "q+": Math.floor((this.getMonth() + 3) / 3),
                "S+": this.getMilliseconds()
            };
            if (/(y+)/i.test(format)) {
                format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
            }
            for (var k in date) {
                if (new RegExp("(" + k + ")").test(format)) {
                    format = format.replace(RegExp.$1, RegExp.$1.length == 1
                        ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
                }
            }
            return format;
        }

    });

</script>
</body>
</html>