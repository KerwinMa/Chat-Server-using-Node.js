<?php

class loggedInUser {

	public $nickname  = "";
	public $email     = "";
	public $hash_pw   = "";
	
	public $sex  = 0; 
	public $age  = 0;
	public $position  = "";
	
	
	// Simple function to update the last sign in of a user
	public function updateLastSignIn() {
		$link = Connect();
		$sql = "UPDATE users SET last_signin = '".time()."'
				WHERE nickname = '".$this->nickname."'";
		
		mysql_query($sql, $link) or die(mysql_error());
	}
	
	// Update a users password
	public function updatePassword($password) {
		$link = Connect();
		$secure_pass = generateHash($password);
		$this->hash_pw = $secure_pass;
		
		$sql = "UPDATE users SET password = '$secure_pass' WHERE nickname = '".$this->nickname."'";
		
		mysql_query($sql, $link) or die(mysql_error());
	}
	
	// Update a users email
	public function updateEmail($email) {
		$link = connect();
		$this->email = $email;
		
		$sql = "UPDATE users SET email = '".$email."' WHERE nickname = '".$this->nickname."'";
		
		mysql_query($sql, $link) or die(mysql_error());
	}
	
	// Logout
	function userLogOut() {
		destorySession("loggedInUser");
	}

}
?>