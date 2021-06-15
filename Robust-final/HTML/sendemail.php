<?php
$mailTo = 'your@mail.com';
$name = htmlspecialchars($_POST['Name']);
$mailFrom = htmlspecialchars($_POST['Email']);
$subject = 'Message from your site';
$message_text = htmlspecialchars($_POST['Message']);

$message =  'From: '.$name.'; Email: '.$mailFrom.' ; Message: '.$message_text;

mail($mailTo, $subject, $message);
?>