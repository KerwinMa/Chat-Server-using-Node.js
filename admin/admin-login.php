<?php
	
	require_once('../includes/conn.php');
	require_once('class_logedin_user.php');
	
	
	if ( !isset($_GET['action']) ) {
		header("Location: index.php"); die();
	}
	$action = $_GET['action'];
	
	$link = Connect();
	
	if ( $action == "login" ) {
	
		if ( !empty($_POST) && isset($_POST['login-submit']) ) {
			$nickname = trim($_POST["nickname"]);
			$password = trim($_POST["password"]);
		}
		
		/*if ( ($nickname != "admin") && ($nickname != "administrator") ) {
			$err_msg = "User name must be 'admin' or 'administrator'.";
			header("Location: index.php?err_msg=" . $err_msg); die(); 
		}*/
		
		$errors = array();
		if ( $nickname == "" )
			$errors[] = "Please enter admin username.";
		if ( $password == "" )
			$errors[] = "Please enter password.";
	
		if ( count($errors) == 0 ) {
		
			$passmod   = md5($password);
			$admin_login_query = "SELECT * FROM admin WHERE nickname='$nickname' AND password='$passmod'";
			$result = mysql_query($admin_login_query, $link) or die(mysql_error());
			$row_checks   = mysql_fetch_assoc($result);
			$totalRows_checks = mysql_num_rows($result);
			if ( $totalRows_checks == 0 ) {
				$err_msg = "Invalid username or password.";
				header("Location: index.php?err_msg=" . $err_msg); die(); 
			} else {
				$loggedIn = new loggedIn();
				$loggedIn->password = $password;
				
				$_SESSION["adminUser"] = $loggedIn;
				$_SESSION["admin_password"]  = $password;

				header("Location: main.php"); die();
			}
			
		} else {	
			
		}
		
	} else if ( $action == "logout" ) {
		destorySession("adminUser");
		destorySession("admin_password");
			
		header("Location: index.php"); die();
		
	} else if ( $action == "register" ) {
		
	}
	
?>