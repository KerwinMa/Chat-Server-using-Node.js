<?php
	
	require_once('includes/conn.php');
	
	
	$response = array("success" => 0, "err_msg" => "");
	
	if ( !isset($_POST["userId"]) || !isset($_POST["roomId"]) ) {
		$response["success"]  = 0; 
		$response["err_msg"]  = "Insufficient parameters!";
		
		echo json_encode($response);
		exit; 
	}
	
	//
	$link    = Connect();
	
	$userId  = $_POST["userId"];
	$roomId  = $_POST["roomId"];
	
	$update_query  = "UPDATE users SET room_id='$roomId' WHERE id='$userId'";
	$result        = mysql_query($update_query, $link) or die(mysql_error());
	if ( $result == FALSE ) {
		$response["success"]  = 0;
		$response["err_msg"]  = "Execution of database query is failed!";
	} else {
		$response["success"]  = 1; 
		$response["err_msg"]  = "";
	}
	
	echo json_encode($response);
	
?>