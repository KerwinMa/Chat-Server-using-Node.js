(function() {

  $(function() {
    var chatroom, currentDate, currentTime, disableButton, enableButton, socket, switchChatroom, testIphone, timeConverter, timeConverterLong;
    socket = io.connectWithSession('http://ec2-107-21-148-54.compute-1.amazonaws.com:3000');
    window.socket = socket;
    chatroom = 1;
    switchChatroom = function(id) {
      return chatroom = id;
    };
    window.switchChatroom = switchChatroom;
    $('#register').click(function() {
      return socket.emit('register', {
        phoneNumber: $('#phoneNumber').val(),
        uuid: '34343434343'
      }, function(ok, result) {
        if (ok) {
          return alert(result.verifyCode);
        } else {
          return alert(result.errorMessage);
        }
      });
    });
    testIphone = function(value) {
      var iphoneObject;
      iphoneObject = window.testObject;
      return iphoneObject.toUpper(value);
    };
    $('#verify').click(function() {
      return socket.emit('verify', {
        phoneNumber: $('#phoneNumber').val(),
        uuid: '34343434343',
        verifyCode: $('#verifyCode').val()
      }, function(ok, result) {
        if (ok) {
          return alert('ok');
        } else {
          return alert(result.errorMessage);
        }
      });
    });
    $('#updateProfile').click(function() {
      return socket.emit('updateProfile', {
        phoneNumber: $('#phoneNumber').val(),
        uuid: '34343434343',
        statusMessage: $('#statusMessage').val(),
        userName: $('#userName').val(),
        kakaoID: $('#kakaoID').val()
      }, function(ok, result) {
        if (ok) {
          return alert('ok');
        } else {
          return alert(result.errorMessage);
        }
      });
    });
    $('#chat').click(function() {
      return socket.emit('message', {
        chatroom: chatroom,
        time: new Date(),
        message: $('#message').val()
      }, function(ok, result) {
        if (ok) {} else {
          return alert(result.errorMessage);
        }
      });
    });
    socket.on('message', function(data) {
      $('#chatroom' + data.chatroom).html($('#chatroom' + data.chatroom).html() + '<p>' + data.message + '</p>');
      return NativeBridge.call('onMessage', data, function() {});
    });
    timeConverter = function(timestamp) {
      var d;
      d = new Date(timestamp * 1000);
      return padStr(d.getHours()) + ':' + padStr(d.getMinutes()) + ':' + padStr(d.getSeconds());
    };
    timeConverterLong = function(timestamp) {
      var d;
      d = new Date(timestamp * 1000);
      return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate() + ' ' + padStr(d.getHours()) + ':' + padStr(d.getMinutes()) + ':' + padStr(d.getSeconds());
    };
    currentDate = function() {
      var d;
      d = new Date;
      return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
    };
    currentTime = function() {
      var d;
      d = new Date;
      return padStr(d.getHours()) + ':' + padStr(d.getMinutes()) + ':' + padStr(d.getSeconds());
    };
    enableButton = function(id) {
      return $(id).removeAttr("disabled");
    };
    return disableButton = function(id) {
      return $(id).attr("disabled", "disabled");
    };
  });

}).call(this);
