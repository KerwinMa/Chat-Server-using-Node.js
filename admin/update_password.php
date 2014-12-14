<?php
	
	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}

	//
	if ( isset($_GET["action"]) )
		$action = $_GET["action"];
		
	$link = Connect();
	
	if ( $action == "update_password" ) {
	
		if ( !empty($_POST) && isset($_POST['change-submit']) ) {
		
			$old_password = $_POST["old_password"]; 
			$new_password = $_POST["new_password"];
			$confirm_password = $_POST["confirm_password"]; 
			
			$old_password_secure = md5($old_password);
			
			$query = "SELECT * FROM admin";
			$result = mysql_query($query, $link) or die(mysql_error()); 
			$row_checks   = mysql_fetch_assoc($result);
			$totalRows_checks = mysql_num_rows($result);
			
			if ( $totalRows_checks == 0 ) {
				$err_msg = "No admins exist!";
				header("Location: change_password.php?err_msg=" . $err_msg); die();
			} else {
				
				$password = $row_checks["password"];
				if ( $password != $old_password_secure ) {
					$err_msg = "Incorrect old password!";
					header("Location: change_password.php?err_msg=" . $err_msg); die();
				} else {
				
					if ( $new_password != $confirm_password ) {
						$err_msg = "Passwords do not match!";
						header("Location: change_password.php?err_msg=" . $err_msg); die();
					}
					$_SESSION["admin_password"]  = $new_password;
					
					$new_password_secure = md5($new_password);
					
					$query = "UPDATE admin SET password='".$new_password_secure."'";
					$result = mysql_query($query, $link) or die(mysql_error()); 
					
					$err_msg = "Password has been changed successfully!";
					header("Location: change_password.php?err_msg=" . $err_msg); die();
				}
				
			}
		}		
		
	}
	
?>
