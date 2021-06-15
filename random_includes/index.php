<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Untitled Document</title>
</head>
<body>

<?
/* Directory Structure
includes/include1.inc.php
includes/include2.inc.php
includes/include3.inc.php
includes/include4.inc.php
includes/include5.inc.php
*/
srand((double)microtime()*1000000); 
$num = rand(1,5);

include ('includes/include'.$num.'.inc.php');

?>

</body>
</html>
