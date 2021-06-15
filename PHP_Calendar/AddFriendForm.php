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
	--></style>
</head>
<body>
<br>
<form method=post action=ItemInsertController.php>
<TABLE BORDER="1" cellpadding="5" cellspacing="0" width=60% bordercolor="#9999FF">
  <tr>
  <td>
  <TABLE border=0 bgcolor="#ffffcc" WIDTH=100%>
  <tr>
   <td colspan=4>
   <table  bgcolor=#c0c0c0 width=100%>
   <tr>
  	<td width=40% align=left bgcolor=#c0c0c0><b>Add Contact</b></td>
  	<td width=60% align=right bgcolor=#c0c0c0>
  		<a href="javascript:sndReq('dispose')"><img border="0" src="images/close.jpg"></img></a>
  		<!-- <font size=-1><a href="javascript:sndReq('dispose')" style="color: black; background: #c0c0c0">Close</a></font> -->
  	</td>
  	</td>
  	</tr>
  	</table>
  	</tr>
  	<tr></tr>
   <tr>
	 <td><b>First name</b></td><input class="TextField" type="text" size=25 name="text"></td>
	</tr>
	<tr>
	 <td><b>Last name</b></td><td><input class="TextField" type=text size=25></td>
   <tr>
   <tr>
	 <td><b>Home phone</b></td><input class="TextField" type=text size=25></td>
	</tr>
	<tr>
	 <td><b>Work phone</b></td><td><input class="TextField" type=text size=25></td>
   <tr>

   <tr>
    <td><b>Email</b></td>
    <td><input class="TextField" type=text name=greeting size=25></textarea></td>
   </tr>
   <tr>
    <td><b>Address</b></td>
    <td><textarea class="TextField" name=greeting rows=3 cols=40></textarea></td>
   </tr>

    <td></td>
    <td>
    <input type=hidden name=date value=<?php echo $this_date; ?> >
    <input type=hidden name=FormName value=InsertFriend>
    <input class="TextField" type=submit name=submit value=Submit>
    </td>
   </tr>
</table>
</form>
</body>
</html>