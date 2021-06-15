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
	<title>AddFriend</title>
	<meta http-equiv="content-type"	content="text/html;	charset=iso-8859-1"/>
	<script language="JavaScript" src="ajax.js"></script>
	<style type="text/css"><!--
		.TextField {
		background-color: c0c0c0;
		border-width: 1;
		}
	-></style>
</head>
<body>
<br>
<form method=post action=ItemInsertController.php>
<TABLE BORDER="1" cellpadding="5" cellspacing="0" width=60% bordercolor="#9999FF">
  <tr>
  <td>
  <TABLE border=0 bgcolor="#ffffcc" WIDTH=100%>
  <tr>
   <td colspan=2>
   <table  bgcolor=#c0c0c0 width=100%>
   <tr>
  	<td width=40% align=left bgcolor=#c0c0c0><b>Add new Item</b></td>
  	<td width=60% align=right bgcolor=#c0c0c0>
  		<a href="javascript:sndReq('dispose')"><img border="0" src="images/close.gif"></img></a>
  		<!-- <font size=-1><a href="javascript:sndReq('dispose')" style="color: black; background: #c0c0c0">Close</a></font> -->
  	</td>
  	</td>
  	</tr>
  	</table>
  	</tr>
    <tr>
    <td width=40%>Type</td>
    <td width=60%>
	 <select class="TextField" name=item>
      <option value=Appointment>Appointment
      <option value=Birthday>Birthday
      <option value=Class>Class
      <option value=Meeting>Meeting
		<option value=Reminder>Reminder
		<option value=Other>Other
    </select>
    </td>
  </tr>
  <tr>
    <td width=40%>Time</td>
    <td width=60%>

      <?php
	      echo "<select class=\"TextField\" name=hours>";
          for($hr = 1; $hr <= 12; $hr++) {
				echo "<option value=$hr>$hr";
		    }
	      echo "</select>
   		   <select class=\"TextField\" name=minutes>
				<option value=00>00
				<option value=15>15
				<option value=30>30
				<option value=45>45
   	   </select>";
   	?>
      <select class="TextField" class="TextField" name=sel_time>
          <option value=am>am
      <option value=pm>pm
      </select>
    </td>
  </tr>
  <tr>
  <td>Recurring</td>
  <td>
   <INPUT class="TextField" TYPE=CHECKBOX NAME="recurring">
  	<select class="TextField" name=recurring_units>
  		<option value=days>Daily</option>
  		<option value=weeks>Weekly</option>
  		<option value=months>Monthly</option>
  	</select>
  	<input class="TextField" type=text size=2 name=recurring_times>&nbsp;times&nbsp;
  </td>
  </tr>
  <tr>
    <td width=40%>Notes</td>
    <td width=60%>
        <textarea class="TextField" name=desc rows=3 cols=40></textarea>
    </td>
    </tr>
    <tr>
    <td></td>
    <td>
    <input type=hidden name=date value=<?php echo $this_date; ?> >
    <input type=hidden name=FormName value=InsertItem>
    <input class="TextField" type=submit name=submit value=Submit>
    </td>
    </tr>
</table>
</form>
</body>
</html>