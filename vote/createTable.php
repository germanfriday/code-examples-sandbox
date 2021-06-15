<?
$user="Your user";
$password="Your password";
$database="Your database";
mysql_connect(localhost,$user,$password);
@mysql_select_db($database) or die("Unable to select database");
$query="CREATE TABLE votesystem (vote1 int(4) NOT NULL,vote2 int(4) NOT NULL,vote3 int(4) NOT NULL,vote4 int(4) NOT NULL)";
mysql_query($query);
mysql_close();
?>