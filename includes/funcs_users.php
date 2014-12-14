<?php

	function isUserLoggedIn() {
		global $loggedInUser;
		
		if ( $loggedInUser == NULL ) { 
			return false;
		} else {
			$sql = "SELECT nickname, password FROM users WHERE nickname = '".$loggedInUser->nickname."' AND password = '".$loggedInUser->hash_pw."' AND active = 1 LIMIT 1";
		
			// Query the database to ensure they haven't been removed or possibly banned?
			if (mysql_num_rows($sql) > 0) {
				return true; 
			} else {
				// No result returned kill the user session, user banned or deleted
				$loggedInUser->userLogOut();
			
				return false;
			}
		}
	}
?>