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
		
   </head>

	<body>

		<?php require_once('navbar.html'); ?>
		
		<div id="admincontainer">
		
			<?php require_once('left_menuwrap.html') ?>
			
			<div id="admincontent" class="right-panel">
				
				<div id="view-chatrooms-container" class="table-div">
					
					<div id="icon-view-chatrooms" class="icon32 icon-view-chatrooms"></div>
					<h2>View Profile</h2>
					
					<form id="loginform" name="loginform" action="update_password.php?action=update_password" method="post">
					
						<div id="view-profile-div">
							<p>
								<label for="old_password">
									Old Password
									<br>
									<input type="password" name="old_password" id="old_password" class="input" value size="20" tabindex="10">
								</label>
							</p>
							<p>
								<label for="new_password">
									New Password
									<br>
									<input type="password" name="new_password" id="new_password" class="input" value size="20" tabindex="10">
								</label>
							</p>
							<p>
								<label for="confirm_password">
									Confirm Password
									<br>
									<input type="password" name="confirm_password" id="confirm_password" class="input" value size="20" tabindex="10">
								</label>
							</p>
							
							<p class="submit">
								<input type="submit" name="change-submit" id="change-submit" class="button-primary" value="Change Password" tabindex="100" style="color:white;">
								<!--<input type="hidden" name="redirect_to" value="index.php">-->
								<p>
									<?php
										if ( isset($_GET["err_msg"]) ) {
											$err_msg = $_GET["err_msg"];
											echo $err_msg;
										}
									?>
								</p>
							</p>
						</div>
						
					</form>
					
				</div>
				
			</div>
			
		</div>
		
	</body>
	
</html>