<?php
session_start();

$user_name = $_SESSION['uname'];
$uid       = $_SESSION['uid'];
if(!isset($uid)) {
	#echo "Session not set";
	header("Location: Fail.php");
}

?>
<html>
<head>
	<title>Calender</title>
	<meta http-equiv="content-type"	content="text/html;	charset=iso-8859-1"/>
	<LINK href="images/styles.css" type=text/css rel=stylesheet></link>
</head>
<body bgcolor="#FFFFFF">
<div id="content">
<font face="Arial, Helvetica">
<form method=post action=calender.php>
<center>

<?php
if($_POST['sel_year'] && $_POST['sel_month']) {
	$tmpd = getdate(mktime(0, 0, 0, $_POST['sel_month'], 1, $_POST['sel_year']));
	$month = $tmpd["mon"]; 
	$fwday= $tmpd["wday"];
	$year = $tmpd["year"];
	$month_textual = $tmpd["month"];
	echo "<font face=Arial, Helvetica color = red><b>";
}
if($_GET['sel_year'] && $_GET['sel_month']) {
	$tmpd = getdate(mktime(0, 0, 0, $_GET['sel_month'], 1, $_GET['sel_year']));
	$month = $tmpd["mon"]; 
	$fwday= $tmpd["wday"];
	$year = $tmpd["year"];
	$month_textual = $tmpd["month"];
	echo "<font face=Arial, Helvetica color = red><b>";
}
if(!isset($tmpd) ) {
	$tmpd = getdate(mktime(0, 0, 0, date("m"), 1, date("Y")));
	$month = $tmpd["mon"]; 
	$fwday= $tmpd["wday"];
	$year = $tmpd["year"];
	$month_textual = $tmpd["month"];
	echo "<font face=Arial, Helvetica color = red><b>";
}
if($month == 2) {
	if(($year%4) == 0) {
		$no_days = 29;
	}
	else {
		$no_days = 28;
	}
}
elseif(($month == 1) || ($month == 3) || ($month == 5) ||  
($month == 7) ||  ($month == 8) ||  ($month == 10) ||  ($month == 12)) {
	$no_days = 31;
}
else {
	$no_days = 30;
}

$next_mon = getdate(mktime(0, 0, 0, ($month + 1), 1, $tmpd["year"]));
$prev_mon = getdate(mktime(0, 0, 0, ($month - 1), 1, $tmpd["year"]));
$next_mon_txt = $next_mon["mon"];
$next_yr_txt  = $next_mon["year"];

echo "<TABLE BORDER=1 cellpadding=5 cellspacing=0 width=100% bordercolor=#9999FF>";
echo "<tr></tr><td>";
echo "<table BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100% BGCOLOR=#FFFFCC>
		<tr>
			<td align=right>
				<a href=calender.php?sel_month=$prev_mon[mon]&sel_year=$prev_mon[year] style=\"color: black; background: FFFFCC\">&lt;</a>
			</td>
			<td colspan=5 align=center><b>$month_textual, $year</b></td>
			<td align=left>
				<a href=calender.php?sel_month=$next_mon_txt&sel_year=$next_yr_txt style=\"color: black; background: FFFFCC\">&gt;</a>
			</td>
		</tr><tr bgcolor=#C0C0C0>";
echo"<td width=14%>Sun</td><td width=14%>Mon</td><td width=14%>Tue</td><td width=14%>Wed</td>
<td width=14%>Thu</td><td width=14%>Fri</td><td width=14%>Sat</td>";
echo"</tr><tr>";

if($fwday == 0) {
	$index = 1;
}
if($fwday == 1) {
	$index = 2;
}
if($fwday == 2) {
	$index = 3;
}
if($fwday == 3) {
	$index = 4;
}
if($fwday == 4) {
	$index = 5;
}
if($fwday == 5) {
	$index = 6;
}
if($fwday == 6) {
	$index = 7;
}

for($a = 1; $a <= $fwday; $a++) {
        echo"<td></td>";
}

for($i = 1; $i <= (7 - $fwday) ; $i++) {

if( $i == date("d")) {
$color = "red";
}
else {
$color = "#000000";
}

echo"<td align=center width=30 height=30>
<a href=ViewDate.php?date=$i&month=$month&year=$year target=ViewDate>
<font color=$color size=-1><b>$i</b></font><a></td>";
$count++;
}

echo"</tr>";

echo"<tr>";

for($j = $i; $j <= ($i + 6); $j++) {

if( $j == date("d")) {
$color = "red";
}
else {
$color = "#000000";
}

echo"<td align=center width=30 height=30><b>
<a href=ViewDate.php?date=$j&month=$month&year=$year target=ViewDate><font color=$color size=-1>
<font color=$color size=-1><b>$j</b></font></td>";
}

echo"</tr>";

echo"<tr>";
for($k = $j; $k <= ($j + 6); $k++) {

if( $k == date("d")) {
$color = "red";
}
else {
$color = "#000000";
}  

echo"<td align=center width=30 height=30><b>
<a href=ViewDate.php?date=$k&month=$month&year=$year target=ViewDate><font color=$color size=-1>
<font color=$color size=-1><b>$k</b></font></td>";
}

echo"</tr>";

echo"<tr>";

for($l = $k; $l <= ($k + 6); $l++) {

if( $l == date("d")) {
$color = "red";
}
else {
$color = "#000000";
}  

echo"<td align=center width=30 height=30><b>
<a href=ViewDate.php?date=$l&month=$month&year=$year target=ViewDate>
<font color=$color size=-1><b>$l</font></b></td>";
}

echo"</tr>";
echo"<tr>";

if(($no_days - $l) >= 7) {
$roll_over = $l + 6;
}

for($m = $l; $m <= $roll_over; $m++) {

if( $m == date("d")) {
$color = "red";
}
else {
$color = "#000000";
}  

echo"<td align=center width=30 height=30><b>
<a href=ViewDate.php?date=$m&month=$month&year=$year target=ViewDate>
<font color=$color size=-1><b>$m</b></font></td>";
}

echo"</tr>";
echo"<tr>";

for($n = $m; $n <= $no_days; $n++) {

if( $n == date("d")) {
$color = "red";
}
else {
$color = "#000000";
}
  
echo"<td align=center width=30 height=30><b>
<a href=ViewDate.php?date=$n&month=$month&year=$year target=ViewDate>
<font color=$color size=-1><b>$n</b></font>";
}

echo"</tr>";
echo"</table></td></tr></table>";
echo"<hr>";
?>

<b>View Month</b>
<table>
<tr>
    <td>
      <select name="sel_year" class="TextField">
	<?php
	$tyear = date("Y");
	for($i = $tyear; $i < ($tyear+10); $i++)
          echo"<option value=$i>$i";
	?>
      </select>
    </td>
    <td>
      <select name="sel_month" class="TextField">
	<?php
	$tmonth = date("m");
	for($j = 1; $j <= 12; $j++)
          echo"<option value=$j>$j";
	?>
      </select>
    </td>
    <td><input type=submit name=submit value=Go class="TextField"></td>
  </tr>
  <tr>
    <td><font size=-1>(year/month)</font></td>
  </tr>
</table>
</form>
</font>
</center>
</div>
</body>
</html>


