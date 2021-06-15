<?php
session_start();
#header("Cache-control: private"); //IE 6 Fix 

$user_name = $_SESSION['uname'];
$fname     = $_SESSION['fname'];
$uid       = $_SESSION['uid'];

if(!isset($uid)) {
	#echo "Session not set";
	header("Location: Fail.php");
}
$date  = $_GET['date'];
$month = $_GET['month'];
$year  = $_GET['year'];
if(!$date) {
	$date = date("d");
}
if(!$month) {
	$month = date("m");
}
if(!$year) {
	$year = date("Y");
}

$this_date = date("Y-m-d", mktime(0, 0, 0, $month, $date, $year));

?>

<html>
<head>
	<title>ViewDate</title>
	<meta http-equiv="content-type"	content="text/html;	charset=iso-8859-1"/>
	<LINK href="images/styles.css" type=text/css rel=stylesheet></link>
	<script language="JavaScript" src="ajax.js"></script>
</head>
<body bgcolor="#FFFFFF">
<div id="content">
<form method=post action=ItemInsertController.php>
<table BORDER=1 CELLSPACING=0 CELLPADDING=0 WIDTH="100%" BGCOLOR="#ffffcc">
  <tr><td>
  <table border=0 bgcolor="#ffffcc" width=100%>
  <tr>
    <td width="90%"><font color="black" face="Arial"><b>
    <?php echo "$fname, ";?>
    Your Schedule for: <?php echo $this_date; ?></b></font></td>
    <td width="10%">	
    <a href="logout.php" target=_top><font color="black">Sign out</font></a>
    </td>
  </tr>
  </table>
  </td>
  </tr>
</table>
<br>

<?php
require('GetItems_DS.php');
$page_num = 1;
if(isset($_GET['page']) ) {
	$page_num = $_GET['page'];
}
$offset = ($page_num - 1) * 5;   #5 rows per page 

$getItems_DS = new GetItems_DS;
$conn = $getItems_DS->openConnection();

#get number of rows first
$result = $getItems_DS->getItemsCount($uid, $this_date, $conn);
$row     = mysql_fetch_array($result, MYSQL_ASSOC);
$total_rows = $row['count']; 
#echo "<br>$total_rows<br>";
$num_pages = ceil($total_rows / 5);   #5 rows per page

$result = $getItems_DS->getScheduleItems($uid, $this_date, $conn, $offset);
$num_items = mysql_num_rows($result);

echo "Add&nbsp;&nbsp;";
echo "<a href=\"javascript:sndReq('AddItem=$year:$month:$date')\" style=\"color: blue; background: white\">Item</a>";
echo "&nbsp;&nbsp;";
echo "<a href=\"javascript:sndReq('AddFriend')\" style=\"color: blue; background: white\">Friend</a>";


echo "<TABLE BORDER=1 cellpadding=5 cellspacing=0 width=100% bordercolor=#9999FF>";
echo "<tr></tr><td>";
echo "<table BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100% BGCOLOR=#000000>\n";
$curr_date = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
echo "<tr bgcolor=#C0C0C0>
      <td align=center width=2%></td>
      <td align=left><b>Time</b></td>
      <td align=left><b>Item</b></td>
      <td align=left><b>Days left</b></td>
	   </tr>\n";

while($result_row = mysql_fetch_row($result)) {
	$dt_arr = explode("-", $result_row[3]);
	$due_date = mktime(0, 0, 0, $dt_arr[1], $dt_arr[2], $dt_arr[0]);
	$days_left = ceil(date(($due_date - $curr_date) / (24 * 3600)));
	
	if($curr_date > $due_date) {
		$status = "<font color=red><b>Over</b></font>";
	}
	if($curr_date == $due_date) {
		$status = "<font color=red><b>Today</b></font>";
	}
	if($curr_date < $due_date) {
		$status = $days_left;
	}
	
	$del = "<input type=checkbox name=del_array[] value=$result_row[0]>";
	
	$arr = explode(":", $result_row[2]);
	#echo "$arr[0]:$arr[1] $result_row[4]<br>";
	$str_time = date("h:i a", mktime($arr[0], $arr[1], $arr[2], date("Y"), date("m"), date("d") ));
	if($arr[0] > 12) {
		$arr[0] = $arr[0] - 12;
	}
	$str_time = "$arr[0]:$arr[1] $result_row[4]";
	printf("<tr bgcolor=#FFFFCC>
			 <td>%s</td>
			 <td class=bodytext>
		    <a href=javascript:sndReq('view_item=$result_row[0]:$page_num')>
		    <font color=#000000>%s</font></a>
		    </td>
          <td align=left><font color=#000000>%s</font></td>
          <td align=left><font color=#000000>%s</font></td>
	   	 </tr>\n",$del,
			 $str_time, 
			 $result_row[1],
			 $status,
			 $result_row[0]
	      );
	$row_num++;
}
#close the connection
$getItems_DS->closeConnection($conn);
if($num_pages > 1) {
echo "<tr><td  bgcolor=#FFFFC colspan=5 align=center>";
for($curr_pg = 1; $curr_pg <= $num_pages; $curr_pg++) {
	if($curr_pg == $page_num) {
		echo "&nbsp;<font color=red>$curr_pg</font>&nbsp;";
	}
	else {
		echo "<a href=\"$self?page=$curr_pg\"><font color=#000000>$curr_pg</font></a>&nbsp;";
	}
}
echo "</td></tr>";
}
echo "</table></td></tr></table>\n";
#echo "<input type=submit name=delete style=\"color: black; background: gray\" value=Delete>";
echo "<input type=submit name=delete value=Delete class=\"TextField\">";
?>
<input type=hidden name=date value=<?php echo $this_date; ?> >
<input type=hidden name="FormName" value="ItemDelete">
</form>
</td></tr></table>
</div>
<div id="AddItemDiv">
</div>
</body>
</html>






