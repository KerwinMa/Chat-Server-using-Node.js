# required modules
express = require('express')
RedisStore = require('connect-redis')(express)
redisStore = new RedisStore()
routes = require('./routes')
parseCookie = require('connect').utils.parseCookie

# redis 
redis = require 'redis'
redisClient = redis.createClient()

redisClient.on 'error',(err)->
    console.log 'error'+err

# express app configure
app = module.exports = express.createServer()
io = require('socket.io').listen(app)

app.configure ->
    app.set 'views', __dirname + '/views'
    app.set 'view engine', 'jade'
    app.use express.logger()
    app.use express.cookieParser()
    app.use express.bodyParser(
        uploadDir: '/root/kakao/public/upload'
        )
    app.use express.session(
        secret: 'Jesus is the TRUTH,the WAY and the LIFE!'
        store:  redisStore
        )
    app.use express.methodOverride()
    app.use app.router
    app.use express.static(__dirname + '/public')

app.configure 'development', ->
    app.use express.errorHandler(
        dumpExceptions: true
        showStack: true
        )

app.configure 'production', ->
    app.use express.errorHandler()

# HTTP actions
app.get '/', (req,res)->
    res.render 'index',
    title: 'chat'
    timestamp: (new Date()).getTime()

app.get '/upload',(req,res) ->
    res.writeHead(200, {'content-type': 'text/html'})
    res.end(
        '<form action="/upload" enctype="multipart/form-data" method="post">'+
        '<input type="text" name="title"><br>'+
        '<input type="file" name="upload" multiple="multiple"><br>'+
        '<input type="submit" value="Upload">'+
        '</form>'
    )

app.post '/upload',(req,res,next) ->
    console.log 'begin upload -------'
    console.log req.files.upload
    filename = req.files.upload.path.split('/').pop()
    console.log 'ok....'
    redisClient.publish 'pubsub', JSON.stringify(
        type: req.body.type
        content: 'http://ec2-107-21-148-54.compute-1.amazonaws.com:3000/upload/'+filename
        sender: req.session.username
        time: req.body.time
        )
    res.json 'ok'

http = require 'http'
querystring = require 'querystring'

sendSMS = (phoneNumber,verifyCode) ->
    data =
        S : 'H'
        UN: 'gianttel1'
        P : 'dWzzvRjf'
        DA: phoneNumber
        SA: '8613602747053'
        M : 'your verify code is:'+verifyCode

    console.log 'sms'
    client = http.createClient 9001,'sms1.cardboardfish.com'
    request = client.request 'POST','/HTTPSMS?'+querystring.stringify(data)
    request.on 'response',(response) ->
        response.on 'end',->
            #console.log response
    request.end()

###
Session = require('connect').middleware.session.Session
io.set 'authorization', (data, accept) ->
    if (data.headers.cookie)
        data.cookie = parseCookie(data.headers.cookie)
        console.log data.cookie
        data.sessionID = data.cookie['express.sid']
        console.log data.sessionID
        data.sessionStore = redisStore
        redisStore.get data.sessionID, (err, session) ->
            if (err || !session)
                console.log 'err:  ' + err
                console.log session
                accept('Error', false)
            else
            data.session = new Session(data, session)
            accept(null, true)
        else
            return accept('No cookie transmitted.', false)
###

sessions = []

# Socket.io actions
io.sockets.on 'connection', (client)->
    #console.log 'socket with sessionid' + hs.sessionID
    session = []
    sessions[client] = session
  
    subscribe = redis.createClient()
    subscribe.subscribe 'pubsub'
    console.log 'client make a socket connection'

    client.on 'message', (data,callback) ->
        session.username = data.sender
        redisClient.publish 'pubsub', JSON.stringify(data)

    subscribe.on 'message', (channel,message) ->
        o = JSON.parse(message)
        if o.sender != session.username
            client.emit 'message', o

    client.on 'register', (data,callback) ->
        session.phoneNumber = data.phoneNumber
        session.uuid = data.uuid
        console.log data.phoneNumber
        if data.phoneNumber
            verifyCode = Math.round(Math.random() * 10000)
            session.verifyCode = verifyCode+''
            console.log 'verifyCode: '+verifyCode
            sendSMS data.phoneNumber,verifyCode

    client.on 'verify', (data,callback) ->
        console.log 'receive verifyCode:'+data.verifyCode
        console.log 'session verifyCode:'+session.verifyCode

        if data.verifyCode==session.verifyCode
            session.userKey = 'user:'+session.phoneNumber
            redisClient.sadd 'user', session.userKey
            client.emit 'verifyResponse', 'true'
        else
            client.emit 'verifyResponse', 'false'
                
    client.on 'updateProfile', (data,callback) ->
        if session.userKey
            redisClient.hmset session.userKey,data
            redisClient.hgetall session.userKey,(err,values)->
                console.dir values

# HTTP listen and console log
app.listen 3000
