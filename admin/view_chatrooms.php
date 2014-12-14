<?php
	
	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}

?>

<html lang="en">

	<head>
		<meta charset="utf-8">
		<title>Just A Fun Chat</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Just A Fun Chat">
		<meta name="author" content="">

		<!-- Le styles -->
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/navbar_style.css" rel="stylesheet">
		<link href="css/container_style.css" rel="stylesheet">
		
		<script src="../js/jquery.js"></script>
		<script src="../js/bootstrap-dropdown.js"></script>
		<script src="../js/custom-funcs.js"></script>
		
   </head>

	<body>

		<?php require_once('navbar.html'); ?>
		<?php require_once('get_chatroom_people.php'); ?>
		
		<div id="admincontainer">
		
			<?php require_once('left_menuwrap.html') ?>
			
			<div id="admincontent" class="right-panel">
				
				<div id="view-chatrooms-container" class="table-div">
					
					<div id="icon-view-chatrooms" class="icon32 icon-view-chatrooms"></div>
					<h2>View Chatrooms</h2>
					
					<form id="view-chatrooms-form" name="view-chatrooms-form" action method="post">
					
						<table class="chatting-table widefat fixed bookmarks" cellspacing="0">
						
							<thead>
								<tr>									
									<th scope="col" id="no" class="manage-column column-no">
										<span>No.</span>
									</th>
									<th scope="col" id="id" class="manage-column column-id">
										<span>Room ID</span>
									</th>
									<th scope="col" id="name" class="manage-column column-name">
										<span>Room Name</span>
									</th>
									<th scope="col" id="users" class="manage-column column-users">
										<span>Number of Users</span>
									</th>
								</tr>
							</thead>
							
							<tfoot>
								<tr>									
									<th scope="col" class="manage-column column-no">
										<span>No.</span>
									</th>
									<th scope="col" class="manage-column column-id">
										<span>Room ID</span>
									</th>
									<th scope="col" class="manage-column column-name">
										<span>Room Name</span>
									</th>
									<th scope="col" class="manage-column column-users">
										<span>Number of Users</span>
									</th>
								</tr>
							</tfoot>
							
							<tbody>
								<script> 
									var room_id_array = new Array();
								</script>
									
								<?php
								for ( $i=0; $i<count($chat_rooms_array); $i++ ) {
									$id    = $i + 1; 
									$tr_id = "chat_room-" . $id;
									
									$room_no     = $chat_rooms_array[$i]["No"];
									$room_id     = $chat_rooms_array[$i]["ID"];
									$room_name   = $chat_rooms_array[$i]["name"];
									$room_users  = $chat_rooms_array[$i]["users"];
									?>
									<script>
										var room_id = "<?php echo $room_id; ?>";
										room_id_array.push(room_id);
										var i = "<?php echo $i; ?>";
									</script>
									<?php
									?>
								<tr id="<?php echo $room_id; ?>" name="<?php echo $tr_id; ?>" valign="middle" class="alternate view-sub-detail">
									<td id="<?php echo $room_id; ?>" class="column-no view-chatrooms-td" onclick="showChatRoomDetails(this.id)"><span><?php echo $room_no; ?></span></td>
									<td id="<?php echo $room_id; ?>" class="column-id view-chatrooms-td" onclick="showChatRoomDetails(this.id)"><span><?php echo $room_id; ?></span></td>
									<td id="<?php echo $room_id; ?>" class="column-name view-chatrooms-td" onclick="showChatRoomDetails(this.id)"><span><?php echo $room_name; ?></span></td>
									<td id="<?php echo $room_id; ?>" class="column-users view-chatrooms-td" onclick="showChatRoomDetails(this.id)"><span><?php echo $room_users; ?></span></td>									
								</tr>
									<?php
								}
								?>
								
							</tbody>
							
						</table>
						
					</form>
					
				</div>
				
			</div>
			
		</div>
	
	</body>
	
</html>