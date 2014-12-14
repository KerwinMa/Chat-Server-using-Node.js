<?php
	
	require_once('../includes/conn.php');
	require_once('funcs_user.php');
	
	
	if ( !isLoggedIn() ) { 
		header("Location: index.php"); die(); 
	}
	
	if ( !empty($_POST) && isset($_POST['submit']) ) {
		
		$link = Connect();
		if ( !isset($_GET["user_id"]) ) {
			die();
		}
		
		$user_id = $_GET["user_id"];	
		
		if ( isset($_POST['nickname']) ) {
			$nickname = $_POST['nickname'];
			$query = "UPDATE users SET nickname='$nickname' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
		
		if ( isset($_POST['email']) ) {
			$email = $_POST['email'];
			$query = "UPDATE users SET email='$email' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
			
		if ( isset($_POST['sex']) ) {
			$sex = $_POST['sex'];
			$query = "UPDATE users SET sex='$sex' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
			
		if ( isset($_POST['age']) ) {
			$age = $_POST['age'];
			$query = "UPDATE users SET age='$age' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
			
		if ( isset($_POST['position']) ) {
			$position = $_POST['position'];
			$query = "UPDATE users SET position='$position' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
		
		if ( isset($_POST['room_id']) ) {
			$room_id = $_POST['room_id'];
			$query = "UPDATE users SET room_id='$room_id' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
		
		if ( isset($_POST['chat_permission']) ) {
			$chat_permission = $_POST['chat_permission'];
			$query = "UPDATE users SET chat_permission='$chat_permission' WHERE id='$user_id'";
			$result = mysql_query($query, $link) or die(mysql_error());
		}
		
		header("Location: view_users.php?action=remove");
		
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
		<?php require_once('conf.php'); ?>
		
		<div id="admincontainer">
		
			<?php 
				require_once('left_menuwrap.html');
				
				$link = Connect();
				
				if ( !isset($_GET["user_id"]) ) {
					die();
				}
				
				$user_id = $_GET["user_id"];									
			?>
			
			<div id="admincontent" class="right-panel">
				
				<div id="view-chatrooms-container" class="table-div">
					
					<div id="icon-view-chatrooms" class="icon32 icon-view-chatrooms"></div>
					<h2>View / Update Users</h2>
					
					<form id="view-chatrooms-form" action="<?php echo $_SERVER['PHP_SELF']; ?>?user_id=<?php echo $user_id; ?>" method="post">
					
						<table class="chatting-table widefat fixed bookmarks" style="width:400px; height:100px;">
						
							<thead>
								<th scope="col" id="field" class="manage-column column-field" style="width:200px;">
									<span style="margin-left:80px;">Field</span>
								</th>
								<th scope="col" id="value" class="manage-column column-value" style="width:200px;">
									<span style="margin-left:80px;">Value</span>
								</th>
								<th>
									<span></span>
								</th>
							</thead>
								
							<tfoot>
								<th scope="col" id="field" class="manage-column column-field" align="center"><span></span></th>
								<th scope="col" id="value" class="manage-column column-value" align="center">
									<span><input type="submit" name="submit" Value="Go" class="button-primary update-user-submit" /></span>
								</th>
								<th>
									<span></span>
								</th>
							</tfoot>
							
							<tbody>
								<?php
									$paged = 1; 
									if ( isset($_GET["paged"]) )
										$paged = $_GET["paged"];
									if ( $paged <= 0 )
										$paged = 1;
										
									//
									$query  = "SELECT * FROM users WHERE id='$user_id'";
									$result = mysql_query($query, $link) or die(mysql_error()); 
									$row    = mysql_fetch_assoc($result);
									
									$nickname  = $row['nickname'];
									$email     = $row['email'];
									$sex       = $row['sex'];
									$age       = $row['age'];
									$position  = $row['position'];
									$room_id   = $row['room_id'];
									$chat_permission  = $row['chat_permission'];
								?>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>nickname</span>
									</td>
									<td class="column-nickname update-user-edit-td" align="center">
										<input type="text" name="nickname" id="nickname" class="update-user-edit-box" value="<?php echo $nickname; ?>" />
									</td>
								</tr>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>e-mail</span>
									</td>
									<td class="column-email update-user-edit-td" align="center">
										<input type="text" name="email" id="email" class="update-user-edit-box" value="<?php echo $email; ?>" />
									</td>
								</tr>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>sex</span>
									</td>
									<td class="column-sex update-user-edit-td" align="center">
										<input type="text" name="sex" id="sex" class="update-user-edit-box" value="<?php echo $sex; ?>" />
									</td>
								</tr>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>age</span>
									</td>
									<td class="column-age update-user-edit-td" align="center">
										<input type="text" name="age" id="age" class="update-user-edit-box" value="<?php echo $age; ?>" />
									</td>
								</tr>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>position</span>
									</td>
									<td class="column-position update-user-edit-td" align="center">
										<input type="text" name="position" id="position" class="update-user-edit-box" value="<?php echo $position; ?>" />
									</td>
								</tr>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>room id</span>
									</td>
									<td class="column-room-id update-user-edit-td" align="center">
										<input type="text" name="room_id" id="room_id" class="update-user-edit-box" value="<?php echo $room_id; ?>" />
									</td>
								</tr>
								<tr class="alternate">
									<td align="center" class="update-user-edit-td-1">
										<span>chat permission</span>
									</td>
									<td class="column-chat-permission update-user-edit-td" align="center">
										<input type="text" name="chat_permission" id="chat_permission" class="update-user-edit-box" value="<?php echo $chat_permission; ?>" />
									</td>
								</tr>
								<?php
								?>
								
							</tbody>
							
						</table>
						
						
						
					</form>
					
				</div>
				
			</div>
			
		</div>
		
	</body>
	
</html>