<?php
	session_start();
	#header("Cache-control: private"); //IE 6 Fix 

	$user_name = $_SESSION['uname'];
	$uid       = $_SESSION['uid'];
	if(!$uid) {
		header('Fail.php');
	}
	require('GetItems_DS.php');
	$getItems_DS = new GetItems_DS;
	$result = $getItems_DS->getCurrItem($uid, $_GET['item_id']);
   $page_num = $_GET['page'];
   $item_id  = $_GET['item_id'];
   
?>
<html>
<head>
	<title>ViewItem</title>
	<meta http-equiv="content-type"	content="text/html;	charset=iso-8859-1"/>
	<LINK href="images/styles.css" type=text/css rel=stylesheet></link>
</head>
<body>
<form method=post action=redirect.php>

<?php
	$numrows    = mysql_num_rows($result);
	$result_row = mysql_fetch_row($result);
	$curr_date  = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	
	$ex_time = explode(":", $result_row[2]);
	if($ex_time[0] > 12) {
		$ex_time[0] = ($ex_time[0] - 12);
	}

	$dt_arr = explode("-", $result_row[3]);
	$due_date = mktime(0, 0, 0, $dt_arr[1], $dt_arr[2], $dt_arr[0]);

	if($curr_date > $due_date) {	
		$status = "<font color=red><b>Over</b></font>";
	}
	if($curr_date == $due_date) {
      $status = "<font color=red><b>Today</b></font>";
	}

	else {
		$days_left = ceil(date(($due_date - $curr_date) / (24 * 3600)));
		if($days_left > 0) {
			$status = $days_left;
		}
		if($days_left == 0) {
			$status = "Today";
		}
		if($days_left < 0) {
			$status = "Over";
		}
	}
	echo "<br>";
	echo "<TABLE BORDER=1 cellpadding=5 cellspacing=0 bordercolor=#9999FF width=60%><tr><td>";
	echo "<table BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100% BGCOLOR=#FFFFE4 width=100%>\n";
	echo "<tr>";
  	echo "<td align=left bgcolor=#c0c0c0><b>Details</b></td>";
  	echo "<td align=right bgcolor=#c0c0c0>
  			<a href=\"javascript:sndReq('edit_item=$item_id:$page_num')\"><img border=\"0\" src=\"images/edit.jpg\"></img></a>";
  	echo "&nbsp;&nbsp;&nbsp;";
  	echo "<a href=\"javascript:sndReq('dispose')\"><img border=0 src=\"images/close.jpg\"></img></a>";
  	echo "&nbsp;&nbsp;";
  	echo "</tr>";

	echo "<tr>
	      <td width=30%><b>Item</b></td>
	      <td>$result_row[1]</td>
	      </tr>
	      <tr>
	      <td width=30%><b>Due Date</b></td>
	      <td>$result_row[3]</td>
	      </tr>
	      <tr>
	      <td width=30%><b>Due Time</b></td>
	      <td>$ex_time[0]:$ex_time[1] $result_row[4]</td>	
	      </tr>
	      <tr>
	      <td width=30%><b>Days left</b></td>
	      <td>$status</td>
	      </tr>
	      <tr>	
	      <td width=30%><b>Notes</b></td>
	      <td width=30%>$result_row[5]</td>
	      </tr>";
	echo "</td></tr></table></table></center>";
	?>
</form>
</body>
</html>





