<?php
	
	function isLoggedIn() {
	
		$loggedIn = $_SESSION["adminUser"];
		
		if ( $loggedIn == NULL ) {
			return false;
		} else {
			return true;
		}
		
	}

?>