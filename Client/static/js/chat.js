/**
 * 聊天室控制
 */

var Chat;
Chat = function (options) {

    var storage = window.localStorage;
    var heartPacketInterval = null;


    this.options = {
        mlc_id: '#msg_list' //message list container id
    };

    this.options = $.extend(this.options, options);

    var messageList = new Vue({
        el: this.options.mlc_id,
        data: {
            list : []
        }
    });

    this.init = function () {
        var _this = this;
        //建立服务器
        this.server = new Server(this.options.url);
        //进行通信
        this.server.connection();
        //绑定接受消息事件处理
        this.server.bindRecvHandler('receive_message', function (data) {
            _this.printMessage(data);
        });

        //收到登录消息处理
        this.server.bindRecvHandler('login', function (data) {
            if (data.status == true) {
                _this.loadMessageRecord(1);
                $(".login").fadeOut();
            } else {
                //alert("登录失败,请重新登录!");
                $(".login").fadeIn();
            }
        });
        //握手成功执行动作,定时发送心跳包
        this.server.on('open', function (evt) {
            if (storage.getItem("access_token") != '') {
                _this.loginServer(storage.getItem('access_token'));
            }
            heartPacketInterval = setInterval(function () {
                _this.sendMessage({type: 'ping'});
            }, 5000);
        });
    };

    /**
     * 打印聊天数据在屏幕
     * @param data
     */
    this.printMessage = function (data) {

        var _this = this;

        data.time = new Date(parseInt(data.time * 1000)).format("yyyy-MM-dd h:m:s");

        messageList.$data.list.push(data);

        messageList.$nextTick(function(){
            _this.scrollToEnd();
        });
        //console.log(data);
        //var nickchen = data.nickchen;
        //var avatar = data.avatar;
        //var message = data.message;
        //var avatar = data.avatar;
        //var time = new Date(parseInt(data.time) * 1000).format("yyyy-MM-dd h:m:s");
        //$(this.options.mlc_id).append("<li><img src='"+avatar+"' width='32' height='32' />" + nickchen + ":" + message + " 时间:" + time + "</li>");
    };

    /**
     * 聊天窗滚动
     */
    this.scrollToEnd = function() {
        var screen = $(this.options.mlc_id).parent();
        try {
            $(screen).scrollTop(screen[0].scrollHeight);
        }catch(e){
            console.log(e);
        }
    }

    /**
     * 发送信息到服务器
     * @param message
     */
    this.sendMessage = function (message) {
        if (this.server.send(JSON.stringify(message))) {
            //this.printMessage(message);
        }
    };

    /**
     * 加载聊天记录
     * @param room_id
     */
    this.loadMessageRecord = function (room_id) {
        var _this = this;
        $.ajax({
            url: this.options.record_url,
            dataType: 'jsonp',
            data: {
                room_id: room_id
            },
            type: 'get',
            success: function (msg) {
                var length = msg.length;
                for (var i = (length - 1); i >= 0; i--) {
                    var message = {
                        nickchen: msg[i].content.nickchen,
                        message: msg[i].content.message,
                        avatar: msg[i].content.avatar,
                        time: msg[i].content.time
                    };

                    _this.printMessage(message);
                }

                _this.scrollToEnd();

            }
        });
    };

    /**
     * 登录服务器
     * @param token
     */
    this.loginServer = function (token) {
        this.server.send(JSON.stringify({type: 'login', content: {access_token: token}}));
    };

    /**
     * 登录web获得授权
     * @param username
     * @param password
     */
    this.loginWeb = function(username, password){
        var _this = this;
        $.ajax({
            url: this.options.login_url,
            data: {
                username: username,
                password: password
            },
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    var token = data.access_token
                    storage.setItem("access_token", token);
                    $(".login").fadeOut();
                    _this.loginServer(token);
                } else {
                    alert(data.msg);
                }
            },
            error: function (e) {
                alert(e);
            }
        });
    }

};
