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
					
					<form id="loginform" name="loginform" action method="post">
					
						<div id="view-profile-div">
							<p class="view-profile-paragraph">
								Username :  administrator/admin
							</p>
							
							<?php global $admin_password; ?>
							<p class="view-profile-paragraph">
								Password :  <?php echo $admin_password; ?>
							</p>
						</div>
						
					</form>
					
				</div>
				
			</div>
			
		</div>
		
	</body>
	
</html>