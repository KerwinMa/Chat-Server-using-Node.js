<?php 

	require_once('includes/conn.php');
	require_once('includes/funcs_users.php');
	require_once('includes/class_user.php');
	
	
	if ( !isset($_POST["nickname"]) ) {
		header("Location: myfriends.html");
		exit;
	} else {
		$link = Connect();
		
		$nickname = $_POST['nickname']; 
		
		$login_query  = "SELECT * FROM users WHERE nickname='$nickname'";
		$result = mysql_query($login_query, $link) or die(mysql_error());
		$row_checks   = mysql_fetch_assoc($result);
		$totalRows_checks = mysql_num_rows($result);
		if ( $totalRows_checks == 0 ) {
			// user not found (Incorrect nickname or password)
			// echo json with success = 0
			$response["success"]    = 0;
			$response["error_msg"]  = "Friends for that name doesn't exist!";
			echo json_encode($response);
		} else {
			$response["success"]    = 1;
			$response["error_msg"]  = "";
			$response["nickname"]   = $row_checks['nickname'];
			$response["email"]      = $row_checks['email'];
			$response["sex"]        = $row_checks['sex'];
			$response["age"]        = $row_checks['age'];
			$response["position"]   = $row_checks['position'];
			
			echo json_encode($response);
		}

		mysql_free_result($result);
	}
	
?>
