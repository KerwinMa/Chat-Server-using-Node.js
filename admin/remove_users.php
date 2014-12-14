<?php

	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}
	
	//
	if ( !isset($_GET['remove_users_id']) ) {
		if ( isset($_GET['paged']) ) {
			$paged = $_GET['paged'];
			header("Location: view_users.php?action=remove&paged=" . $paged);
		}
		die();
	}
	
	$remove_users_id_string = $_GET["remove_users_id"];
	$remove_users_id_array = explode(",", $remove_users_id_string);
	
	$link = Connect();
	
	for ( $i=0; $i<count($remove_users_id_array); $i++ ) {
		$user_id = $remove_users_id_array[$i];
		$query = "DELETE FROM users WHERE id='$user_id'";
		$result = mysql_query($query, $link) or die(mysql_error());
	}
	
	if ( isset($_GET['paged']) ) {
		$paged = $_GET['paged'];
		header("Location: view_users.php?action=remove&paged=" . $paged);
	} else {
		header("Location: view_users.php?action=remove");
	}

?>