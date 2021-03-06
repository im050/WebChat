/**
 * 聊天室控制
 * @author memory<service@im050.com>
 */
var Chat;
Chat = function (options) {

    var storage = window.localStorage;
    var heartPacketInterval = null;

    this.user = {
        user_id: 0,
        nickchen: ''
    }

    this.screen = null;

    //默认参数
    this.options = {
        mlc_id: '#msg_list' //message list container id
    };

    //合并自定义参数
    this.options = $.extend(this.options, options);

    //全局引用
    var _chat = this;

    //消息列表
    var messageList = new Vue({
        el: this.options.mlc_id,
        data: {
            list: []
        },
        methods: {
            //判断是否是自身发的消息
            isOwner: function (user_id) {
                return _chat.user.user_id == user_id;
            }
        }
    });

    //在线列表
    var onlineList = new Vue({
        el: "#online-list",
        data: {
            users: [],
            user_count: 0
        }
    });

    /**
     * 初始化过程
     */
    this.init = function () {
        //加载房间
        this.loadRooms();
        this.screen = $(this.options.mlc_id).parent();
        var _this = this;
        //建立服务器
        this.server = new Server(this.options.url);
        //进行通信
        this.server.connection();
        //绑定接受消息事件处理
        this.server.bindRecvHandler('receive_message', function (data) {
            _this.printMessage(data);
        });

        //有新用户登录
        this.server.bindRecvHandler('user_login', function (data) {
            if (!_this.existsOnlineUser(data.user_id)) {
                onlineList.$data.users.push(data);
                onlineList.$data.user_count++;
            }
        });

        //有用户退出
        this.server.bindRecvHandler('user_logout', function (data) {
            var list = onlineList.$data.users;
            for (var i = 0; i < list.length; i++) {
                var user = list[i];
                if (user.user_id == data.user_id) {
                    onlineList.$data.users.splice(i, 1);
                    onlineList.$data.user_count--;
                    break;
                }
            }
        });

        //收到登录消息处理
        this.server.bindRecvHandler('login', function (data) {
            if (data.status == true) {
                //登录成功,加载聊天记录
                _this.loadMessageRecord(1);
                //更新用户信息
                _this.setUser(data.user.user_id, data.user.nickchen);
                //请求在线列表
                _this.sendMessage({type: 'online_list', content: {
                   room_id : 1
                }});
                $(".login").fadeOut();
            } else {
                //alert("登录失败,请重新登录!");
                $(".login").fadeIn();
            }
        });

        //更新在线列表
        this.server.bindRecvHandler('online_list', function (data) {
            //console.log(更新在线列表);
            onlineList.$data.users = data;
            onlineList.$data.user_count = data.length;
        });

        //更换房间
        this.server.bindRecvHandler('change_room', function (data) {
            console.log("切换房间成功");
        });

        this.server.bindRecvHandler('pop_message', function (data) {
            alert(data.message);
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

        /*
         this.server.on("close", function (evt) {
         storage.clear();
         alert("服务器把你踹下去了!");
         });
         */


    };

    this.existsOnlineUser = function (user_id) {
        var i = 0;
        for (; i < onlineList.$data.users.length; i++) {
            var data = onlineList.$data.users[i];
            if (data.user_id == user_id) {

                return true;
            }
        }
        return false;
    }

    /**
     * 打印聊天数据在屏幕
     * @param data
     */
    this.printMessage = function (data, toScrollEnd) {
        if (toScrollEnd == null)
            toScrollEnd = false;

        var _this = this;
        data.time = new Date(parseInt(data.time * 1000)).format("yyyy-MM-dd h:m:s");
        messageList.$data.list.push(data);
        messageList.$nextTick(function () {
            var currentHeight = $(_this.screen).scrollTop() + $(_this.screen).height();
            //console.log(_this.screen[0].scrollHeight - currentHeight);
            if (_this.screen[0].scrollHeight - currentHeight < 100 || toScrollEnd == true) {
                _this.scrollToEnd();
            }
        });
    };

    /**
     * 聊天窗滚动
     */
    this.scrollToEnd = function () {
        var screen = $(this.screen);
        try {
            $(screen).scrollTop(screen[0].scrollHeight);
        } catch (e) {
            //console.log(e);
        }
    }

    /**
     * 发送信息到服务器
     * @param message
     */
    this.sendMessage = function (message) {
        this.server.send(JSON.stringify(message));
    };

    /**
     * 加载聊天记录
     * @param room_id
     */
    this.loadMessageRecord = function (room_id) {
        console.log(room_id);
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
                        time: msg[i].content.time,
                        user_id: msg[i].content.user_id
                    };

                    _this.printMessage(message, true);
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
        this.server.send(JSON.stringify({type: 'login', content: {access_token: token, room_id: 1}}));
    };

    /**
     * 登录web获得授权
     * @param username
     * @param password
     */
    this.loginWeb = function (username, password) {
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
                    var token = data.access_token;
                    storage.setItem("access_token", token);
                    $(".login").fadeOut();
                    _this.loginServer(token);
                } else {
                    //console.log(data);
                    alert(data.msg);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }

    this.loadRooms = function () {
        var _this = this;
        $.ajax({
            url: this.options.rooms_url,
            dataType: 'jsonp',
            type: 'get',
            success: function (rooms) {
                // console.log(rooms);
                // console.log("yes");
                var rooms = new Vue({
                    el: '#room_list',
                    data: {
                        rooms: rooms
                    },
                    methods: {
                        changeRoom: function (room_id) {
                            _this.sendMessage({
                                'type': 'change_room',
                                'content': {
                                    'room_id': room_id
                                }
                            });
                            //请求在线列表
                            _this.sendMessage({
                                type: 'online_list',
                                content: {
                                    'room_id': room_id
                                }
                            });
                            //请求聊天记录
                            _this.loadMessageRecord(room_id);
                        }
                    }
                });
            }
        });
    }

    this.setUser = function (user_id, nickchen) {
        this.user.user_id = user_id;
        this.user.nickchen = nickchen;
    }

};
