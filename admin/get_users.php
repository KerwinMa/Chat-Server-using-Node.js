<?php
	
	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}
	
	
	$users_array = array();
	
	$link = Connect();
	
	$query = "SELECT * FROM users";
	$result = mysql_query($query, $link) or die(mysql_error()); 
	$i = 0;
	while ( $row = mysql_fetch_array($result) ) {
		$users_array[$i]["no"]        = $i+1;
		$users_array[$i]["id"]        = $row["id"];
		$users_array[$i]["nickname"]  = $row["nickname"];
		$users_array[$i]["email"]     = $row["email"];
		$users_array[$i]["sex"]       = $row["sex"];
		$users_array[$i]["age"]       = $row["age"];
		$users_array[$i]["position"]  = $row["position"];
		$users_array[$i]["room_id"]   = $row["room_id"];
		$users_array[$i]["chat_permission"]   = $row["chat_permission"];
		
		$i++;
	}
	
?>