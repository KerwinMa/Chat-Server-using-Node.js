<?php

	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}
	
	//
	if ( !isset($_GET['expel_user_id']) || !isset($_GET['room_id']) ) {
		if ( isset($_GET['paged']) ) {
			$paged = $_GET['paged'];
			header("Location: view_users_in_chatroom.php?action=expel&paged=" . $paged . "&room_id=" . $room_id);
		} else {
			header("Location: view_users_in_chatroom.php?action=expel&room_id=" . $room_id);
		}	
		die();		
	}
	
	$expel_user_id = $_GET['expel_user_id'];
	$room_id = $_GET['room_id'];
	
	$link = Connect();
	
	$query = "UPDATE users SET room_id='0' WHERE id='$expel_user_id'";
	$result = mysql_query($query, $link) or die(mysql_error());
	
	$query = "UPDATE users SET chat_permission='0' WHERE id='$expel_user_id'";
	$result = mysql_query($query, $link) or die(mysql_error());
	
	if ( isset($_GET['paged']) ) {
		$paged = $_GET['paged'];
		header("Location: view_users_in_chatroom.php?action=expel&paged=" . $paged . "&room_id=" . $room_id);
	} else {
		header("Location: view_users_in_chatroom.php?action=expel&room_id=" . $room_id);
	}	
	
	die();

?>