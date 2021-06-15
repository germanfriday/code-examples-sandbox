<?
$hostname="localhost";
$user="imapuser";
$password="123";

?>
<html>
<body  bgcolor="FFFFCC">

<table border="1" cellpadding="5" width="80%" align="center">
<tr><td colspan="2" bgcolor="#0066cc"><font color="#FFFFCC" size="+1" align="center">Files Uploaded</font></td></tr>
<tr  bgcolor="#ffff00"><td style="font-size: 110%;"><nobr>File Name</nobr></td>
	<td style="font-size: 110%"  align="right"><nobr>File size</nobr></td></tr>
<?

/* 
 * improved user interface contributed by Rad Inks
 * http://www.radinks.com/
 */

 
require("ftp.php");

$ftp = new FTP($hostname);
$sock = $ftp->connect();

if($sock && $ftp->is_ok()==1 && $ftp->login($user,$password))
{
	 
	$file = $_FILES['file'];
	$k = count($file['name']);

		
		
	for($i=0 ; $i < $k ; $i++)
	{
		if($i %2)
		{
			echo '<tr bgcolor="#FFFF99"> ';
		}
		else
		{	
			echo '<tr>';
		}
		$ftp->stor($file['tmp_name'][$i],$file['name'][$i]);
		
		echo '<td align="left">' . $file['name'][$i] ."</td>\n";
		echo '<td align="right">' . $file['size'][$i] ."</td></tr>\n";

		
		// ob_flush();
	}
}
else
{
	echo '<tr><td>Files could not be moved to the FTP server</td></tr>';
}

?>
</table>
<p>
<table border=0 width="600" align="center">
<tr><td>
<p>
	If you like mega upload please provide a link to us so that others will get to know about
	it.
	<a href="http://www.raditha.com/megaupload/">http://www.raditha.com/megaupload/</a>
</p>
<p>This is a request and not a condition for use. You can use megaupload regardless of weather or
not you provide a return link.</p>
</td></tr>
</table>


	
</body>
</html>




