<?php
session_start();
header("Cache-control: private"); //IE 6 Fix 
session_unregister('uname');
session_unregister('uid');
$_SESSION = array();
session_destroy(); 
?>
<p>You have logged out. Please <a href=login.php>click</a> here to relogin.</p>