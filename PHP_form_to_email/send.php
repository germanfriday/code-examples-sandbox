<?php
$to             ="you@where-ever.com";
$name		= ($_POST['name']);
$email		= ($_POST['email']);
$subject	= ($_POST['subject']);
$msg		= ($_POST['msg']);
$sub            = "form to mail";
$headers .= "From: $name <$email>\n";  
$headers .= "Content-Type: text/plain; charset=iso-8859-1\n";
$mes       = "Subject: ".$subject."
Message: ".$msg."
Name: ".$name."
Email: ".$email."
Date & Time: ".$d."";
if(empty($name) || empty($email) || empty($subject) || empty($msg)) {
echo "<h1>All fields are required</h1>";
} elseif(!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
echo "<h1>Sorry the email address you entered is invalid</p>";
} else {
mail($to, $sub, $mes, $headers);
echo "<h1><b><center>Thank you $name.<br>I will get back to you as soon as posiable</b></center></h1>"; 
}
?>