<?php

	require_once('includes/conn.php');
	require_once('includes/funcs_users.php');
	require_once('includes/class_user.php');
	
	
	if ( isset($_GET['tag']) && $_GET['tag'] != '' ) {
	
		// get tag (login or register)
		$tag        = $_GET['tag'];
		
		// response Array
		$response   = array("tag" => $tag, "success" => 0);
		
		// check for tag type
		if ( $tag == 'login' ) {
		
			// Request type is check Login
			if ( !isset($_POST["nickname"] || !isset($_POST["password"]) ) ) {
			
				$response["success"] = 0;
				$response["err_msg"] = "Insufficient parameters!";
			
				echo json_encode($response); 
				die();
				
			} else {
				
				$link      = Connect();
				
				// get some useful values from the posted parameters ...
				$nickname  = $_POST["nickname"];
				$password  = $_POST["password"];
				$position  = $_POST["position"];
				
				$passmod   = $password;
				$passmod   = md5($passmod);
				
				$login_query        = "SELECT * FROM users where nickname='$nickname' AND password='$passmod'";
				$login_result       = mysql_query($login_query, $link) or die(mysql_error());
				$row_checks         = mysql_fetch_assoc($login_result);
				$totalRows_checks   = mysql_num_rows($login_result);
				if ( $totalRows_checks == 0 ) {
				
					// if user is not found (Incorrect nickname or password)
					// echo json with success = 0
					$response["success"] = 0;
					$response["err_msg"] = "Incorrect nickname or password!";
					
					echo json_encode($response);
					die();
					
				} else {
				
					$is_online   = $row_checks['is_online'];
					if ( $is_online == 1 ) {
						$response["success"] = 0;
						$response["err_msg"] = "The user is already online!";
					
						echo json_encode($response);
						die();
					}
					
					// if user is found, echo json with success = 1
					$response["success"]   = 1;
					$response["err_msg"]   = "";
					$response["nickname"]  = $row_checks['nickname'];
					$response["userid"]    = $row_checks['id'];
					
					$user_id    = $response["userid"];
					
					// if the user logs in, set 'is_online' field to '1' and set 'position' field to new value ...
					$is_online   = 1;
					$query       = "UPDATE users SET is_online='$is_online', position='$position' WHERE id='$user_id'";
					$result      = mysql_query($query, $link) or die(mysql_error());
					if ( $result == FALSE ) {
						$response["success"] = 0;
						$response["err_msg"] = "The user could not log in!";
					
						echo json_encode($response);
						die();
					}
					
					// if the user logs in, get the friends list requested ...
					$friend_request_list_query   = "SELECT user_id FROM friends WHERE user_id2='$user_id' AND approved=0";
					$result   = mysql_query($friend_request_list_query, $link) or die(mysql_error());
					
					$response["friends_request"] = "";
					$friends_id_request_array = array();
					if ( mysql_num_rows($result) != 0 ) {
						while ( $row_checks = mysql_fetch_array($result) ) {
							$friends_id_request_array[] = $row_checks['user_id'];
						}
						
						$friends_request_array = array();
						
						$friends_name_request_array = array();
						for ( $i=0; $i<count($friends_id_request_array); $i++ ) {
							$friend_id          = $friends_id_request_array[$i];
							
							$friend_name_query  = "SELECT nickname FROM users WHERE id='$friend_id'";
							$result             = mysql_query($friend_name_query, $link) or die(mysql_error());
							$row                = mysql_fetch_assoc($result);
							$totalRows          = mysql_num_rows($result);
							
							if ( $totalRows == 0 )
								continue;
								
							$friends_name_request_array[]  = $row['nickname'];
							
							$friends_request_array[$i]     = $friends_id_request_array[$i] . "-" . $friends_name_request_array[$i];
						}
						
						$response["friends_request"]       = implode(",", $friends_request_array); 
					}
					
				}
				
				echo json_encode($response);

				mysql_free_result($login_result);
			}
			
		} else if ( $tag == 'register' ) {
			// Request type is Register a new user
			$nickname                 = $_POST['nickname'];
			$email                    = $_POST['email'];
			$password                 = md5($_POST['password']);
			$sex                      = $_POST['sex'];
			$age                      = $_POST['age'];
			$position                 = $_POST['position'];
			$active                   = 0;
			$create_time              = date('Y-m-d H:i:s');
			$last_signin              = date('Y-m-d H:i:s');
			$last_activation_request  = date('Y-m-d H:i:s');
			$lost_password_request    = date('Y-m-d H:i:s');
			$room_id                  = 0; 
			$chat_permission          = 1;
			
			$link   = Connect();
			
			// check if this nickname is duplicated with the previous ones registered already...
			$duplication_query  = "SELECT id FROM users WHERE nickname='$nickname'";
			$result             = mysql_query($duplication_query, $link) or die(mysql_error());			
			$totalRows_checks   = mysql_num_rows($result);
			if ( $totalRows_checks != 0 ) {
				$response["success"]  = 0;
				$response["err_msg"]  = "The user already exists!";
			
				echo json_encode($response); 
				die();
			}
			//
			
			$register_query   = "INSERT INTO users (nickname, email, password, sex, age, position, active, create_time, last_signin, last_activation_request, lost_password_request, room_id, chat_permission) values ('$nickname','$email','$password','$sex','$age','$position','$active','$create_time','$last_signin','$last_activation_request', '$lost_password_request', '$room_id', '$chat_permission')";
			$register_result  = mysql_query($register_query, $link) or die(mysql_error());
			
			/*include ('includes/emailregistro.php');
			$envia = "PMD";
			$remite = "formacion@odpe.com";
			
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "From: $envia <$remite>\r\n"; 
			$headers .= "Content-type: text/html; charset=utf-8\r\n"; 
			
			$asunto = $nombre." ".$apellidos.", por favor active su cuenta en PMD";
			
			mail($correo, $asunto, $cuerpo, $headers) or die ("Error al Enviar su Registro!");*/
			
			//mysql_free_result($register_result);
			//
			$response["success"] = 1;
			$response["err_msg"] = "";
			
			echo json_encode($response); 
			die();
		}
		
	} else {
	
		echo "Access Denied";
		
	}
	
?>
