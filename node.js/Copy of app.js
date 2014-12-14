(function() {

	var app     = require('express');
	var server  = module.exports  = app.createServer();
	var io      = require('socket.io').listen(server);

	var md5     = require('./md5.js');
	
	var mysql                 = require('mysql');
	var database_name         = 'chatting_room';
	var admin_table_name      = 'admin';
	var users_table_name      = 'users';
	var friends_table_name    = 'friends';
	var message_table_name    = 'messages';
	var chatrooms_table_name  = 'chatrooms';
	var mysqlClient           = mysql.createClient({
		user: 'root',
		password: '',
	});
	
	mysqlClient.query('CREATE DATABASE IF NOT EXISTS '+database_name, function(err) {
		if ( err && err.number != mysql.ERROR_DB_CREATE_EXISTS ) {
			throw err;
		}
	});
	mysqlClient.query('USE '+database_name);
	
	
	// users which are currently connected to the chat
	var users = {};
	
	
	io.sockets.on('connection', function (socket) {
		
		socket.on('register', function(data) {
			var response_json  = null;
		
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			var nickname       = json.nickname;
			var email          = json.email;
			var password       = json.password;
			var enc_password   = md5.md5(password);
			var sex            = json.sex;
			var age            = json.age;
			var position       = json.position;
			
			var fullDate       = new Date();
			var year           = fullDate.getFullYear();
			var month          = fullDate.getMonth()+1;
			var date           = fullDate.getDate();
			var hour           = fullDate.getHours();
			var min            = fullDate.getMinutes();
			var sec            = fullDate.getSeconds();
			
			var cur_time_string   = year+'-'+month+'-'+date+' '+hour+':'+min+':'+sec;
			
			var create_time              = cur_time_string;
			var last_signin              = cur_time_string;
			var last_activation_request  = cur_time_string;
			var lost_password_request    = cur_time_string;
			
			var active      = 1;
			var room_id     = 0;
			var chat_permission   = 1;
			var ban_last_time     = cur_time_string;
			var ban_period        = -1;
			var is_online   = 0;
			
			mysqlClient.query(
				'SELECT * FROM '+users_table_name+' WHERE nickname="'+nickname+'" OR email="'+email+'" ORDER BY id DESC',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('register_response', response_json);
						throw err;
						return;
					}
					
					if ( results.length > 0 ) { 
						var cur_nickname = results[0].nickname;
						var cur_email    = results[0].email;
						
						if ( cur_nickname == nickname )
							response_json = {'success':0, 'err_msg':'Nick name already exists!'};
						else if ( cur_email == email )
							response_json = {'success':0, 'err_msg':'Email address already exists!'};
						
						socket.emit('register_response', response_json);
					} else {
						mysqlClient.query(
							'INSERT INTO '+users_table_name+' '+
							'SET nickname = ?, email = ?, password = ?, sex = ?, age = ?, position = ?, active = ?, create_time = ?, last_signin = ?, last_activation_request = ?, lost_password_request = ?, room_id = ?, chat_permission = ?, ban_last_time = ?, ban_period = ?, is_online = ?',
							[nickname, email, enc_password, sex, age, position, active, create_time, last_signin, last_activation_request, lost_password_request, room_id, chat_permission, ban_last_time, ban_period, is_online], function(err, info) {
								if ( err ) {
									response_json = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('register_response', response_json);
						
									throw err;
									return;
								}
								
								response_json = {'success':1, 'nickname':nickname};
								socket.emit('register_response', response_json);
							}
						);
					}
				}
			);
		});
		
		socket.on('login', function(data) {
			var response_json  = null;
		
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			var nickname       = json.nickname;
			var password       = json.password;
			var enc_password   = md5.md5(password);
			var new_position   = json.position;
			
			mysqlClient.query(
				'SELECT * FROM '+users_table_name+' WHERE nickname="'+nickname+'" AND password="'+enc_password+'"',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('login_response', response_json);
						
						throw err;
						return;
					}
					
					if ( results.length > 0 ) {
						// if the user exists ...
						var user_id    = results[0].id;
						nickname       = results[0].nickname;
						var email      = results[0].email;
						var sex        = results[0].sex;
						var age        = results[0].age;
						var position   = results[0].position;
						var room_id    = results[0].room_id;
						var is_online  = results[0].is_online;						
						
						if ( is_online == 1 ) {
							response_json  = {'success':0, 'err_msg':'The user is already online!'};
							socket.emit('login_response', response_json);
							
							return;
						}
						
						var user         = {'user_id':user_id, 'nickname':nickname, 'email':email, 'sex':sex, 'age':age, 'position':new_position, 'room_id':room_id};
						socket.user      = user;
						
						users[nickname]  = user;
						
						//
						var fullDate    = new Date();
						var year        = fullDate.getFullYear();
						var month       = fullDate.getMonth()+1;
						var date        = fullDate.getDate();
						var hour        = fullDate.getHours();
						var min         = fullDate.getMinutes();
						var sec         = fullDate.getSeconds();
						
						var cur_time_string   = year+'-'+month+'-'+date+' '+hour+':'+min+':'+sec;
						
						var last_signin = cur_time_string;
								
						// update some fields such as is_online when logging in ...
						var is_online = 1;
						mysqlClient.query(
							'UPDATE '+users_table_name+' '+
							'SET is_online = ?, position = ?, last_signin = ? WHERE id = ?', [is_online, new_position, last_signin, user_id], function(err, info) {
								if ( err ) {
									response_json  = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('login_response', response_json);
									throw err;
									return;
								}
								
								mysqlClient.query(
									'SELECT * FROM '+admin_table_name,
									function selectCb(err, admin_results, fields) {
										if ( err ) {
											response_json  = {'success':0, 'err_msg':'Database query error!'};
											socket.emit('login_response', response_json);
											
											throw err;
											return;
										}
										
										if ( admin_results.length == 1 ) {
											var admin_name  = admin_results[0].nickname;
											
											response_json   = {'success':1, 'err_msg':'', 'msg':'Logged in!', 'user_id':user_id, 'nickname':nickname, 'admin_name':admin_name};
											socket.emit('login_response', response_json);
											
										} else if ( admin_results.length > 1 ) {
											
											response_json   = {'success':0, 'err_msg':'Duplicated admins exist!', 'msg':'Logged in!', 'user_id':user_id, 'nickname':nickname};
											socket.emit('login_response', response_json);
											
										} else if ( admin_results.length == 0 ) {
											
											response_json   = {'success':0, 'err_msg':'No admins exist!', 'msg':'Logged in!', 'user_id':user_id, 'nickname':nickname};
											socket.emit('login_response', response_json);
											
										}
										
										return;
									}
								);
							}
						);
					} else {
						response_json  = {'success':0, 'err_msg':'Invalid nickname or password!'};
						socket.emit('login_response', response_json);
						return;
					}
				} // function selectCb(err, results, fields)
			); // mysqlClient.query
		});
		
		socket.on('logout', function() {
			var response_json  = null;
		
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('logout_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('logout_response', response_json);
				return;
			}
				
			var user_id   = socket.user.user_id;
			var nickname  = socket.user.nickname;
			
			delete users[nickname];
			
			var is_online  = 0;
			var room_id    = 0;
			mysqlClient.query(
				'UPDATE '+users_table_name+' '+
				'SET is_online = ?, room_id = ? WHERE id = ?', [is_online, room_id, user_id], function(err, info) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('logout_response', response_json);
						throw err;
						return;
					}
					
					response_json = {'success':1, 'err_msg':'', 'msg':'Logged out!'};
					socket.emit('logout_response', response_json);
					
					return;
				}
			);
		});
		
		socket.on('view_profile', function (data) {
			var response_json  = null;
		
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('view_profile_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('view_profile_response', response_json);
				return;
			}
			
			if ( data == undefined ) {
				var user_id        = socket.user.user_id;
			
				mysqlClient.query(
					'SELECT * FROM '+users_table_name+' WHERE id="'+user_id+'"',
					function selectCb(err, results, fields) {
						if ( err ) {
							response_json  = {'success':0, 'err_msg':'Database query error!'};
							socket.emit('view_profile_response', response_json);
							
							throw err;
							return;
						}
						
						if ( results.length > 0 ) {
							var nickname   = results[0].nickname;
							var email      = results[0].email;
							var sex        = results[0].sex;
							var age        = results[0].age;
							var position   = results[0].position;
							
							response_json  = {'success':1, 'nickname':nickname, 'email':email, 'sex':sex, 'age':age, 'position':position};
							socket.emit('view_profile_response', response_json);
							return;
						} else {
							response_json  = {'success':0, 'err_msg':'Could not get profile!'};
							socket.emit('view_profile_response', response_json);
							return;
						}
					}
				);
			} else {
				var jsonString   = JSON.stringify(data);
				var json         = JSON.parse(jsonString);
			
				if ( json.nickname == undefined ) {
					response_json  = {'success':0, 'err_msg':'Could not get profile!'};
					socket.emit('view_profile_response', response_json);
					return;
				} else {
					var nickname = json.nickname;
				
					mysqlClient.query(
						'SELECT * FROM '+users_table_name+' WHERE nickname="'+nickname+'"',
						function selectCb(err, results, fields) {
							if ( err ) {
								response_json  = {'success':0, 'err_msg':'Database query error!'};
								socket.emit('view_profile_response', response_json);
								
								throw err;
								return;
							}
							
							if ( results.length > 0 ) {
								var nickname   = results[0].nickname;
								var email      = results[0].email;
								var sex        = results[0].sex;
								var age        = results[0].age;
								var position   = results[0].position;
								
								response_json  = {'success':1, 'nickname':nickname, 'email':email, 'sex':sex, 'age':age, 'position':position};
								socket.emit('view_profile_response', response_json);
								return;
							} else {
								response_json  = {'success':0, 'err_msg':'Could not get profile!'};
								socket.emit('view_profile_response', response_json);
								return;
							}
						}
					);
				}
			}
			
		});
		
		socket.on('update_profile', function (data) {
			var response_json  = null;
		
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('update_profile_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('update_profile_response', response_json);
				return;
			}
			
			var user_id      = socket.user.user_id;
			
			var jsonString   = JSON.stringify(data);
			var json         = JSON.parse(jsonString);
			
			var nickname     = json.nickname;
			var email        = json.email;
			var sex          = json.sex;
			var age          = json.age;
			var position     = json.position;
			
			mysqlClient.query(
				'UPDATE '+users_table_name+' '+
				'SET nickname = ?, email = ?, sex = ?, age = ?, position = ? WHERE id = ?', [nickname, email, sex, age, position, user_id], function(err, info) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('update_profile_response', response_json);
						throw err;
						return;
					}
					
					response_json = {'success':1, 'err_msg':'', 'msg':'Profile has been updated successfully!'};
					socket.emit('update_profile_response', response_json);
					
					return;
				}
			);
		});
		
		socket.on('sendchat', function (data) {
			var response_json  = null; 
			
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			var type           = json.type;
			var sender_id      = json.sender_id;
			var sender_name    = json.sender_name;
			var msg            = json.msg;
			var room_id        = json.room_id;
			var receiver_id    = json.receiver_id;
			var receiver_name  = json.receiver_name;
			var read_status    = json.read_status;
			
			var fullDate       = new Date();
			var year           = fullDate.getFullYear();
			var month          = fullDate.getMonth()+1;
			var date           = fullDate.getDate();
			var hour           = fullDate.getHours();
			var min            = fullDate.getMinutes();
			var sec            = fullDate.getSeconds();
			
			var send_time      = year+'-'+month+'-'+date+' '+hour+':'+min+':'+sec;
			
			mysqlClient.query(
				'SELECT * FROM '+users_table_name+' WHERE id="'+sender_id+'" AND nickname="'+sender_name+'"',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('login_response', response_json);
						
						throw err;
						return;
					}
					
					if ( results.length > 0 ) {
						var chat_permission  = results[0].chat_permission;
						
						if ( chat_permission == 0 ) {
							response_json  = {'success':0, 'err_msg':'You are not allowed to chat!'};
							socket.emit('login_response', response_json);
							
							return;
						}
						
						mysqlClient.query(
							'INSERT INTO '+message_table_name+' '+
							'SET sender_id = ?, receiver_id = ?, sender_name = ?, receiver_name = ?, send_time = ?, msg = ?, read_status = ?, room_id = ?',
							[sender_id, receiver_id, sender_name, receiver_name, send_time, msg, read_status, room_id], 
							function(err, info) {
								if ( err ) {
									response_json = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('register_response', response_json);
						
									throw err;
									return;
								}
								
								response_json  = {'success':1, 'err_msg':'', 'msg':msg, 'sender_id':sender_id, 'sender_name':sender_name, 'receiver_id':receiver_id, 'receiver_name':receiver_name, 'room_id':room_id};
								socket.broadcast.emit('updatechat', response_json);
								
								return;
							}
						);
					} else {
						response_json  = {'success':0, 'err_msg':'Invalid session!'};
						socket.emit('login_response', response_json);
						
						return;
					}
				}
			);
			
		});
		
		socket.on('room_in', function(data) {
			var response  = null;
			
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('room_in_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('room_in_response', response_json);
				return;
			}
			
			var jsonString   = JSON.stringify(data);
			var json         = JSON.parse(jsonString);
			
			var user_id      = socket.user.user_id;
			
			if ( json.room_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Insufficient parameters!'};
				socket.emit('room_in_response', response_json);
				return;
			}			
			
			var room_id      = json.room_id;
			
			//
			mysqlClient.query(
				'SELECT * FROM '+users_table_name+' WHERE id="'+user_id+'"',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('room_in_response', response_json);
						
						throw err;
						return;
					}
					
					if ( results.length > 0 ) {
						var chat_permission  = results[0].chat_permission;
						var ban_last_time    = results[0].ban_last_time;
						var ban_period       = results[0].ban_period;
						
						if ( chat_permission == 0 ) {
							
							if ( ban_period == 2 ) {
								response_json = {'success':0, 'err_msg':'You are not allowed to chat permanently!', 'msg':''};
								socket.emit('room_in_response', response_json);
								return;
							} else if ( ban_period == 1 ) {
								var real_ban_period  = 24 * 3600 * 1000;
								
								var cur_date_time    = new Date();
								
								if ( (cur_date_time.getTime() - ban_last_time.getTime()) < real_ban_period ) {
									response_json = {'success':0, 'err_msg':'You are not allowed to chat!', 'msg':''};
									socket.emit('room_in_response', response_json);
									return;
								} else {
									chat_permission  = 1;
									ban_period       = -1;
									
									mysqlClient.query(
										'UPDATE '+users_table_name+' '+
										'SET chat_permission = ?, ban_period = ?, room_id = ? WHERE id = ?', [chat_permission, ban_period, room_id, user_id], function(err, info) {
											if ( err ) {
												response_json  = {'success':0, 'err_msg':'Database query error!'};
												socket.emit('room_in_response', response_json);
												throw err;
												return;
											}
											
											response_json = {'success':1, 'err_msg':'', 'msg':'Room in!', 'room_id':room_id};
											socket.emit('room_in_response', response_json);
											return;
										}
									);
								}								
							} else if ( ban_period == 0 ) {
								var real_ban_period  = 5 * 60 * 1000;
								
								var cur_date_time    = new Date();
								
								if ( (cur_date_time.getTime() - ban_last_time.getTime()) < real_ban_period ) {
									response_json = {'success':0, 'err_msg':'You are not allowed to chat!', 'msg':''};
									socket.emit('room_in_response', response_json);
									return;
								} else {
									chat_permission  = 1;
									ban_period       = -1;
									
									mysqlClient.query(
										'UPDATE '+users_table_name+' '+
										'SET chat_permission = ?, ban_period = ?, room_id = ? WHERE id = ?', [chat_permission, ban_period, room_id, user_id], function(err, info) {
											if ( err ) {
												response_json  = {'success':0, 'err_msg':'Database query error!'};
												socket.emit('room_in_response', response_json);
												throw err;
												return;
											}
											
											response_json = {'success':1, 'err_msg':'', 'msg':'Room in!', 'room_id':room_id};
											socket.emit('room_in_response', response_json);
											return;
										}
									);
								}
							}
								
						} else {
						
							mysqlClient.query(
								'UPDATE '+users_table_name+' '+
								'SET room_id = ? WHERE id = ?', [room_id, user_id], function(err, info) {
									if ( err ) {
										response_json  = {'success':0, 'err_msg':'Database query error!'};
										socket.emit('room_in_response', response_json);
										throw err;
										return;
									}
									
									response_json = {'success':1, 'err_msg':'', 'msg':'Room in!', 'room_id':room_id};
									socket.emit('room_in_response', response_json);
									return;
								}
							);
						}
					} else {
						response_json  = {'success':0, 'err_msg':'Invalid session!'};
						socket.emit('room_in_response', response_json);
						
						return;
					}
				}
			);
		});
		
		socket.on('room_out', function() {
			var response_json  = null;
			
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('room_out_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('room_out_response', response_json);
				return;
			}
			
			var user_id       = socket.user.user_id;
			var room_id       = 0;
			
			mysqlClient.query(
				'UPDATE '+users_table_name+' '+
				'SET room_id = ? WHERE id = ?', [room_id, user_id], function(err, info) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('room_out_response', response_json);
						throw err;
						return;
					}
					
					response_json = {'success':1, 'err_msg':'', 'msg':'Room out!'};
					socket.emit('room_out_response', response_json);
					
					return;
				}
			);
		});
		
		socket.on('get_friends', function() {
			var response_json  = null;
			
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('update_profile_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('update_profile_response', response_json);
				return;
			}
			
			var user_id        = socket.user.user_id;
			
			var flag           = 1
			mysqlClient.query(
				'SELECT * FROM '+friends_table_name+' WHERE (user_id="'+user_id+'" OR user_id2="'+user_id+'") AND approved="'+flag+'"',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('get_friends_response', response_json);
						
						throw err;
						return;
					}
					
					var friends_count = results.length;
					
					if ( friends_count > 0 ) {	
						
						var friends_list = '';
						
						var get_count  = 0;
						for ( var i=0; i<friends_count; i++ ) {
							var friend_id;
							if ( user_id == results[i].user_id )
								friend_id  = results[i].user_id2;
							else if ( user_id == results[i].user_id2 )
								friend_id  = results[i].user_id;
							else 
								friend_id  = 0;
							
							mysqlClient.query(
								'SELECT nickname, position FROM '+users_table_name+' WHERE id="'+friend_id+'"',
								function selectCb(err, results_1, fields) {
									if ( err ) {
										response_json  = {'success':0, 'err_msg':'Database query error!'};
										socket.emit('get_friends_response', response_json);
										
										throw err;
										return;
									}
									
									if ( results_1.length > 0 ) {
										if ( user_id == results[get_count].user_id )
											friend_id  = results[get_count].user_id2;
										else if ( user_id == results[get_count].user_id2 )
											friend_id  = results[get_count].user_id;
										else 
											friend_id  = 0;
								
										var friend_name    = results_1[0].nickname;
										var position       = results_1[0].position;
										
										get_count++;
										if ( get_count == 1 )
											friends_list = friend_id + '-' + friend_name + '-' + position;
										else 
											friends_list = friends_list + ',' + friend_id + '-' + friend_name + '-' + position;
											
										if ( get_count == friends_count ) {
											response_json  = {'success':1, 'err_msg':'', 'friends_list':friends_list};
											socket.emit('get_friends_response', response_json);
											
											return;
										}
										
									} else {
										response_json  = {'success':0, 'err_msg':'Could not get the nickname of friend!'};
										socket.emit('get_friends_response', response_json);
										
										return;
									}
								}
							);
						}
					} else {
					
						response_json  = {'success':1, 'err_msg':'', 'msg':'No friends!'};
						socket.emit('get_friends_response', response_json);
						
					}
				}
			);
		});
		
		socket.on('get_chatroom_people', function () {
			var response_json = null; 
			
			mysqlClient.query(
				'SELECT * FROM '+chatrooms_table_name,
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('get_chatroom_people_response', response_json);
						
						throw err;
						return;
					}	
					
					if ( results.length > 0 ) {
						var room_people_list  = '';
					
						var room_count = results.length;
						var get_count  = 0;
						for ( var i=0; i<room_count; i++ ) {
							var room_id    = results[i].room_id;
							var room_name  = results[i].name;
							
							mysqlClient.query(
								'SELECT * FROM '+users_table_name+' WHERE room_id="'+room_id+'"',
								function selectCb(err, results_1, fields) {
									if ( err ) {
										response_json  = {'success':0, 'err_msg':'Database query error!'};
										socket.emit('get_chatroom_people_response', response_json);
										
										throw err;
										return;
									}
									
									var peoples      = results_1.length;
									
									room_id  = results[get_count].room_id;
									
									get_count++;
									
									if ( get_count == 1 )
										room_people_list = room_id + '-' + peoples;
									else
										room_people_list = room_people_list + ',' + room_id + '-' + peoples;
									
									if ( get_count == room_count ) {
										response_json  = {'success':1, 'err_msg':'', 'people':room_people_list};
										socket.emit('get_chatroom_people_response', response_json);
										
										return;
									}
								}
							);
						}
						
						
					} else {
						response_json  = {'success':0, 'err_msg':'Could not get room list!'};
						socket.emit('get_chatroom_people_response', response_json);
					}
				}
			);
			
		});
		
		socket.on('add_friend', function (data) {
			var response_json  = null; 
		
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			if ( json.friend_name == undefined ) {
				response_json  = {'success':0, 'err_msg':'Insufficient parameters!'};
				socket.emit('add_friend_response', response_json);
				return;
			}
			
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('add_friend_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('add_friend_response', response_json);
				return;
			}
			
			var user_id      = socket.user.user_id;
			//
			var friend_name  = json.friend_name;
			
			mysqlClient.query(
				'SELECT * FROM '+users_table_name+' WHERE nickname="'+friend_name+'"',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('add_friend_response', response_json);
						
						throw err;
						return;
					}
					
					if ( results.length > 0 ) {
						var friend_id  = results[0].id;
						if ( friend_id == user_id ) {
							response_json  = {'success':0, 'err_msg':'You are about to add yourself to be your friend!'};
							socket.emit('add_friend_response', response_json);
							return;
						}
						
						mysqlClient.query(
							'SELECT * FROM '+friends_table_name+' WHERE user_id="'+user_id+'" AND user_id2="'+friend_id+'"',
							function selectCb(err, results, fields) {
								if ( err ) {
									response_json  = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('add_friend_response', response_json);
									
									throw err;
									return;
								}
								
								if ( results.length > 0 ) {
									response_json  = {'success':0, 'err_msg':'The request has already been made!'};
									socket.emit('add_friend_response', response_json);
									return;
								}
								
								var approved = 0;
								mysqlClient.query(
									'INSERT INTO '+friends_table_name+' '+
									'SET user_id = ?, user_id2 = ?, approved = ?',
									[user_id, friend_id, approved], function(err, info) {
										if ( err ) {
											response_json = {'success':0, 'err_msg':'Database query error!'};
											socket.emit('add_friend_response', response_json);
								
											throw err;
											return;
										}
										
										var send_friend_request  = friend_id + '-' + friend_name;
										response_json = {'success':1, 'err_msg':'', 'send_friend_request':send_friend_request};
										io.sockets.emit('add_friend_response', response_json);
									}
								);
							}
						);
					} else {
						response_json  = {'success':0, 'err_msg':'Friend ID for that name could not be found!'};
						socket.emit('add_friend_response', response_json);
						return;
					}
				}
			);
		});
		
		socket.on('send_receive_friends_list', function () {
			var response_json  = null;
			
			if ( (socket.user == undefined) || (socket.user.user_id == undefined) || (socket.user.nickname == undefined) ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('send_receive_friends_list_response', response_json);
				return;
			}
				
			var user_id   = socket.user.user_id;
			
			var approved = 0;
			mysqlClient.query(
				'SELECT user_id2 FROM '+friends_table_name+' WHERE user_id="'+user_id+'" AND approved="'+approved+'"',
				function selectCb(err, results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('send_receive_friends_list_response', response_json);
						
						throw err;
						return;
					}
					
					var send_friends_name_array_string   = '';
					var send_friends_count               = results.length;
					
					if ( send_friends_count > 0 ) {
					
						var get_count = 0;
						
						for ( var i=0; i<send_friends_count; i++ )  {
							var friend_id  = results[i].user_id2;
							
							mysqlClient.query(
								'SELECT nickname FROM '+users_table_name+' WHERE id="'+friend_id+'"',
								function selectCb(err, results_1, fields) {
									if ( err ) {
										response_json  = {'success':0, 'err_msg':'Database query error!'};
										socket.emit('send_receive_friends_list_response', response_json);
										
										throw err;
										return;
									}
									
									if ( results_1.length > 0 ) {
										friend_id        = results[get_count].user_id2;
										var friend_name  = results_1[0].nickname;
										
										get_count++;
										if ( get_count == 1 )
											send_friends_name_array_string = friend_id + '-' + friend_name;
										else 
											send_friends_name_array_string = send_friends_name_array_string + ',' + friend_id + '-' + friend_name;
										
										if ( get_count == send_friends_count ) {
											//
											mysqlClient.query(
												'SELECT user_id FROM '+friends_table_name+' WHERE user_id2="'+user_id+'" AND approved="'+approved+'"',
												function selectCb(err, results, fields) {
													if ( err ) {
														response_json  = {'success':0, 'err_msg':'Database query error!'};
														socket.emit('send_receive_friends_list_response', response_json);
														
														throw err;
														return;
													}
													
													var receive_friends_name_array_string   = '';
													var receive_friends_count               = results.length;
													if ( receive_friends_count > 0 ) {	
														get_count = 0;
														
														for ( var i=0; i<receive_friends_count; i++ )  {
															var friend_id  = results[i].user_id;
															
															mysqlClient.query(
																'SELECT nickname FROM '+users_table_name+' WHERE id="'+friend_id+'"',
																function selectCb(err, results_1, fields) {
																	if ( err ) {
																		response_json  = {'success':0, 'err_msg':'Database query error!'};
																		socket.emit('send_receive_friends_list_response', response_json);
																		
																		throw err;
																		return;
																	}
																	
																	if ( results_1.length > 0 ) {
																		friend_id        = results[get_count].user_id;
																		var friend_name  = results_1[0].nickname;
																		
																		get_count++;
																		if ( get_count == 1 )
																			receive_friends_name_array_string = friend_id + '-' + friend_name;
																		else 
																			receive_friends_name_array_string = receive_friends_name_array_string + ',' + friend_id + '-' + friend_name;
																			
																		if ( get_count == receive_friends_count ) {
																			response_json  = {'success':1, 'err_msg':'', 'send_friends_list':send_friends_name_array_string, 'receive_friends_list':receive_friends_name_array_string};
																			socket.emit('send_receive_friends_list_response', response_json);
																			
																			return;
																		}
																	} else {
																		response_json  = {'success':0, 'err_msg':'Friend name for that ID could not be found!'};
																		socket.emit('send_receive_friends_list_response', response_json);
																		
																		return;
																	}
																}
															);
															
														}
													} else {
														response_json  = {'success':1, 'err_msg':'', 'send_friends_list':send_friends_name_array_string, 'receive_friends_list':receive_friends_name_array_string};
														socket.emit('send_receive_friends_list_response', response_json);
														return;
													}
												}
											);
											
										}
									} else {
										response_json  = {'success':0, 'err_msg':'Friend name for that ID could not be found!'};
										socket.emit('send_receive_friends_list_response', response_json);
										
										return;
									}
								}
							);
							
						}
					} else {
								
						mysqlClient.query(
							'SELECT user_id FROM '+friends_table_name+' WHERE user_id2="'+user_id+'" AND approved="'+approved+'"',
							function selectCb(err, results, fields) {
								if ( err ) {
									response_json  = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('send_receive_friends_list_response', response_json);
									
									throw err;
									return;
								}
								
								var receive_friends_name_array_string   = '';
								var receive_friends_count               = results.length;
								if ( receive_friends_count > 0 ) {	
									var get_count = 0;
									
									for ( var i=0; i<receive_friends_count; i++ )  {
										var friend_id  = results[i].user_id;
										
										mysqlClient.query(
											'SELECT nickname FROM '+users_table_name+' WHERE id="'+friend_id+'"',
											function selectCb(err, results_1, fields) {
												if ( err ) {
													response_json  = {'success':0, 'err_msg':'Database query error!'};
													socket.emit('send_receive_friends_list_response', response_json);
													
													throw err;
													return;
												}
												
												if ( results_1.length > 0 ) {
													friend_id        = results[get_count].user_id;
													var friend_name  = results_1[0].nickname;
													
													get_count++;
													if ( get_count == 1 )
														receive_friends_name_array_string = friend_id + '-' + friend_name;
													else 
														receive_friends_name_array_string = receive_friends_name_array_string + ',' + friend_id + '-' + friend_name;
														
													if ( get_count == receive_friends_count ) {
														response_json  = {'success':1, 'err_msg':'', 'send_friends_list':send_friends_name_array_string, 'receive_friends_list':receive_friends_name_array_string};
														socket.emit('send_receive_friends_list_response', response_json);
														
														return;
													}
												} else {
													response_json  = {'success':0, 'err_msg':'Friend name for that ID could not be found!'};
													socket.emit('send_receive_friends_list_response', response_json);
													
													return;
												}
												
											}
										);
										
									}
								} else {
									response_json  = {'success':1, 'err_msg':'', 'send_friends_list':send_friends_name_array_string, 'receive_friends_list':receive_friends_name_array_string};
									socket.emit('send_receive_friends_list_response', response_json);
									return;
								}
							}
						);
						
					}
				}
			);
		});
		
		socket.on('receive_friend', function (data) {
			var response_json  = null;
			
			if ( (socket.user == undefined) || (socket.user.user_id == undefined) ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('receive_friend_response', response_json);
				return;
			}
				
			var user_id   = socket.user.user_id;
			
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			if ( (json.tag == undefined) || (json.friend_id == undefined) ) {
				response_json  = {'success':0, 'err_msg':'Insufficient parameters!'};
				socket.emit('receive_friend_response', response_json);
				return;
			}
			
			var tag         = json.tag;
			var friend_id   = json.friend_id;
			
			if ( tag == 'follow' ) {
			
				var approved = 1;
				mysqlClient.query(
					'UPDATE '+friends_table_name+' '+
					'SET approved = ? WHERE user_id = ? AND user_id2 = ?', [approved, friend_id, user_id], function(err, info) {
						if ( err ) {
							response_json  = {'success':0, 'err_msg':'Database query error!'};
							socket.emit('receive_friend_response', response_json);
							throw err;
							return;
						}
						
						mysqlClient.query(
							'SELECT nickname FROM '+users_table_name+' WHERE id="'+friend_id+'"',
							function selectCb(err, results, fields) {
								if ( err ) {
									response_json  = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('receive_friend_response', response_json);
									
									throw err;
									return;
								}
								
								if ( results.length > 0 ) {
									var friend_name  = results[0].nickname;
									
									response_json = {'success':1, 'err_msg':'', 'friend_name':friend_name, 'tag':'follow'};
									io.sockets.emit('receive_friend_response', response_json);
								} else {
									response_json = {'success':0, 'err_msg':'Friend name for that ID could not be found!'};
									socket.emit('receive_friend_response', response_json);
									return;
								}
							}
						);
						
					}
				);
				
			} else if ( tag == 'unfollow' ) {
			
				mysqlClient.query(
					'DELETE FROM '+friends_table_name+' WHERE (user_id="'+friend_id+'" AND user_id2="'+user_id+'") OR (user_id="'+user_id+'" AND user_id2="'+friend_id+'")',
					function (err, info) {
						if ( err ) {
							response_json  = {'success':0, 'err_msg':'Database query error!'};
							socket.emit('receive_friend_response', response_json);
							
							throw err;
							return;
						}
						
						mysqlClient.query(
							'SELECT nickname FROM '+users_table_name+' WHERE id="'+friend_id+'"',
							function selectCb(err, results, fields) {
								if ( err ) {
									response_json  = {'success':0, 'err_msg':'Database query error!'};
									socket.emit('receive_friend_response', response_json);
									
									throw err;
									return;
								}
								
								if ( results.length > 0 ) {
									var friend_name  = results[0].nickname;
									
									response_json = {'success':1, 'err_msg':'', 'friend_id':friend_id, 'friend_name':friend_name, 'tag':'unfollow'};
									io.sockets.emit('receive_friend_response', response_json);
								} else {
									response_json = {'success':0, 'err_msg':'Friend name for that ID could not be found!'};
									socket.emit('receive_friend_response', response_json);
									return;
								}
							}
						);
					}
				);
			
			} else {
				response_json  = {'success':0, 'err_msg':'Invalid parameters!'};
				socket.emit('receive_friend_response', response_json);
				return;
			}
		});
		
		socket.on('delete_friend', function (data) {
			var response_json  = null;
			
			if ( (socket.user == undefined) || (socket.user.user_id == undefined) ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('delete_friend_response', response_json);
				return;
			}
				
			var user_id   = socket.user.user_id;
			
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			if ( json.friend_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Insufficient parameters!'};
				socket.emit('delete_friend_response', response_json);
				return;
			}
			
			var friend_id  = json.friend_id;
			
			mysqlClient.query(
				'DELETE FROM '+friends_table_name+' WHERE (user_id="'+user_id+'" AND user_id2="'+friend_id+'") OR (user_id="'+friend_id+'" AND user_id2="'+user_id+'")',
				function (err, info) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('delete_friend_response', response_json);
						
						throw err;
						return;
					}
					
					mysqlClient.query(
						'SELECT nickname FROM '+users_table_name+' WHERE id="'+friend_id+'"',
						function selectCb(err, results, fields) {
							if ( err ) {
								response_json  = {'success':0, 'err_msg':'Database query error!'};
								socket.emit('delete_friend_response', response_json);
								
								throw err;
								return;
							}
							
							if ( results.length > 0 ) {
								var friend_name  = results[0].nickname;
								
								response_json = {'success':1, 'err_msg':'', 'friend_id':friend_id, 'friend_name':friend_name};
								io.sockets.emit('delete_friend_response', response_json);
							} else {
								response_json = {'success':0, 'err_msg':'Friend name for that ID could not be found!'};
								socket.emit('delete_friend_response', response_json);
								return;
							}
						}
					);
				}
			);
		});
		
		socket.on('is_ban_user', function(data) {
			var response_json     = null;
			
			if ( (socket.user == undefined) || (socket.user.user_id == undefined) ) {
				response_json     = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('is_ban_user_response', response_json);
				return;
			}
			
			var user_id     = socket.user.user_id;
			var friend_id   = -1;
			
			if ( data != undefined ) {
				var jsonString  = JSON.stringify(data);
				var json        = JSON.parse(jsonString);
				
				if ( json.friend_id == undefined )
					friend_id   = -1;
				else 
					friend_id   = json.friend_id;
			}
			
			var user_is_banned   = 0;
			var friend_is_banned = 0;
			
			// Check if the current logged-in user is banned or not ...
			mysqlClient.query(
				'SELECT * FROM '+users_table_name+' WHERE id="'+user_id+'"',
				function selectCb(err, user_results, fields) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('is_ban_user_response', response_json);
						
						throw err;
						return;
					}
					
					if ( user_results.length > 0 ) {
					
						var user_nickname    = user_results[0].nickname;
						var chat_permission  = user_results[0].chat_permission;
						var ban_last_time    = user_results[0].ban_last_time;
						var ban_period       = user_results[0].ban_period;
						
						if ( chat_permission == 0 ) {
						
							if ( ban_period == 2 ) {
							
								user_is_banned       = 1;
								
							} else if ( ban_period == 1 ) {
							
								var real_ban_period  = 24 * 3600 * 1000;
								var cur_date_time    = new Date();								
								if ( (cur_date_time.getTime() - ban_last_time.getTime()) < real_ban_period )
									user_is_banned   = 1;
								else
									user_is_banned   = 0;
								
							} else if ( ban_period == 0 ) {
							
								var real_ban_period  = 5 * 60 * 1000;
								var cur_date_time    = new Date();								
								if ( (cur_date_time.getTime() - ban_last_time.getTime()) < real_ban_period )
									user_is_banned   = 1;
								else
									user_is_banned   = 0;
								
							}
								
						} else {
						
							user_is_banned   = 0;
							
						}
						
						if ( friend_id == -1 ) {
						
							friend_nickname   = '';
							friend_is_banned  = 0;
							response_json     = {'success':1, 'err_msg':'', 'user_is_banned':user_is_banned, 'friend_is_banned':friend_is_banned, 'friend_id':friend_id, 'friend_nickname':friend_nickname};
							socket.emit('is_ban_user_public_response', response_json);
							return;
							
						} else {
							// Check if the friend is banned or not ...
							mysqlClient.query(
								'SELECT * FROM '+users_table_name+' WHERE id="'+friend_id+'"',
								function selectCb(err, friend_results, fields) {
									if ( err ) {
										response_json  = {'success':0, 'err_msg':'Database query error!'};
										socket.emit('is_ban_user_private_response', response_json);
										
										throw err;
										return;
									}
									
									if ( friend_results.length > 0 ) {
										var friend_nickname  = friend_results[0].nickname;
										var chat_permission  = friend_results[0].chat_permission;
										var ban_last_time    = friend_results[0].ban_last_time;
										var ban_period       = friend_results[0].ban_period;
										
										if ( chat_permission == 0 ) {
										
											if ( ban_period == 2 ) {
											
												friend_is_banned     = 1;
												
											} else if ( ban_period == 1 ) {
											
												var real_ban_period  = 24 * 3600 * 1000;
												var cur_date_time    = new Date();								
												if ( (cur_date_time.getTime() - ban_last_time.getTime()) < real_ban_period )
													friend_is_banned = 1;
												else
													friend_is_banned = 0;
												
											} else if ( ban_period == 0 ) {
											
												var real_ban_period  = 5 * 60 * 1000;
												var cur_date_time    = new Date();								
												if ( (cur_date_time.getTime() - ban_last_time.getTime()) < real_ban_period )
													friend_is_banned = 1;
												else
													friend_is_banned = 0;
												
											}
												
										} else {
										
											friend_is_banned  = 0;
											
										}
										
										//
										if ( user_is_banned == 0 ) {
										
											chat_permission = 1;
											ban_period      = -1;
											mysqlClient.query(
												'UPDATE '+users_table_name+' '+
												'SET chat_permission = ?, ban_period = ? WHERE id = ?', [chat_permission, ban_period, user_id], function(err, info) {
													if ( err ) {
														response_json  = {'success':0, 'err_msg':'Database query error!'};
														socket.emit('is_ban_user_private_response', response_json);
														throw err;
														return;
													}
													
													if ( friend_is_banned == 0 ) {
														chat_permission = 1;
														ban_period      = -1;
														mysqlClient.query(
															'UPDATE '+users_table_name+' '+
															'SET chat_permission = ?, ban_period = ? WHERE id = ?', [chat_permission, ban_period, friend_id], function(err, info) {
																if ( err ) {
																	response_json  = {'success':0, 'err_msg':'Database query error!'};
																	socket.emit('is_ban_user_private_response', response_json);
																	throw err;
																	return;
																}
																
																response_json  = {'success':1, 'err_msg':'', 'user_is_banned':user_is_banned, 'friend_is_banned':friend_is_banned, 'friend_id':friend_id, 'friend_nickname':friend_nickname};
																socket.emit('is_ban_user_private_response', response_json);
																return;
															}
														);
													} else {
														response_json  = {'success':1, 'err_msg':'', 'user_is_banned':user_is_banned, 'friend_is_banned':friend_is_banned, 'friend_id':friend_id, 'friend_nickname':friend_nickname};
														socket.emit('is_ban_user_private_response', response_json);
														return;
													}
												}
											);
											
										} else {
										
											if ( friend_is_banned == 0 ) {
											
												chat_permission = 1;
												ban_period      = -1;
												mysqlClient.query(
													'UPDATE '+users_table_name+' '+
													'SET chat_permission = ?, ban_period = ? WHERE id = ?', [chat_permission, ban_period, friend_id], function(err, info) {
														if ( err ) {
															response_json  = {'success':0, 'err_msg':'Database query error!'};
															socket.emit('is_ban_user_private_response', response_json);
															throw err;
															return;
														}
														
														response_json  = {'success':1, 'err_msg':'', 'user_is_banned':user_is_banned, 'friend_is_banned':friend_is_banned, 'friend_id':friend_id, 'friend_nickname':friend_nickname};
														socket.emit('is_ban_user_private_response', response_json);
														return;
													}
												);
												
											} else {
											
												response_json  = {'success':1, 'err_msg':'', 'user_is_banned':user_is_banned, 'friend_is_banned':friend_is_banned, 'friend_id':friend_id, 'friend_nickname':friend_nickname};
												socket.emit('is_ban_user_private_response', response_json);
												return;
												
											}
										}
										
										//
										
									} else {
									
										response_json  = {'success':0, 'err_msg':'Invalid friend id!'};
										socket.emit('is_ban_user_private_response', response_json);
										
										return;
									}
								}
							);
						}
						
					} else {
					
						response_json  = {'success':0, 'err_msg':'Invalid session!'};
						socket.emit('is_ban_user_response', response_json);
						
						return;
					}
				}
			);
		});
		
		socket.on('ban_user', function(data) {
			var response_json     = null;
			var is_admin_update   = 0;
			var is_ban_user       = 0;
			
			if ( (socket.user == undefined) || (socket.user.user_id == undefined) ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('ban_user_response', response_json);
				return;
			}
			
			var jsonString     = JSON.stringify(data);
			var json           = JSON.parse(jsonString);
			
			if ( json.user_to_admin.length > 0 ) {
				var user_to_admin   = json.user_to_admin;
				
				mysqlClient.query(
					'SELECT * FROM '+users_table_name+' WHERE nickname="'+user_to_admin+'"',
					function selectCb(err, results, fields) {
						if ( err ) {
							response_json  = {'success':0, 'err_msg':'Database query error!'};
							socket.emit('ban_user_response', response_json);
							
							throw err;
							return;
						}
						
						if ( results.length > 0 ) {	
							var user_id     = results[0].id;
							var nickname    = results[0].nickname;
							var password    = results[0].password;
							
							var idadmin     = 1;
							
							mysqlClient.query(
								'UPDATE '+admin_table_name+' '+
								'SET user_id = ?, nickname = ?, password = ? WHERE idadmin = ?', [user_id, nickname, password, idadmin], function(err, info) {
									if ( err ) {
										response_json  = {'success':0, 'err_msg':'Database query error!'};
										socket.emit('ban_user_response', response_json);
										throw err;
										return;
									}
									
									//
									if ( json.ban_user_name.length > 0 ) {
										var ban_user_name  = json.ban_user_name;
										var ban_period     = json.ban_period;
										
										var fullDate       = new Date();
										var year           = fullDate.getFullYear();
										var month          = fullDate.getMonth()+1;
										var date           = fullDate.getDate();
										var hour           = fullDate.getHours();
										var min            = fullDate.getMinutes();
										var sec            = fullDate.getSeconds();
										
										var cur_time_string   = year+'-'+month+'-'+date+' '+hour+':'+min+':'+sec;
										
										var chat_permission  = 0;
										var ban_last_time  = cur_time_string;
										
										mysqlClient.query(
											'SELECT * FROM '+users_table_name+' WHERE nickname="'+ban_user_name+'"',
											function selectCb(err, results, fields) {
												if ( err ) {
													response_json  = {'success':0, 'err_msg':'Database query error!'};
													socket.emit('ban_user_response', response_json);
													
													throw err;
													return;
												}
												
												if ( results.length > 0 ) {	
													var ban_user_id     = results[0].id;
													
													mysqlClient.query(
														'UPDATE '+users_table_name+' '+
														'SET chat_permission = ?, ban_last_time = ?, ban_period = ? WHERE nickname = ?', [chat_permission, ban_last_time, ban_period, ban_user_name], function(err, info) {
															if ( err ) {
																response_json  = {'success':0, 'err_msg':'Database query error!'};
																socket.emit('ban_user_response', response_json);
																throw err;
																return;
															}
															
															is_admin_update = 1;  is_ban_user = 1;
															response_json = {'success':1, 'err_msg':'', 'is_admin_update':is_admin_update, 'is_ban_user':is_ban_user, 'ban_user_id':ban_user_id};
															socket.emit('ban_user_response', response_json);
															
															response_json = {'success':1, 'err_msg':'', 'is_admin_update':is_admin_update, 'is_ban_user':is_ban_user, 'ban_user_id':ban_user_id, 'ban_user_name':ban_user_name};
															socket.broadcast.emit('ban_user_response', response_json);
															
															return;
														}
													);
												} else {
													response_json  = {'success':0, 'err_msg':'User ID could not be found!'};
													socket.emit('ban_user_response', response_json);
													return;
												}
											}
										);
										
									} else {
										is_admin_update = 1;  is_ban_user = 0;
										response_json = {'success':1, 'err_msg':'', 'is_admin_update':is_admin_update, 'is_ban_user':is_ban_user};
										socket.emit('ban_user_response', response_json);
										return;
									}
									//
								}
							);
						} else {
							response_json  = {'success':0, 'err_msg':'User ID could not be found!'};
							socket.emit('ban_user_response', response_json);
							
							return;
						}
					}
				);
			} else {
				//
				if ( json.ban_user_name.length > 0 ) {
					var ban_user_name  = json.ban_user_name;
					var ban_period     = json.ban_period;
					
					var fullDate       = new Date();
					var year           = fullDate.getFullYear();
					var month          = fullDate.getMonth()+1;
					var date           = fullDate.getDate();
					var hour           = fullDate.getHours();
					var min            = fullDate.getMinutes();
					var sec            = fullDate.getSeconds();
					
					var cur_time_string   = year+'-'+month+'-'+date+' '+hour+':'+min+':'+sec;
					
					var chat_permission  = 0;
					var ban_last_time  = cur_time_string;
					
					mysqlClient.query(
						'SELECT * FROM '+users_table_name+' WHERE nickname="'+ban_user_name+'"',
						function selectCb(err, results, fields) {
							if ( err ) {
								response_json  = {'success':0, 'err_msg':'Database query error!'};
								socket.emit('ban_user_response', response_json);
								
								throw err;
								return;
							}
							
							if ( results.length > 0 ) {	
								var ban_user_id     = results[0].id;
								
								mysqlClient.query(
									'UPDATE '+users_table_name+' '+
									'SET chat_permission = ?, ban_last_time = ?, ban_period = ? WHERE nickname = ?', [chat_permission, ban_last_time, ban_period, ban_user_name], function(err, info) {
										if ( err ) {
											response_json  = {'success':0, 'err_msg':'Database query error!'};
											socket.emit('ban_user_response', response_json);
											throw err;
											return;
										}
										
										is_admin_update = 0;  is_ban_user = 1;
										response_json = {'success':1, 'err_msg':'', 'is_admin_update':is_admin_update, 'is_ban_user':is_ban_user, 'ban_user_id':ban_user_id};
										socket.emit('ban_user_response', response_json);
										
										response_json = {'success':1, 'err_msg':'', 'is_admin_update':is_admin_update, 'is_ban_user':is_ban_user, 'ban_user_id':ban_user_id, 'ban_user_name':ban_user_name};
										socket.broadcast.emit('ban_user_response', response_json);
										
										return;
									}
								);
							} else {
								response_json  = {'success':0, 'err_msg':'User ID could not be found!'};
								socket.emit('ban_user_response', response_json);
								return;
							}
						}
					);
										
				} else {
					is_admin_update = 0;  is_ban_user = 0;
					response_json = {'success':1, 'err_msg':'', 'is_admin_update':is_admin_update, 'is_ban_user':is_ban_user};
					socket.emit('ban_user_response', response_json);
					return;
				}
				//
			}
		});
		
		socket.on('connect', function() {
			console.log('connect!');
		});
		
		socket.on('disconnect', function() {
			console.log('disconnect!');
			
			var response_json  = null;
			
			if ( socket.user == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('disconnect_response', response_json);
				return;
			}
				
			if ( socket.user.user_id == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('disconnect_response', response_json);
				return;
			}
			
			if ( socket.user.nickname == undefined ) {
				response_json  = {'success':0, 'err_msg':'Invalid session!'};
				socket.emit('disconnect_response', response_json);
				return;
			}
				
			var user_id   = socket.user.user_id;
			var nickname  = socket.user.nickname;
			
			delete users[nickname];
			
			var is_online  = 0;
			var room_id    = 0;
			mysqlClient.query(
				'UPDATE '+users_table_name+' '+
				'SET is_online = ?, room_id = ? WHERE id = ?', [is_online, room_id, user_id], function(err, info) {
					if ( err ) {
						response_json  = {'success':0, 'err_msg':'Database query error!'};
						socket.emit('disconnect_response', response_json);
						throw err;
						return;
					}
					
					response_json = {'success':1, 'err_msg':'', 'msg':'Disconnected!'};
					io.sockets.emit('disconnect_response', response_json);
				}
			);
			
		});
	});

	server.listen(5555);
	
}).call(this);