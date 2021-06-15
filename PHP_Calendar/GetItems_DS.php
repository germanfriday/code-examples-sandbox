<?php
class GetItems_DS {
	function GetItems_DS() {
	}
	
	function openConnection() {
   	require('settings.php');

		$curr_date=date("Y-m-d", mktime(0,0,0, $month, $date, $year));

		$db = mysql_connect($hostname, $uname, $login_password)
		   or die("Couldnot connect to server");

		return $db;
	}

	function closeConnection($db) {
		mysql_close($db);
	}
	
	function getItemsCount($uid, $dt, $db) {
		require('settings.php');
		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");
		   
		$sql = "select count(item_id) as count
				from $schedule_items_tb where uid='$uid' and item_date='$dt'";
				
		#echo $sql;
			
		$result = mysql_query($sql)
			or die("Query failed");

		return $result;
	}
		
	function getScheduleItems($uid, $dt, $db, $offset) {
		require('settings.php');
		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");
		   
		$sql = "select item_id, item, item_time, item_date, 
				item_ampm
				from $schedule_items_tb where uid='$uid' and item_date='$dt' order by item_time
				LIMIT $offset, 5";
				
		#echo $sql;
			
		$result = mysql_query($sql)
			or die("Query sfailed");

		return $result;
	}
	
	function getCurrItem($uid, $item_id) {
   	require('settings.php');
		$curr_date=date("Y-m-d", mktime(0,0,0, $month, $date, $year));

		$db = mysql_connect($hostname, $uname, $login_password)
		   or die("Couldnot connect to server");

		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");

		$sql = "select item_id, item, item_time, item_date, 
				item_ampm,
				description
				from $schedule_items_tb where uid='$uid' and item_id='$item_id'";

		#echo $sql;
		
		$result = mysql_query($sql)
			or die("Query failed");

		mysql_close($db);
		return $result;
	}
}
?>