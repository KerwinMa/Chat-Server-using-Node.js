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
		<?php require_once('get_users.php'); ?>
		<?php require_once('conf.php'); ?>
		
		<div id="admincontainer">
		
			<?php 
				require_once('left_menuwrap.html') 
				
			?>
			
			<div id="admincontent" class="right-panel">
				
				<div id="view-chatrooms-container" class="table-div">
					
					<div id="icon-view-chatrooms" class="icon32 icon-view-chatrooms"></div>
					<h2>View / Update Users</h2>
					
					<form id="view-chatrooms-form" action method="post">
					
						<table class="chatting-table widefat fixed bookmarks" style="width:100%; height:100px;">
						
							<thead>
								<tr>
									<th scope="col" class="manage-column column-cb check-column for-users-table" style="padding-top:19px;">
										<input id="cb" type="checkbox" onclick="toggleAllUsers(this.checked, this.id)">
									</th>
									
									<th scope="col" class="manage-column column-edit">
										<span></span>
									</th>
									<th scope="col" class="manage-column column-remove">
										<span></span>
									</th>
									
									<th scope="col" id="no" class="manage-column column-no" style="width:8%;">
										<span>No.</span>
									</th>
									<th scope="col" id="id" class="manage-column column-id">
										<span>User ID</span>
									</th>
									<th scope="col" id="nickname" class="manage-column column-nickname">
										<span>NickName</span>
									</th>
									<th scope="col" id="email" class="manage-column column-email">
										<span>E-mail</span>
									</th>
									<th scope="col" id="sex" class="manage-column column-sex">
										<span>Sex</span>
									</th>
									<th scope="col" id="age" class="manage-column column-age">
										<span>Age</span>
									</th>
									<th scope="col" id="position" class="manage-column column-position">
										<span>Position</span>
									</th>
									<th scope="col" id="room_id" class="manage-column column-room-id">
										<span>Current Room ID</span>
									</th>
									<th scope="col" id="chat_permission" class="manage-column column-chat-permission">
										<span>Chat Permission</span>
									</th>	
								</tr>
							</thead>
							
							<tfoot>
								<tr>
									<th scope="col" class="manage-column column-cb check-column for-users-table" style="padding-top:19px;">
										<input id="cb" type="checkbox" onclick="toggleAllUsers(this.checked, this.id)">
									</th>
									
									<th scope="col" class="manage-column column-edit">
										<span></span>
									</th>
									<th scope="col" class="manage-column column-remove">
										<span></span>
									</th>
									
									<th scope="col" class="manage-column column-no">
										<span>No.</span>
									</th>
									<th scope="col" class="manage-column column-id">
										<span>User ID</span>
									</th>
									<th scope="col" class="manage-column column-nickname">
										<span>NickName</span>
									</th>
									<th scope="col" class="manage-column column-email">
										<span>E-mail</span>
									</th>
									<th scope="col" class="manage-column column-sex">
										<span>Sex</span>
									</th>
									<th scope="col" class="manage-column column-age">
										<span>Age</span>
									</th>
									<th scope="col" class="manage-column column-position">
										<span>Position</span>
									</th>
									<th scope="col" class="manage-column column-room-id">
										<span>Current Room ID</span>
									</th>
									<th scope="col" class="manage-column column-chat-permission">
										<span>Chat Permission</span>
									</th>	
								</tr>
							</tfoot>
							
							<tbody>
								<?php
								$action = "";
								if ( isset($_GET["action"]) )
									$action = $_GET["action"];
									
								$paged = 1; 
								if ( isset($_GET["paged"]) )
									$paged = $_GET["paged"];
								if ( $paged <= 0 )
									$paged = 1;
								
								?>
								<script> var paged = "<?php echo $paged; ?>" </script>
								<?php
								
								for ( $index=0; $index<$items_per_page; $index++ ) {
									$i = $index + ($paged-1) * $items_per_page;
									
									if ( $i == count($users_array) )
										break;
									if ( $i >= count($users_array) )
										break;
										
									$user_no     = $users_array[$i]["no"];
									$user_id     = $users_array[$i]["id"];
									$nickname    = $users_array[$i]["nickname"];
									$email       = $users_array[$i]["email"];
									$sex         = $users_array[$i]["sex"];
									if ( $sex == 0 )
										$sex = "Male";
									else
										$sex = "Female";
					
									$age         = $users_array[$i]["age"];
									$position    = $users_array[$i]["position"];
									$room_id     = $users_array[$i]["room_id"];
									$chat_permission = $users_array[$i]["chat_permission"];
									?>
								<tr id="user_<?php echo $user_id; ?>" valign="middle" class="alternate" onmouseover="overRemoveActionLink(this.id);" onmouseout="outRemoveActionLink(this.id);">
									<th scope="col" id="user_<?php echo $user_id; ?>" class="column-cb check-column view-users-td">
										<input id="user_<?php echo $user_id; ?>" type="checkbox">
									</th>
									
									<td align="center" class="view-users-td">										
										<a class="submitedit" href="edit_user.php?user_id=<?php echo $user_id; ?>&room_id=<?php echo $room_id; ?>&paged=<?php echo $paged; ?>">
											<img src="images/b_edit.png" title="Edit" alt="Edit" class="icon" width="16" height="16"></img>
										</a>
									</td>
									
									<td align="left" class="view-users-td">
										<?php if ( $action == "expel" ) { ?>
										<a class="submitexpel" href="expel_user.php?user_id=<?php echo $user_id; ?>&room_id=<?php echo $room_id; ?>&paged=<?php echo $paged; ?>" onclick="if ( confirm( 'You are about to expel this user from this chatting room.\n  \'Cancel\' to stop, \'OK\' to expel.' ) ) { return true; } return false;">
										<?php } else if ( $action == "remove" ) { ?>
										<a class="submitremove" href="remove_user.php?remove_user_id=<?php echo $user_id; ?>&paged=<?php echo $paged; ?>" onclick="if ( confirm( 'You are about to remove this user from this chatting application.\n  \'Cancel\' to stop, \'OK\' to remove.' ) ) { return true; } return false;">
										<?php } ?>
											<img src="images/b_drop.png" title="Remove" alt="Remove" class="icon" width="16" height="16"></img>
										</a>
									</td>
									
									<td id="user_<?php echo $user_id; ?>" class="column-no view-users-td"><span><?php echo $user_no; ?></span></td>
									<td id="user_<?php echo $user_id; ?>" class="column-id view-users-td"><span><?php echo $user_id; ?></span></td>
									<td id="user_<?php echo $user_id; ?>" class="column-nickname view-users-td">
										<span><?php echo $nickname; ?></span>
										<div id="action_div_<?php echo $user_id; ?>" class="row-actions" style="visibility:hidden;">
											<span>
												<?php if ( $action == "expel" ) { ?>
												<a class="submitexpel" href="expel_user.php?user_id=<?php echo $user_id; ?>&room_id=<?php echo $room_id; ?>&paged=<?php echo $paged; ?>" onclick="if ( confirm( 'You are about to expel this user from this chatting room.\n  \'Cancel\' to stop, \'OK\' to expel.' ) ) { return true; } return false;">Expel</a>
												<?php } else if ( $action == "remove" ) { ?>
												<a class="submitremove" href="remove_user.php?remove_user_id=<?php echo $user_id; ?>&paged=<?php echo $paged; ?>" onclick="if ( confirm( 'You are about to remove this user from this chatting application.\n  \'Cancel\' to stop, \'OK\' to remove.' ) ) { return true; } return false;">Remove</a>
												<?php } ?>
											</span>
										</div>
									</td>
									<td id="user_<?php echo $user_id; ?>" class="column-email view-users-td"><span><?php echo $email; ?></span></td>									
									<td id="user_<?php echo $user_id; ?>" class="column-sex view-users-td"><span><?php echo $sex; ?></span></td>
									<td id="user_<?php echo $user_id; ?>" class="column-age view-users-td"><span><?php echo $age; ?></span></td>
									<td id="user_<?php echo $user_id; ?>" class="column-position view-users-td"><span><?php echo $position; ?></span></td>
									<td id="user_<?php echo $user_id; ?>" class="column-room-id view-users-td"><span><?php echo $room_id; ?></span></td>
									<td id="user_<?php echo $user_id; ?>" class="column-chat-permission view-users-td"><span><?php echo $chat_permission; ?></span></td>
								</tr>
									<?php
								}
								?>
								
							</tbody>
							
						</table>
						
						<div class="tablenav bottom">
							<div class="alignleft actions">
								<select name="action_users" id="action_users">
									<option value="remove" selected="selected">Remove</option>
								</select>
								<input type="button" name="apply" id="doRemove" class="button-secondary action" value="Apply" onclick="onApplyToUsers();">
							</div>
							<div class="alignleft actions"></div>
							<?php 
							$prev_page = $paged - 1;
							$next_page = $paged + 1;
							
							$last_page = 1;
							$i = 0;
							while (true) {
								for ( $index=0; $index<$items_per_page; $index++ ) {
									$i = $index + ($last_page-1) * $items_per_page;
									if ( $i == count($users_array) )
										break;
									if ( $i >= count($users_array) )
										break;
								}
								
								if ( $i == count($users_array) )
									break;
								if ( $i >= count($users_array) )
									break;
									
								$last_page++;
							}
							
							if ( count($users_array) > $items_per_page ) { ?>
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo count($users_array); ?> items</span>
								
								<span class="pagination-links">
								
									<?php if ( $prev_page == 0 ) { ?>
									<a class="first-page disabled" title="Go to the first page" href="#">&laquo;</a>
									<a class="prev-page disabled" title="Go to the previous page" href="#">&lsaquo;</a>
									<?php } else { ?>
									<a class="first-page" title="Go to the first page" href="view_users.php?action=remove">&laquo;</a>
									<a class="prev-page" title="Go to the previous page" href="view_users.php?action=remove&paged=<?php echo $prev_page; ?>">&lsaquo;</a>
									<?php } ?>
									
									<span class="paging-input">
									
									</span>
									<?php
									if ( ($paged * $items_per_page) <= count($users_array) ) {
									?>
									<a class="next-page" title="Go to the next page" href="view_users.php?action=remove&paged=<?php echo $next_page; ?>">&rsaquo;</a>
									<a class="last-page" title="Go to the last page" href="view_users.php?action=remove&paged=<?php echo $last_page; ?>">&raquo;</a>
									<?php } else { ?>
									<a class="next-page disabled" title="Go to the next page" href="#">&rsaquo;</a>
									<a class="last-page disabled" title="Go to the last page" href="#">&raquo;</a>
									<?php } ?>
								</span>
								
							</div>
							<?php } ?>
							
							<br class="clear">
						</div>
						
					</form>
					
				</div>
				
			</div>
			
		</div>
		
	</body>
	
</html>