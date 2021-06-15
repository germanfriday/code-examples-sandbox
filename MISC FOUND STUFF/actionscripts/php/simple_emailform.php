<?

/*    *******************************************************************************************
      ***                                                                                     ***
      ***  SIMPLE EMAILFORM USING PHP - together with Flash 5                                 ***
      ***                                                                                     ***
      ***  With this script you can make a simple email form, where the script sends          ***
      ***  the submitted message to your email address.                                       ***
      ***                                                                                     ***
      ***  This is a flash form - it also works in HTML.                                      *** 
      ***                                                                                     ***
      ***  Files: formular.fla,formular.swf,formular.html and simple_emailform.php            ***
      ***                                                                                     ***
      ***                                                                                     ***
      ***  The script is placed in a subfolder called "php"                                   ***
      ***                                                                                     ***
      ***  It's very simple, the script doesn't need a database, but the webserver has to     ***
      ***  support PHP.                                                                       ***
      ***  Read the comments in the script.                                                   ***
      ***                                                                                     ***
      ***  I cannot answer any questions on PHP, but there«s a lot of friendly people on      ***
      ***  the net!                                                                           ***
      ***                                                                                     ***
      ***  Commented and manipulated for use in Flash by Lars Bregendahl Bro - www.westend.dk ***
      ***                                                                                     ***
      *******************************************************************************************
*/

/*    ****************************************************************************
      *** $msg relates to the variables that is placed in the body of the mail ***
      ****************************************************************************
*/

$msg = "E-mail sent from my site\n\n";

/*    ********************************************************************************************
      ***  The string From Name is written in to the body followd by the variable $sender_name ***
      ******************************************************************************************** 
*/

$msg .= "From Name:    $sender_name\n";
$msg .= "From E-Mail:  $sender_email\n";
$msg .= "Telephone:    $telephone\n\n";
$msg .= "Message:      $message\n\n";

/*    *************************************************************  
      ***  \n gives a linebreak - \n\n gives a double linebreak ***  
      *************************************************************
*/

/*    ****************************************************
      ***  Remember to put in the correct email-adress ***
      ****************************************************
*/

$to = "name@domain.com";
$subject = "TEST of the simple EmailForm";

/*    ********************************************************
      ***  Writes "Sent from my website" in the from-field ***
      ********************************************************
*/

$mailheaders = "Sent from my website \n";

/*    **********************************************************
      ***  Writes the senders email in the "Reply-To field"  *** 
      **********************************************************
*/

$mailheaders .= "Reply-To: $sender_email\n\n";

/*    ********************************************
      ***  Mail Function - executes the script ***
      ********************************************
*/

mail($to, $subject, $msg, $mailheaders);


?>

