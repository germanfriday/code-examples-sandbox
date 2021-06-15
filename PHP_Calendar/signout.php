session_start();
header("Cache-control: private"); //IE 6 Fix 

$_SESSION['uid'] = FALSE;

if(!$user_name) {
	header('login.php');
}