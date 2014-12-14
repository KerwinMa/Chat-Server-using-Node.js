<?php

	////////////////////////////////////////////////////////////
	//
	//	get the list of send or receive friends request ...
	//
	
	require_once('includes/conn.php');
	
	$response = array("success" => 0, "err_msg" => "");
	
	if ( !isset($_GET["userId"]) ) {
		$response["success"]  = "0";
		$response["err_msg"]  = "Insufficient get parameters!";
		echo json_encode($response);
		exit;
	}
	
	$link     = Connect();
	
	$userId   = $_GET["userId"];
	
	for ( $index=0; $index<2; $index++ ) {
	
		if ( $index == 0 )
			$query = "SELECT user_id2 FROM friends WHERE user_id='$userId' AND approved=0"; 
		else if ( $index == 1 )
			$query = "SELECT user_id FROM friends WHERE user_id2='$userId' AND approved=0"; 
			
		$result = mysql_query($query, $link) or die(mysql_error());
		
		$friends_id_array = array();
		while ( $row = mysql_fetch_array($result) ) {
			$friends_id_array[] = $row[0];
		}
		
		$friends_array = array();
		
		$friends_name_array = array();
		if ( count($friends_id_array) == 0 ) {
			$friends_name_array_string = "";
		} else {
			for ( $i=0; $i<count($friends_id_array); $i++ ) {
				$friend_id  = $friends_id_array[$i];
				
				$query      = "SELECT nickname FROM users WHERE id='$friend_id'";
				$result     = mysql_query($query, $link) or die(mysql_error());
				$row        = mysql_fetch_assoc($result);
				
				$friends_name_array[$i]  = $row['nickname'];
				
				$friends_array[$i]       = $friends_id_array[$i] . "-" . $friends_name_array[$i];
			}
			
			$friends_name_array_string   = implode(",", $friends_array);
		}
		
		if ( $index == 0 )
			$response["send_friends_list"]     = $friends_name_array_string;
		else if ( $index == 1 )
			$response["receive_friends_list"]  = $friends_name_array_string;
			
	}
	
	$response["success"]  = 1;
	$response["err_msg"]  = ""; 
	
	echo json_encode($response);

?>