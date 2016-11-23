var storage = window.localStorage;

$(function () {

    var recordUrl = 'http://localhost:8888/record/';
    var roomsUrl = 'http://localhost:8888/rooms/';
    var websocket_uri = 'ws://127.0.0.1:8888';


    //初始化聊天室
    var chat = new Chat({
        'url': websocket_uri,
        'record_url': recordUrl,
        'rooms_url': roomsUrl
    });

    chat.init();

    //发送按钮事件绑定
    $("[name=send]").click(function () {
        var msg = $("[name=msg]").val();
        if ($.trim(msg) == '') {
            return false;
        }
        var data = {
            type: 'send_message',
            content: {
                message: msg
            }
        }
        $("[name=msg]").val('');
        chat.sendMessage(data);
    });

    //回车发送事件
    $("input[name=msg]").keypress(function (event) {
        if (event.keyCode == 13) {
            $("[name=send]").click();
        }
    });

    //登录按钮事件绑定
    $("input[name=login_btn]").click(function () {
        var loginUrl = $("form[name=login_form]").attr('action');
        chat.options.login_url = loginUrl;
        var data = {
            username: $("input[name=username]").val(),
            password: $("input[name=password]").val()
        }
        chat.loginWeb(data.username, data.password);
    });

    //日期格式化处理函数
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

    //
    $(".menu ul li").click(function(){
        var id = $(this).attr('id');
        var elementId = "#" + id + "_sidebar";
        if ($(elementId).width() <= 0) {
            $(".sidebar").width(0);
            $(elementId).width(300);
        } else {
            $(".sidebar").width(0);
        }
    })

    $(".chat").niceScroll({
        cursorcolor: "rgba(0,0,0,0.3)",
        cursorborder: "none"
    });


});
