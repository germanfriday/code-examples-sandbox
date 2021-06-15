<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Mjrz.net - Welcome!</title>
	<meta http-equiv="content-type"	content="text/html;	charset=iso-8859-1"	/>
	<LINK href="../images/styles.css" type=text/css rel=stylesheet></link>
	
	<script type="text/javascript">
	function changeColor(color)
	{
		document.getElementById('x').style.background=color
	}
	</script>
</head>

<body bgcolor=#ffffff background='../images/bkg_lines.gif'>
<ul id="tabmenu">
	<li><a class="active" href="../index.php">Projects</a></li>
	<li><a href="../personal/aboutme.php">About me</a></li>
	<li><a href="../contact/feedback.php">Contact</a></li>

</ul>

<div id="content">
<TABLE cellSpacing=0 cellPadding=0 width=100% border=0 bgColor=#ffffcc>
  <TBODY>
  <TR><td><p>Web based Calendar and scheduler</p></td></tr>
  <TR><td>Description and Features:</td></tr>
  <TR>
    <TD class=bodytext vAlign=top> 
      <table width="100%" border="0">
        <tr>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><font color=#000000>Written in PHP</font> 
          </td>
        </tr>
        <tr>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><font color=#000000>Support for MySQL and PostgreSQL</font> 
          </td>
        </tr>
        <tr>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><font color=#000000>Single and reccuring events support</font> 
          </td>
        </tr>
        <tr>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%">Calendar sharing with groups</font>
          <font size=-1>&nbsp;(Coming soon)</font>
          </td>
        </tr>
        <TR>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%">
          		<font color=#000000>For a live demo, use the login form on this page</font>
          		<font size=-1>&nbsp;(user guest, pass guest)</font>
          </td>
        </tr>
        <TR>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><a href="../downloads/downloads.php?ditem=scheduler"><font color=#CC0000>Download</font></a>
          </td>
        </tr>
        <TR>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><a href="../contact/feedback.php"><font color=#CC0000>Send me your comments</font></a>
          </td>
        </tr>
        <TR>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><a href="http://www.hotscripts.com/Detailed/11065.html">
          <font color=#CC0000>Please rate this app at Hotscripts.com</font></a>
          </td>
        </tr>
        <tr>
          <td width="5%"><img src="../images/arrow.gif" width="20" height="20"></td>
          <td width="95%"><a href="http://wholinkstome.com/">Who Links Here</a></td>
        </tr>

       <tr>
       <td></td>
	      <td>
	      <p>External links</p>
			<li><a href="http://www.php.net"><font color=#000000>Php website</font></a></li>
			<li><a href="http://www.postgresql.org"><font color=#000000>PostgreSQL site</font></a></li>
			<li><a href="http://www.mysql.com"><font color=#000000>MySQL site</font></a></li>
			</td>
       </tr>
      </table>
      </P>
    </td>
    <!-- right column content -->
    <TD vAlign=top align="right"> 
	  <form name="scheduler_login" method="post" action="UserAuthController.php">
	  <table>
	  <tr>
		  <td>User name</td>
	  	  <td><input type=text size=15 name=uname></td>
	  	  <td></td>
	  </tr>
	  <tr>
		  <td>Password</td>
	  	  <td><input type=password size=15 name=password></td>
	  	  <td></td>
	  </tr>
	  <tr><td></p></td></tr>
	  <tr>
		  <td></td>
		  <td align="center">
		  		<input type=submit name=submit style="color: black; background: gray" value="Submit"> 
 		  </td>
		  <td></td>
	  </tr>
	  </table>
     </form>
    <BR>
    </TD>
    </TR>
    </TBODY>
    </TABLE>
  </div>
</body>
</html>
