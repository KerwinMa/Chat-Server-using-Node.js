NativeBridge =
  callbacksCount: 1
  callbacks: {}
  resultForCallback: resultForCallback = (callbackId, resultArray) ->
    try
      callback = NativeBridge.callbacks[callbackId]
      return  unless callback
      callback.apply null, resultArray
    catch e
      alert e

  call: call = (functionName, args, callback) ->
    #alert 'in nativebridge'
    hasCallback = callback and typeof callback is "function"
    callbackId = (if hasCallback then NativeBridge.callbacksCount++ else 0)
    NativeBridge.callbacks[callbackId] = callback  if hasCallback
    iframe = document.createElement("IFRAME")
    iframe.setAttribute "src", "js-frame:" + functionName + ":" + callbackId + ":" + encodeURIComponent(JSON.stringify(args))
    document.documentElement.appendChild iframe
    iframe.parentNode.removeChild iframe
    iframe = null

testCall = (value) ->
  alert value.toUpperCase()
  NativeBridge.call 'setBackgroundColor',[30,60,200],->
    alert 'callback'

sendMessage = (content,sender,time) ->
  socket.emit 'message',
    chatroom: '1'
    content: content
    type: 1
    sender: sender
    time: time
  , (ok) ->
      #if(ok)
      #alert 'message sent'

sendEventMessage = (event,data) ->
  socket.emit event,data,false

window.sendEventMessage = sendEventMessage
window.sendMessage = sendMessage
window.NativeBridge = NativeBridge
window.testCall = testCall
