<?php

	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}
	
	//
	if ( !isset($_GET['remove_user_id']) ) {
		if ( isset($_GET['paged']) ) {
			$paged = $_GET['paged'];
			header("Location: view_users.php?action=remove&paged=" . $paged);
		}
		die();
	}
	
	$remove_users_id = $_GET["remove_user_id"];
	
	$link = Connect();
	
	$query = "DELETE FROM users WHERE id='$remove_users_id'";
	$result = mysql_query($query, $link) or die(mysql_error());
		
	if ( isset($_GET['paged']) ) {
		$paged = $_GET['paged'];
		header("Location: view_users.php?action=remove&paged=" . $paged);
	} else {
		header("Location: view_users.php?action=remove");
	}

?>