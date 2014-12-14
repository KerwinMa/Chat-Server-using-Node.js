<?php

	require_once('includes/conn.php');

	
	$response = array("success" => 0, "err_msg" => "");
	
	if ( !isset($_GET['userId']) || !isset($_GET['friend']) ) {
		$response["success"] = 0;
		$response["err_msg"] = "Insufficient get parameters.";
		echo json_encode($response);
		die();
	}
	
	$userId  = $_GET['userId'];
	$friend  = $_GET['friend'];
	
	$link    = Connect();
	
	// run a query and get the user_id for the `friend` ...
	$friend_id_query   = "SELECT id FROM users WHERE nickname='$friend'";
	$result            = mysql_query($friend_id_query, $link) or die(mysql_error());
	$row_checks        = mysql_fetch_assoc($result);
	$totalRows_checks  = mysql_num_rows($result);
	
	$friendId = 0;
	if ( $totalRows_checks == 0 ) {
		$response["success"]  = 0;
		$response["err_msg"]  = "Friend ID cannot be found.";
		echo json_encode($response);
		die();
	} else {
		$friendId = $row_checks['id'];
	}
	
	$query = "SELECT * FROM friends WHERE user_id='$userId' AND user_id2='$friendId'";
	$result = mysql_query($query, $link) or die(mysql_error());
	if ( mysql_num_rows($result) != 0 ) {
		$response["success"]  = 0;
		$response["err_msg"]  = "The request has already been made.";
		
		echo json_encode($response);
		die();
	}
	
	// run a query and add one row to the friends table, so that the `friends request` will be sent to the friend when he will login ...
	$add_friends_request_query   = "INSERT INTO friends (user_id, user_id2, approved) VALUES (" . $userId . ", " . $friendId . ", 0)";
	$add_friends_request_result  = mysql_query($add_friends_request_query, $link) or die(mysql_error());
	
	$response["success"] = 1;
	$response["err_msg"] = "";
	
	$response["send_friend_request"] = $friendId . "-" . $friend;
	
	// run a query and get the e-mail address for the friend ...
	/*$friend_email_query  = "SELECT email FROM users WHERE id='$friend'";
	$result = mysql_query($friend_email_query, $link) or die(mysql_error());
	$row_checks   = mysql_fetch_assoc($result);
	$totalRows_checks = mysql_num_rows($result);
	if ( $totalRows_checks == 0 ) {
		$response["success"] = 0;
		$response["err_msg"] = "Your friend's email address is invalid.";
		echo json_encode($response);
		exit;
	} else {
		$friend_email = $row_checks['email'];
	}
	
	// send an e-mail message to the friend's e-mail address ...
	$header = "MIME-Version: 1.0\r\n";
	$header .= "Content-type: text/plain; charset=utf-8\r\n";
	$header .= "From: " . $userId . ">\r\n";
	
	$subject = "Friend Request!";
	$message = "You are requested to be a friend from " . $userName;
	
	mail($friend_email, $subject, $message, $header);
	
	if ( mail($friend_email, $subject, $message, $header) ) {
		$response["success"] = 1;
		$response["err_msg"] = "E-mail has been sent successfully.";
	} else {
		$response["success"] = 0;
		$response["err_msg"] = "E-mail has not been sent successfully.";
	}*/
	
	echo json_encode($response);
	
?>