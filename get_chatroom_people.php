<?php
	
	require_once('includes/conn.php');
	
	
	$response  = array("success" => 0, "err_msg" => "");
	
	$link      = Connect();
	
	$current_room_id = -1;
	if ( isset($_GET['room_id']) )
		$current_room_id = $_GET['room_id']; 
	
	$room_id_array = array(1000, 2000, 3000, 4000, 5000);
	for ( $i=0; $i<count($room_id_array); $i++ ) {
		$room_id  = $room_id_array[$i];
		$query    = "SELECT * FROM users WHERE room_id='$room_id'";
		$result   = mysql_query($query, $link) or die(mysql_error());
	
		$peoples = 0;
		if ( mysql_num_rows($result) == 0 ) {
			$response["people"][$i] = $room_id . "-0";
		} else {
			while ( $row = mysql_fetch_array($result) ) {
				$peoples++;
			}
			
			$response["people"][$i] = $room_id . "-" . $peoples;
		}
	}

	$response["success"]  = 1; 
	$response["err_msg"]  = "";
	
	echo json_encode($response);	
	
?>