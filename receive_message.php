	<?php

	require_once('includes/conn.php');
	
	
	$response = array("success" => 0, "err_msg" => "");
	
	
	$link = Connect();
	
	if ( !isset($_POST["room_id"]) ) {
		$response["success"] = 0;
		$response["err_msg"] = "Insufficient parameters!";
		echo json_encode($response); 
		exit;
	}
	
	$room_id      = $_POST["room_id"];
	
	// receiver_id = current user id ...
	$receiver_id  = -1;
	if ( isset($_POST["receiver_id"]) ) 
		$receiver_id  = $_POST["receiver_id"];
		
	// In case of private message(PM), sender_id = chatting-user id ...
	// while in case of public message, sender_id = -1 ...
	$sender_id    = -1;
	if ( isset($_POST["sender_id"]) )
		$sender_id    = $_POST["sender_id"];
		
	$date_time = NULL;
	$dt_string = "";
	
	date_default_timezone_set('UTC');
	if ( isset($_POST["dt"]) ) {
		$dt_string = $_POST["dt"];
		$dt_array  = explode(",", $dt_string);
		
		$date_time_string = $dt_array[2] . "-" . $dt_array[1] . "-" . $dt_array[0] . " " . $dt_array[3] . ":" . $dt_array[4] . ":" . $dt_array[5];
		$date_time = date('Y-m-d H:i:s', strtotime($date_time_string));
	}
		
	if ( $sender_id == -1 ) {
		
		if ( $date_time == NULL ) {
			$query = "SELECT * FROM messages WHERE room_id='$room_id' AND sender_id<>'$receiver_id' AND receiver_id='-1'";
		} else {
			$query = "SELECT * FROM messages WHERE room_id='$room_id' AND send_time>'$date_time' AND sender_id<>'$receiver_id' AND receiver_id='-1'";
		}
		
	} else {
		
		if ( $date_time == NULL ) {
			$query = "SELECT * FROM messages WHERE room_id='$room_id' AND sender_id='$sender_id' AND receiver_id='$receiver_id'";
		} else {
			$query = "SELECT * FROM messages WHERE room_id='$room_id' AND sender_id='$sender_id' AND receiver_id='$receiver_id' AND send_time>'$date_time'";
		}
		
	}	
	
	$result = mysql_query($query, $link) or die(mysql_error());			
			
	$message_string = array();
	while ( $row = mysql_fetch_array($result) ) {
		$message_string[] = array($row['sender_id'], $row['sender_name'], $row['msg']);
	}
	
	$response["success"] = 1;
	$response["err_msg"] = "";
	$response["msg"]     = $message_string;
	
	$dt                  = date('Y-m-d-H-i-s'); //date('Y-m-d H:i:s');
	list($year, $month, $day, $hour, $minute, $second) = explode("-", $dt);
	$response["dt"]      = $year . "," . $month . "," . $day . "," . $hour . "," . $minute . "," . $second;
	
	echo json_encode($response);

?>