<!-- Replace the default pgbar.php file if you hate that annoying clicking sound that
     exploder gives out all the time.
	 Contributed by Orest Kinasevych
-->

<html>
<head>
<title>Progress Bar</title>
<bgsound src='none.wav' loop='infinite'>
</head>

<?
	$iTotal = urlencode($_REQUEST['iTotal']);
	$iRead = urlencode($_REQUEST['iRead']);
	$iStatus = urlencode($_REQUEST['iStatus']);
	$sessionId = urlencode($_REQUEST['sessionid']);
	
	$link = "/cgi-bin/progress.cgi?iTotal=$iTotal&iRead=$iRead&iStatus=$iStatus&sessionid=$sessionId";
?>	
	 
<frameset rows="*,120" scroll="none">
	<frame src="<? echo $link; ?>">
	<frame src="/php/sponser.php">
</frameset>

<noframes>
 This is the progress bar window for the PHP mega upload. <a href="/php/progress.php">Follow this
 link</a> for the main page.
</noframes>
</html>
