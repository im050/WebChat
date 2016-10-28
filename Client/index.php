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
<script src="static/js/server.js"></script>
<script src="static/js/chat.js"></script>
<script src="static/js/common.js"></script>
</body>
</html>