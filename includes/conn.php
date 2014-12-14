<?php
	
	session_start();
	
	function connect() {
		$db_host = "localhost";
		$db_name = "chatting_room";
		$db_user = "root";
		$db_password = ""; 
		
		$link = mysql_connect($db_host, $db_user, $db_password) or die ("Error selecting the database.");
		mysql_select_db($db_name, $link) or die("Error selecting the database."); 
		
		return $link;
	}
	
	if ( isset($_SESSION["adminUser"]) && is_object($_SESSION["adminUser"]) ) {
		$loggedIn = $_SESSION["adminUser"];
	}
	
	if ( isset($_SESSION["admin_password"]) ) {
		$admin_password = $_SESSION["admin_password"];
	}
	
?>