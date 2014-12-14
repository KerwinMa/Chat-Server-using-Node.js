<?php
	
	if ( isset($_GET['err_msg']) ) {
		$err_msg = $_GET['err_msg'];
	}
?>


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		
		<title>JustAFunChat &rsaquo; Log In</title>
		<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
		<meta name="description" content="" />
		<meta name="author" content="" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link href="css/style.css" rel="stylesheet">
		
		<meta name='robots' content='noindex,nofollow' />
		
	</head>
	
	<body class="login">
		
		<div id="login">
			<div class="logintitle">
				<h1>Just A Fun Chat</h1>
			</div>
			
			<?php
			if ( isset($err_msg) ) {
			?>
			<div id="err_msg_div">
				<p> <?php echo $err_msg; ?> </p>
			</div>
			<?php 
			}
			?>
				
			<form name="loginform" id="loginform" action="admin-login.php?action=login" method="post">
				<p>
					<label for="user_login">
						Username
						<br>
						<input type="text" name="nickname" id="nickname" class="input" value size="20" tabindex="10">
					</label>
				</p>
				
				<p>
					<label for="user_pass">
						Password
						<br>
						<input type="password" name="password" id="password" class="input" value size="20" tabindex="20">
					</label>
				</p>
				
				<p class="forgetmenot"> 
					<label for="rememberme">
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90">
						Remember Me
					</label>
				</p>
				
				<p class="submit">
					<input type="submit" name="login-submit" id="login-submit" class="button-primary" value="Log In" tabindex="100">
					<input type="hidden" name="redirect_to" value="index.php">
					<input type="hidden" name="testcookie" value="1"> 
				</p>
				
			</form>
			
		</div>
	
		
	</body>
</html>