<?php
class Friend_DS {
	function Friend_DS() {
		require('settings.php');
		$db = mysql_connect($hostname, $uname, $login_password)
		   or die("Couldnot connect to server");

		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");
	}
	
	function addFriend($uid, $fid, $greeting) {
		require('settings.php');
		$today = getdate(mktime(0, 0, 0, date("m"), 1, date("Y")));
 		$sql = "insert into $schedule_friends_tb values (
				  null,
				  '$uid',
				  '$fid',
				  '$today[year]-$today[mon]-$today[wday]',
				  '$greeting'
				  )";
		
		#echo $sql;
		
		$result = mysql_query($sql)
			or die("Query failed");

		mysql_close($db);
		return $result;
	}	
	
	function deleteFriend($item_list) {
		for($i = 0; $i < count($item_list); $i++) {
 			$sql = "delete from $schedule_items_tb where item_id=$item_list[$i];";
			#echo $sql;			
			$result = mysql_query($sql)
				or die("Query failed");
		}
		mysql_close($db);
		return $result;
	}
}
?>