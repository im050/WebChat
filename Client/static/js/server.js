/**
 * websocket封装
 * @param websocket_uri
 * @constructor
 */

var Server = function (websocket_uri) {

    this.websocket = null;
    this.websocket_uri = websocket_uri;
    this.recvFunction = [];

}

Server.prototype.connection = function () {

    var _server = this;

    if (this.websocket == null)
        this.websocket = new WebSocket(this.websocket_uri);

    this.websocket.onmessage = function (event) {
        var data = {};
        try {
            data = JSON.parse(event.data);
        } catch (e) {
            data.type = 'null';
            data.content = event.data;
        }

        if (_server.recvFunction.hasOwnProperty(data.type)) {
            _server.recvFunction[data.type](data.content);
        } else {
            console.log('UNKNOWN CALLBACK HANDLER.');
        }
    }

}

Server.prototype.on = function (method, callback) {
    switch (method) {
        case 'open':
            this.websocket.onopen = callback;
            break;
        case 'message':
            this.websocket.onmessage = callback;
            break;
        case 'close':
            this.websocket.onclose = callback;
            //this.websocket = null;
            break;
        case 'error':
            this.websocket.onerror = callback;
            break;
    }
}

Server.prototype.bindRecvHandler = function (method_name, callback, forceUpdate) {

    if (forceUpdate == null)
        forceUpdate = false;

    if (forceUpdate == false) {
        if (this.recvFunction[method_name] != null)
            return;
    }

    this.recvFunction[method_name] = callback;

}

Server.prototype.send = function (message) {
    this.websocket.send(message);
}
