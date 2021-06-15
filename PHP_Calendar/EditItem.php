<?php
	session_start();
	header("Cache-control: private"); //IE 6 Fix 

	$user_name = $_SESSION['uid'];
	if(!$user_name) {
		header('Fail.php');
	}
	require('GetItems_DS.php');
	$getItems_DS = new GetItems_DS;
	$result = $getItems_DS->getCurrItem($user_name, $_GET['item_id']);
   $page_num = $_GET['page'];
?>

<html>
<head>
	<title>EditItem</title>
	<meta http-equiv="content-type"	content="text/html;	charset=iso-8859-1"/>
	<LINK href="images/styles.css" type=text/css rel=stylesheet></link>
</head>
<body>
<form method=post action=ItemInsertController.php>
<input type=hidden name=FormName value=EditItem>

<?php
$numrows = mysql_num_rows($result);

$result_row = mysql_fetch_row($result);

$old_item=$result_row[1];
$old_time=$result_row[2];
$old_date=$result_row[3];
$old_ampm=$result_row[7];

$ex_time = explode(":", $result_row[2]);

$curr_date = mktime(0, 0, 0, date("M"), date("D"), date("Y"));

$dt_arr = explode("-", $result_row[3]);
//
echo "<BR>";
?>
	<TABLE BORDER=1 width = 60% cellpadding=5 cellspacing=0 bordercolor=#9999FF><tr><td>
	<TABLE  BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100% BGCOLOR=#FFFFE4>
		<tr>
			<td width=20% align=left bgcolor=#c0c0c0><b>Details</b></td>
  			<td align=right bgcolor=#c0c0c0>
  				<font size=-1><a href="javascript:sndReq('dispose')"><img border=0 src="images/close.jpg"></img></a>
  				&nbsp;&nbsp;
  			</td>
  		</tr>
      <tr>
      <td width=50%><b>Item</b></td>
      <td><select name=item class="TextField">
		<option value=<?php echo "$result_row[1]>$result_row[1]"; ?>
                <option value=Appointment>Appointment
                <option value=Birthday>Birthday
                <option value=Class>Class
                <option value=Meeting>Meeting
                <option value=Reminder>Reminder
                <option value=Other>Other
         </select>
      </td>
      </tr>
      <tr>
      <td width=20%><b>Due Date</b></td>
      <td>
		<select name=sel_year class="TextField">
           <?php 
	        $tyear = date("Y");
   	     for($i = date("Y"); $i <= (date("Y") + 5); $i++) {
   	     	 if($i == $dt_arr[0]) {
   	     	 	echo "<option SELECTED value=$i>$i";
   	     	 }
   	     	 else {
	   	       echo"<option value=$i>$i"; 
	   	    }
   	     } ?>
      </select>

      <select name=sel_month class="TextField">
        	<?php
        	$tmonth = date("m");
        	for($j = 1; $j <= 12; $j++) {
        	    if($j == $dt_arr[1]) {
	         	 echo"<option SELECTED value=$j>$j";
	          }
	          else {
  	         	 echo"<option value=$j>$j";
	          }
         } ?>
      </select>
      <input type=text class="TextField" size=2 name=sel_date value=<?php echo "$dt_arr[2]>"; ?>
      </td>
      </tr>
      <tr>
      <td><b>Due Time</b></td>
      <td>
        <select name=hours class="TextField">
        <?php 
        		if($ex_time[0] > 12) {
        			$ex_time[0] = ($ex_time[0] - 12);
        		}
        		for($hr = 1; $hr <= 12; $hr++) {
        			if($hr == $ex_time[0]) {
	        	  		echo "<option SELECTED value=$hr>$hr";
	        	  	}
	        	  	else {
  	        	  		echo "<option value=$hr>$hr";
	        	  	}
        		} ?>
        </select>
	     <select name=minutes class="TextField">
			<?php 
			if($ex_time[1] == "00") {
				echo "<option SELECTED value=00>00
			         <option value=15>15
   			      <option value=30>30
   			      <option value=45>45";
			}
			if($ex_time[1] == "15") {
				echo "<option value=00>00
			         <option SELECTED value=15>15
   			      <option value=30>30
   			      <option value=45>45";
			}
			if($ex_time[1] == "30") {
				echo "<option value=00>00
			         <option value=15>15
   			      <option SELECTED value=30>30
   			      <option value=45>45";
   		}
   		if($ex_time[1] == "45") {
				echo "<option value=00>00
			         <option value=15>15
   			      <option value=30>30
   			      <option SELECTED value=45>45";
			}
         ?>
        </select>
        <select name=sel_time class="TextField">
        		<?php 
        			if($result_row[4] == "AM") {
        				echo "<option SELECTED value=am>am</option>";
        				echo "<option value=pm>pm</option>";
        			}
        			else {
        				echo "<option value=am>am</option>";
        				echo "<option SELECTED value=pm>pm</option>";
        			}
        		?>
        </select>	
      </tr>
      <tr>
      <td width=20%><b>Notes</b></td>
      <td rowspan=2>
      <textarea name=desc rows=3 cols=40 class="TextField"><?php echo $result_row[5]; ?></textarea></td>
      </tr>
      <tr></tr>
  	 	<tr>
  	 		<td></td>
 			<td>
 				<input type=submit name=submit value=Submit class="TextField">
 			</td>
 		</tr>
 		</table>
 		</td>
 		</tr>
 		</table>
<br>
<input type=hidden name=item_id value=<?php echo $_GET['item_id'];?> >
<input type=hidden name=page value=<?php echo $page_num; ?> >
<center>
</form>
</center>
</body>
</html>





