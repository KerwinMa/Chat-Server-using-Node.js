<?php
/*
	Developed by: Micheal Jackson
*/

require_once('funcs_general.php');

class loggedIn {

	public $password  = NULL;
	public $name      = NULL; 
	
	
	// Update a password
	/*public function updatePassword($pass) {
		$secure_pass   = md5($pass);
		
		$this->password = $secure_pass;
		
		$query = "UPDATE admin SET password='".$secure_pass."'";
		
		$link = Connect();
		$result = mysql_query($query, $link) or die(mysql_error());
		
	}*/

}
?>