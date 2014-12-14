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
			</div>
			
		</div>
		
	</body>
	
</html>