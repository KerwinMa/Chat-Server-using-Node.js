<?php

	function destorySession($name) {
	
		if ( isset($_SESSION[$name]) ) {
			$_SESSION[$name] = NULL;
			
			unset($_SESSION[$name]);
		}
	}

?>