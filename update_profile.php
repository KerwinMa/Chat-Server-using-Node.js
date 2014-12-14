<?php 

	require_once('includes/conn.php');
	require_once('includes/funcs_users.php');
	require_once('includes/class_user.php');

	
	$response = array("success" => 0, "err_msg" => "");
	if ( !isset($_POST['userId']) ) {
		$response['success'] = 0;
		$response["err_msg"] = "Insufficient parameters!";
		echo json_encode($response);
		die();
	}
	
	$userId = $_POST['userId'];
	
	$link = Connect();
	
	if ( isset($_POST['nickname']) ) {
		$nickname = $_POST['nickname'];
		$query = "UPDATE users SET nickname='$nickname' WHERE id='$userId'";
		$result = mysql_query($query, $link) or die(mysql_error());
	}
	
	if ( isset($_POST['sex']) ) {
		$sex = $_POST['sex'];
		$query = "UPDATE users SET sex='$sex' WHERE id='$userId'";
		$result = mysql_query($query, $link) or die(mysql_error());
	}
	
	if ( isset($_POST['age']) ) {
		$age = $_POST['age'];
		$query = "UPDATE users SET age='$age' WHERE id='$userId'";
		$result = mysql_query($query, $link) or die(mysql_error());
	}
	
	if ( isset($_POST['email']) ) {
		$email = $_POST['email'];
		$query = "UPDATE users SET email='$email' WHERE id='$userId'";
		$result = mysql_query($query, $link) or die(mysql_error());
	}
		
	if ( isset($_POST['position']) ) {
		$position = $_POST['position'];
		$query = "UPDATE users SET position='$position' WHERE id='$userId'";
		$result = mysql_query($query, $link) or die(mysql_error());
	}
	
	$response["success"] = 1;
	$response["err_msg"] = "";
	
	echo json_encode($response);
	
	//mysql_free_result($result);
	
?>
