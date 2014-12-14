<?php

	require_once('includes/conn.php');
	
	
	$response = array("success" => 0, "err_msg" => "");
	
	if ( !isset($_POST["sender_id"]) || !isset($_POST["sender_name"]) || !isset($_POST["msg"]) || !isset($_POST["room_id"]) ) {
		$response["success"] = 0;
		$response["err_msg"] = "Insufficient parameters!";
		echo json_encode($response);
		exit;
	}
	
	$link = Connect();
	
	date_default_timezone_set('UTC');
	
	$sender_id    = $_POST["sender_id"];
	$sender_name  = $_POST["sender_name"];
	$dt           = date('Y-m-d H:i:s');
	$msg          = $_POST["msg"];
	$room_id      = $_POST["room_id"];
	
	$receiver_id  = -1; 
	if ( isset($_POST["receiver_id"]) ) 
		$receiver_id = $_POST["receiver_id"];
		
	$receiver_name = ""; 
	if ( isset($_POST["receiver_name"]) )
		$receiver_name = $_POST["receiver_name"];
		
	$read_status = 0;
	if ( isset($_POST["read_status"]) ) {
		$read_status = $_POST["read_status"];
	} else {
		if ( $receiver_id == -1 )
			$read_status = 1;
	}
	
	$query = "INSERT INTO messages (sender_id, receiver_id, sender_name, receiver_name, send_time, msg, read_status, room_id) VALUES ('$sender_id', '$receiver_id', '$sender_name', '$receiver_name', '$dt', '$msg', '$read_status', '$room_id')";
	$result = mysql_query($query, $link) or die(mysql_error());
	if ( $result == FALSE ) {
		$response["success"] = 0;
		$response["err_msg"] = "Execution of MySQL query is failed!";
		echo json_encode($response); 
		exit;
	} else {
		$response["success"] = 1;
		$response["err_msg"] = "";
	}
	
	echo json_encode($response); 

?>