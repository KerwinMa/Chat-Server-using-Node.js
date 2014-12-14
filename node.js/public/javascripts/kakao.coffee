$ ->
  socket = io.connectWithSession('http://ec2-107-21-148-54.compute-1.amazonaws.com:3000')

  window.socket = socket

  chatroom = 1
  switchChatroom = (id)->
    chatroom = id
  window.switchChatroom = switchChatroom

  $('#register').click ->
    socket.emit 'register',
      phoneNumber: $('#phoneNumber').val()
      uuid: '34343434343'
    , (ok,result)->
      if(ok)
        alert result.verifyCode
      else
        alert result.errorMessage
 
  testIphone = (value)->
    iphoneObject = window.testObject
    return iphoneObject.toUpper(value)

  $('#verify').click ->
    socket.emit 'verify',
      phoneNumber: $('#phoneNumber').val()
      uuid: '34343434343'
      verifyCode: $('#verifyCode').val()
    , (ok,result)->
      if(ok)
        alert 'ok'
      else
        alert result.errorMessage

  $('#updateProfile').click ->
    socket.emit 'updateProfile',
      phoneNumber: $('#phoneNumber').val()
      uuid: '34343434343'
      statusMessage: $('#statusMessage').val()
      userName: $('#userName').val()
      kakaoID: $('#kakaoID').val()
    , (ok,result)->
      if(ok)
        alert 'ok'
      else
        alert result.errorMessage

  $('#chat').click ->
    socket.emit 'message',
      chatroom: chatroom
      time: new Date()
      message: $('#message').val()
    ,(ok,result)->
      if(ok)
        #alert 'ok'
      else
        alert result.errorMessage
  socket.on 'message', (data) ->
    $('#chatroom'+data.chatroom).html $('#chatroom'+data.chatroom).html()+'<p>'+data.message+'</p>'
    NativeBridge.call 'onChatMessage',data,->
      #alert data.message

  socket.on 'event', (data) ->
    NativeBridge.call 'onEventMessage',data, ->

  timeConverter = (timestamp) ->
    d = new Date timestamp*1000
    return padStr(d.getHours())+':'+padStr(d.getMinutes())+':'+padStr(d.getSeconds())

  timeConverterLong = (timestamp) ->
    d = new Date timestamp*1000
    return d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+padStr(d.getHours())+':'+padStr(d.getMinutes())+':'+padStr(d.getSeconds())

  currentDate = ->
    d = new Date
    return d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()

  currentTime = ->
    d = new Date
    return padStr(d.getHours())+':'+padStr(d.getMinutes())+':'+padStr(d.getSeconds())

  #设置按钮是否可用
  enableButton = (id) ->
    $(id).removeAttr("disabled")
  disableButton = (id) ->
    $(id).attr("disabled", "disabled")

