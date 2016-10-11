<!DOCTYPE html>
<html charset="utf-8">
<head>
	<title>Chat Online</title>
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
		margin-bottom: 30px;
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
<script type="text/javascript">
	$(function(){
		if (window.WebSocket) {
			var socket_address = 'ws://127.0.0.1:8888';
			var socket = new WebSocket(socket_address);
			var heartPacketInterval = null;
			socket.onopen = function(){
				console.log('HandShake');
				socket.send(JSON.stringify({type: 'init'}));
				heartPacketInterval = setInterval(function(){
					socket.send(JSON.stringify({type: 'ping'}));
				}, 5000);
			}

			socket.onmessage = function(event){
				var data = {};
				try {
					data = JSON.parse(event.data);
				} catch(e) {
					data.type = 'null';
					data.content = event.data;
				}
				switch(data.type) {
					case 'receive_message':
						var content = data.content;
						$("#msg_list").append("<li>"+content+"</li>");
						break;
					case 'error':
						var error_code = data.error_code;
						if (error_code == 'UNLOGIN') {
							alert('Please Login!');
						}
						break;
				}
			}

			$("[name=send]").click(function(){
				var msg = $("[name=msg]").val();
				var data = {
					type : 'send_message',
					content : msg
				}
				$("[name=msg]").val('');
				socket.send(JSON.stringify(data));
			});
		}
	});
</script>
</body>
</html>