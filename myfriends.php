<?php

	////////////////////////////////////////////////////////
	//
	//	returns the friends list ...
	//
	
	require_once('includes/conn.php');
	
	
	$response = array("success" => 0, "err_msg" => "");
	
	if ( !isset($_GET['userId']) ) {
		$response["success"] = 0;
		$response["err_msg"] = "Insufficient get parameters.";
		echo json_encode($response);
		exit;
	}
	
	$userId  = $_GET['userId'];
	
	$link    = Connect();
	
	// run a query and get the user_id for the `friend` ...
	$friend_id_query  = "SELECT * FROM friends WHERE (user_id='$userId' OR user_id2='$userId') AND approved=1";
	$result           = mysql_query($friend_id_query, $link) or die(mysql_error());
	
	if ( mysql_num_rows($result) == 0 ) {
		$response["success"] = 1;
		$response["err_msg"] = "No Friends.";
	} else {
		$friends_id_array = array();
		while ( $row = mysql_fetch_array($result) ) {
			if ( $userId == $row['user_id2'] )
				$friends_id_array[] = $row['user_id'];
			else if ( $userId == $row['user_id'] )
				$friends_id_array[] = $row['user_id2'];
		}
		
		$friends_array = array();
		
		$friends_name_array = array();
		
		if ( count($friends_id_array) == 0 ) {
			$friends_name_array_string = "";
		} else {	
			for ( $i=0; $i<count($friends_id_array); $i++ ) {
				$friend_id = $friends_id_array[$i];
				
				$friend_name_query  = "SELECT nickname FROM users WHERE id='$friend_id'";
				$result = mysql_query($friend_name_query, $link) or die(mysql_error());
				$row   = mysql_fetch_assoc($result);
				$totalRows = mysql_num_rows($result);
				
				if ( $totalRows == 0 )
					continue;
					
				$friends_name_array[] = $row['nickname'];
				
				$friends_array[$i] = $friends_id_array[$i] . "-" . $friends_name_array[$i];
			}
			
			$friends_name_array_string = implode(",", $friends_array);
		}
		
		$response["friends_list"] = $friends_name_array_string;
		
		$response["success"] = 1;
		$response["err_msg"] = "";
	}
	
	echo json_encode($response);
	
?>