<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Contact Form Response</title>
</head>

<body>


<?php 
// Written by Stefan Mischook
// September 2006 - www.killerphp.com | www.killersites.com
// You may use this code as you like.


$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Validation
if(3 < strlen($name) && 3 < strlen($email) && 3 < strlen($message))
{

$email_message = <<< EMAIL
Message from websites contact form.

Name: $name
Email:$email

The message:
$message

EMAIL;
 
$headers = "cc:you@your_alternate_address.com\r\n"; 


	if(mail('stefan@killersites.com','Contact form email', $email_message, $headers))
	{
		echo "Your email has been delivered, we will contact you shortly.";	
	}
	else 
	{
		echo "We had a problem sending the email.";
	}
	
 
}
else
{
  echo "You did not fill in the form properly. Please use the browser's 'back' button and update the form.";	
}




?>




</body>
</html>
