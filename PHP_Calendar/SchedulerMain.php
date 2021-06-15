<?php
session_start();
header("Cache-control: private"); //IE 6 Fix 

$user_name = $_SESSION['uname'];
$uid       = $_SESSION['uid'];
if(!isset($uid)) {
	#echo "Session not set";
	header("Location: Fail.php");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
	"http://www.w3.org/TR/html4/frameset.dtd"><html>
<head>
<title>SchedulerMain</title>
</head>
<frameset cols="25%,75%" frameborder="0" border="0" framespacing="0">
<frame noresize="noresize" name="Calender" src="calender.php">
<frame noresize="noresize" name="ViewDate" src="ViewDate.php">
<noframes>

</noframes>
</frameset>
</html>




