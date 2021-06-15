<?php
class UserAuth {
	var $user;
	var $pass;
	
	function UserAuth() {
	}
	
	function isUserValid($user, $pass) {
		require('settings.php');
		$db  = mysql_connect($hostname, $uname, $login_password)
			or die("Couldnot connect to db");
		
		mysql_select_db($database_name, $db)
			or die("Couldnot select db");

		$sql = "select uid, first_name from $schedule_users_tb where user_name='$user' and password='$pass'";
		#echo $sql;
		
		$result = mysql_query($sql);
		$rows   = mysql_num_rows($result);

		if($rows == 1) {
			$result_row = mysql_fetch_row($result);
			return $result_row;
		}
		else {
			return "0";
		}
	}
}
?>