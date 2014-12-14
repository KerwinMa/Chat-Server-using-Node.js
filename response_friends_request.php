<?php

	require_once('includes/conn.php');
	
	
	$response = array("success" => 0, "err_msg" => "");
	
	if ( !isset($_GET['tag']) || !isset($_GET['userId']) || !isset($_GET['friendId']) ) {
		$response["success"]  = 0;
		$response["err_msg"]  = "Insufficient get parameters."; 
		echo json_encode($response);
		exit;
	}
	
	$tag       = $_GET['tag']; 
	$userId    = $_GET['userId']; 
	$friendId  = $_GET['friendId'];
	
	$link      = Connect();
	
	if ( $tag == "follow" ) {
	
		$update_friend_query  = "UPDATE friends SET approved=1 WHERE user_id='$friendId' AND user_id2='$userId'";
		$result  = mysql_query($update_friend_query, $link) or die(mysql_error());
		if ( $result == FALSE ) {
			$response["success"]  = 0;
			$response["err_msg"]  = "Could not update the friend request.";
		} else {
			// get the nickname of friend 
			$friend_name_query  = "SELECT nickname FROM users WHERE id='$friendId'";
			$result             = mysql_query($friend_name_query, $link) or die(mysql_error());
			$row                = mysql_fetch_assoc($result);
			$totalRows          = mysql_num_rows($result);
			
			if ( $totalRows == 0 ) {
				$response["success"]      = 0; 
				$response["err_msg"]      = "Could not get the friend's nickname.";
			} else {
				$response["success"]      = 1; 
				$response["friend_name"]  = $row["nickname"];
				$response["tag"]          = "follow";
				$response["err_msg"]      = "";
			}
		}		

	} else if ( $tag == "unfollow" ) {
		
		//$delete_friend_query = "DELETE FROM friends WHERE user_id='$friendId' AND user_id2='$userId'";
		$delete_friend_query   = "DELETE FROM friends WHERE (user_id='$friendId' AND user_id2='$userId') OR (user_id='$userId' AND user_id2='$friendId')";
		$result                = mysql_query($delete_friend_query, $link) or die(mysql_error());
		if ( $result == FALSE ) {
			$response["success"]  = 0;
			$response["err_msg"]  = "Could not delete the friend request.";
		} else {
			// get the nickname of friend 
			$friend_name_query  = "SELECT nickname FROM users WHERE id='$friendId'";
			$result             = mysql_query($friend_name_query, $link) or die(mysql_error());
			$row                = mysql_fetch_assoc($result);
			$totalRows          = mysql_num_rows($result);
			
			if ( $totalRows == 0 ) {
				$response["success"]      = 0; 
				$response["err_msg"]      = "Could not get the friend's nickname.";
			} else {
				$response["success"]      = 1; 
				$response["friend_name"]  = $row["nickname"];
				$response["tag"]          = "unfollow";
				$response["err_msg"]      = "";
			}
		}
		
	}
	
	echo json_encode($response);
	
?>