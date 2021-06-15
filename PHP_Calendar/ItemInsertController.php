<?php
session_start();
#header("Cache-control: private"); //IE 6 Fix 

$uid = $_SESSION['uid'];
$user_name = $_SESSION['uname'];

if(!$user_name || !$user_name) {
	header('Fail.php');
}

require('AddItems_DS.php');
require('ItemBean.php');
require('Friend_DS.php');

if($_POST['FormName'] == 'InsertFriend') { #update item 
	echo "Done-1";
	$ds = new Friend_DS();	
	echo "Done0";
	$result = $ds->addFriend($_POST['uid'], '2', $_POST['greeting']);
	
	echo "Done";
	$dt_arr = explode("-", $_POST['date']);
 	if($result == true) {
		header("Location: ViewDate.php?date=$dt&month=$mo&year=$yr");
	}
	else {
		echo "Friend insert failed";
	}	
}

if($_POST['FormName'] == 'EditItem') { #update item 
	$ds = new AddItems_DS;
	$bean = new ItemBean;
	$bean->setItemId($_POST['item_id']);
	$bean->setItem($_POST['item']);
	
	$ampm = $_POST['sel_time'];
	if($ampm == "pm") {
		$hours = ($_POST['hours'] + 12);
	}
	else {
		$hours = $_POST['hours'];
	}
	
	$bean->setItemTime(implode(":", array($hours, $_POST['minutes'], 1)));
	
	$bean->setItemAmPm($_POST['sel_time']);

	#check if this date is after current date.
	$this_date = implode("-", array($_POST['sel_year'], $_POST['sel_month'], $_POST['sel_date']));
	$yr = $_POST['sel_year'];
	$mo = $_POST['sel_month'];
	$dt = $_POST['sel_date'];
	
	$bean->setItemDate($this_date);
	$tdesc = mysql_escape_string($_POST['desc']);
	$bean->setItemDesc(strip_tags($tdesc));
	
	$result = $ds->updateItem($bean);
	
	$page_num = $_POST['page'];
 	if($result == true) {
	 	header("Location: ViewDate.php?date=$dt_arr[2]&month=$dt_arr[1]&year=$dt_arr[0]");
	}
	else {
		echo "Item edit failed";
	}
}

if($_POST['FormName'] == 'InsertItem') { #add new item 
	$recurring = $_POST['recurring'];
	if(isset($recurring) ) {
		$recurring_times = $_POST['recurring_times'];
		$recurring_units = $_POST['recurring_units'];
		
		if($recurring_units == "days") {
			$tdt_arr = explode("-", $_POST['date']);
			for($i = 1; $i < $recurring_times; $i++) {
				$tdt_arr[2] += 1;
				$startdate = date("Y-m-d", mktime(0, 0, 0, $tdt_arr[1], $tdt_arr[2], $tdt_arr[0]));
				insertItem($uid, $startdate);
			}
		}
		if($recurring_units == "weeks") {
			$tdt_arr = explode("-", $_POST['date']);
			for($i = 1; $i < $recurring_times; $i++) {
				$tdt_arr[2] += 7;
				$startdate = date("Y-m-d", mktime(0, 0, 0, $tdt_arr[1], $tdt_arr[2], $tdt_arr[0]));
				insertItem($uid, $startdate);
			}
		}
		if($recurring_units == "months") {
			$tdt_arr = explode("-", $_POST['date']);
			for($i = 1; $i < $recurring_times; $i++) {
				$tdt_arr[1] += 1;
				$startdate = date("Y-m-d", mktime(0, 0, 0, $tdt_arr[1], $tdt_arr[2], $tdt_arr[0]));
				insertItem($uid, $startdate);
			}
		}
	}
	insertItem($uid, $_POST['date']);
}	

function insertItem($uid, $tdate) {
	
	$ds = new AddItems_DS;
	$bean = new ItemBean;

	$bean->setUid($uid);
	$bean->setItem($_POST['item']);

	$ampm = $_POST['sel_time'];
	if($ampm == "pm") {
		$hours = ($_POST['hours'] + 12);
	}
	else {
		$hours = $_POST['hours'];
	}
	$bean->setItemTime(implode(":", array($hours, $_POST['minutes'], 1)));
	$bean->setItemAmPm($_POST['sel_time']);

	#check if this date is after current date.
	//$bean->setItemDate($_POST['date']);
	$bean->setItemDate($tdate);
	
	$tdesc = mysql_escape_string($_POST['desc']);
	$bean->setItemDesc(strip_tags($tdesc));
	$result = $ds->addItem($bean);
	$dt_arr = explode("-", $_POST['date']);
	
	if($result == true) {
		header("Location: ViewDate.php?date=$dt_arr[2]&month=$dt_arr[1]&year=$dt_arr[0]");
	}
	else {
		echo "Item insert failed";
	}
}

if($_POST['FormName'] == 'ItemDelete') { #add new item 
	$ds = new DeleteItems_DS;
	$del_array = $_POST['del_array'];
	$result = $ds->deleteItem($del_array);
	$dt_arr = explode("-", $_POST['date']);
	header("Location: ViewDate.php?date=$dt_arr[2]&month=$dt_arr[1]&year=$dt_arr[0]");
}
?>	