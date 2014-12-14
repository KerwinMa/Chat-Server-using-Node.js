<?php
	
	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}
	
	//
	$chat_rooms_array = array();
	
	$link = Connect();
	
	$room_array = array(); 
	$query = "SELECT * FROM chatrooms";
	$result = mysql_query($query, $link) or die(mysql_error()); 
	$i = 0;
	while ( $row = mysql_fetch_array($result) ) {
		$room_array[$i]["id"]    = $row["room_id"];
		$room_array[$i]["name"]  = $row["name"];
		$i++;
	}
	
	for ( $i=0; $i<count($room_array); $i++ ) {
	
		$chat_rooms_array[$i]["No"]    = $i+1;
		$chat_rooms_array[$i]["ID"]    = $room_array[$i]["id"];
		$chat_rooms_array[$i]["name"]  = $room_array[$i]["name"];
	
		$room_id = $room_array[$i]["id"];
		$query = "SELECT * FROM users WHERE room_id='$room_id'";
		$result = mysql_query($query, $link) or die(mysql_error());
	
		$users = 0;
		if ( mysql_num_rows($result) == 0 ) {
			$chat_rooms_array[$i]["users"] = 0;
		} else {
			while ( $row = mysql_fetch_array($result) ) {
				$users++;
			}
			
			$chat_rooms_array[$i]["users"] = $users;
		}
		
	}
	
?>