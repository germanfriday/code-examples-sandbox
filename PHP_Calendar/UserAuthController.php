<?php
session_start();
#header("Cache-control: private");

require('UserAuth.php');

$user = $_POST['uname'];
$pass = $_POST['password'];

$user = mysql_escape_string($user);
$pass = mysql_escape_string($pass);

$userauth = new UserAuth;

$val = $userauth->isUserValid($user, $pass);
if($val == "0") {
	header("Location: login.php");
}
else {
	$_SESSION['uid'] = $val[0];
	$_SESSION['fname'] = $val[1];
	$_SESSION['uname'] = $user;
	header("Location: SchedulerMain.php");
}
?>
