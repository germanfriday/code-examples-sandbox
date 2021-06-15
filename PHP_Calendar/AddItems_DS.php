<?php
class AddItems_DS {
	function AddItems_DS() {
	}
	
	function addItem($item) {
    	require('settings.php');
   	
		$db = mysql_connect($hostname, $uname, $login_password)
		   or die("Couldnot connect to server");

		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");

 		$sql = "insert into $schedule_items_tb values (
				  null,
				  '$item->uid',
				  '$item->item',
				  '$item->itemTime',
				  '$item->itemAmPm',
 				  '$item->itemDate',
				  '$item->itemDesc'
				  )";
		echo $sql;
		
		$result = mysql_query($sql)
			or die("Query failed");

		mysql_close($db);
		return $result;
	}
	
	function updateItem($item) {
    	require('settings.php');
    	
		$db = mysql_connect($hostname, $uname, $login_password)
		   or die("Couldnot connect to server");

		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");

 		$sql = "update $schedule_items_tb set 
				  item='$item->item',
				  item_time='$item->itemTime',
				  item_ampm='$item->itemAmPm',
				  item_date='$item->itemDate',
				  description='$item->itemDesc'
				  where item_id=$item->itemId;";

		#echo $sql;
		
		$result = mysql_query($sql)
			or die("Query failed");

		mysql_close($db);
		return $result;
	}
}

class DeleteItems_DS {
	function DeleteItems_DS() {
	}
	
	function deleteItem($item_list) {
    	require('settings.php');
    	
		$db = mysql_connect($hostname, $uname, $login_password)
		   or die("Couldnot connect to server");

		mysql_select_db($database_name, $db)
		   or die("Couldnot select databasee");

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