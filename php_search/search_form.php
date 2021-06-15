<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Untitled Document</title>
</head>

<body>

<? 
//This is only displayed if they have submitted the form 
if ($searching =="yes") 
{ 
echo "<h2>Results</h2><p>"; 

//If they did not enter a search term we give them an error 
if ($find == "") 
{ 
echo "<p>You forgot to enter a search term"; 
exit; 
} 

// Otherwise we connect to our Database 
mysql_connect("mysql.yourhost.com", "user_name", "password") or die(mysql_error()); 
mysql_select_db("database_name") or die(mysql_error()); 

// We preform a bit of filtering 
$find = strtoupper($find); 
$find = strip_tags($find); 
$find = trim ($find); 

//Now we search for our search term, in the field the user specified 
$data = mysql_query("SELECT * FROM users WHERE upper($field) LIKE'%$find%'"); 

//And we display the results 
while($result = mysql_fetch_array( $data )) 
{ 
echo $result['fname']; 
echo " "; 
echo $result['lname']; 
echo "<br>"; 
echo $result['info']; 
echo "<br>"; 
echo "<br>"; 
} 

//This counts the number or results - and if there wasn't any it gives them a little message explaining that 
$anymatches=mysql_num_rows($data); 
if ($anymatches == 0) 
{ 
echo "Sorry, but we can not find an entry to match your query<br><br>"; 
} 

//And we remind them what they searched for 
echo "<b>Searched For:</b> " .$find; 
} 
?>


<h2>Search</h2> 
<form name="search" method="post" action="<?=$PHP_SELF?>">
Seach for: <input type="text" name="find" /> in 
<Select NAME="field">
<Option VALUE="fname">First Name</option>
<Option VALUE="lname">Last Name</option>
<Option VALUE="info">Profile</option>
</Select>
<input type="hidden" name="searching" value="yes" />
<input type="submit" name="search" value="Search" />
</form>

</body>
</html>
