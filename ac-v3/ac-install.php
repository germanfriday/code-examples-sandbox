<?php
/*
File:	ac-install.php
Author: cbolson.com 
Script: availability calendar
Version: 3.0
Url: http://www.cbolson.com/sandbox/availability-calender/v3.0/
Date Created: 26-08-2009   
Use:	1. Define config values (check file is writable first)
		2. Install database
		3. Check files exist?
*/

//	define path to root
$dir=dirname(__FILE__);
if(substr($dir,-1)!="/") $dir.='/';	# add trailing slash if required

define("AC_ROOT", 		$dir);
define("FILE_CONFIG",	"ac-config.inc.php");

$check_items=array();
$check_items[1]=array(
	"pend"	=> "Check configuration file <u>".FILE_CONFIG."</u> exists.",
	"ok"	=> "Configuration file exists.",
	"ko"	=> "Configuration file <u>".FILE_CONFIG."</u> does not exist."
	);

$check_items[2]=array(
	"pend"	=> "Define database configuration.",
	"ok"	=> "Database configuration defined.",
	"ko"	=> "Database configuration not defined",
	"current"	=> "Define database configuration."
	);
$check_items[3]=array(
	"pend"	=> "Create Database Tables",
	"ok"	=> "Database tables created",
	"ko"	=> "Unable to create database tables"
	);
$check_items[4]=array(
	"pend"	=> "Define calendar config",
	"ok"	=> "Calendar Configured for use.",
	"ko"	=> "Calendar configuration not defined."
	);
	
	
	
$check_item_states=array();

//	check config file exists
if(!file_exists(FILE_CONFIG))	$check_item_states[1]="ko";	
else 							$check_item_states[1]="ok";


//	is config writable?
if($check_item_states[1]=="ok"){
	//	config file exists
	/*
	1-check db connection  - if user has modified manually we can move on - no need to check if writable etc
	2-check is writable
	3-check has values
	4-c
	*/
	/*
	$check_items[2]=array(
	"pend"	=> "Check confiuration <u>".FILE_CONFIG."</u> file is writable",
	"ok"	=> "Configuration file is writable.",
	"ko"	=> "<u>".FILE_CONFIG."</u> is not writable - chmod to 777 then <a href='ac-install.php'>click here</a>."
	);*/
	
	//	if form posted - write file
	if(isset($_POST["db"])){
		//	write db connect values to config file
		$fh = fopen(FILE_CONFIG, 'w') or die("can't open file");
		$stringData = '<?php
//	 database settings
define("AC_DB_HOST",		"'.$_POST["db"]["host"].'");
define("AC_DB_NAME",		"'.$_POST["db"]["name"].'");
define("AC_DB_USER",		"'.$_POST["db"]["username"].'");
define("AC_DB_PASS",		"'.$_POST["db"]["password"].'");
define("AC_DB_PREFIX",		"");
//	do not alter this line
define("AC_INLCUDES_ROOT",	AC_ROOT."ac-includes/");
?>
';
		fwrite($fh, $stringData);
		fclose($fh);
	}
	
	//	check connection
	//	try to connect to db
	//	general config
	require_once(FILE_CONFIG);
	
	
	if(check_db_connection()){
		
		//	db connection ok - move on to next state
		$check_items_states[2]="ok";
	}else{
		
		//	check for values
		if( AC_DB_HOST=="" || AC_DB_USER==""  || AC_DB_PASS==""  || AC_DB_NAME=="" ){
			//	config values not set - show form
			$show_config_form=true;
		}else{
			//	values there but NOT correct
			$show_config_form=true;
			$waring='<div class="warning">Unable to connect to database.  Please check your data.</div>';
		}
	}
	
	if($show_config_form){
		//	check file is writable
		if(!is_writable(FILE_CONFIG)){
			$check_item_states[2]="ko";	
			$check_items[2]["ko"]="File <u>".FILE_CONFIG."</u> is not writable - chmod to 777 then <a href='ac-install.php'>click here</a>";
		}else{
			
			
			
			$check_item_states[2]="current";	
			$check_items[2]["current"]='
			<fieldset>
			<legend>Define your database configuration settings:</legend>
			'.$waring.'
			<form method="post" action="">
			<input type="hidden" name="page" value="'.ADMIN_PAGE.'">
			<dl>
				<dt><label for="id_title">Database Host</label></dt>
				<dd><input type="text" name="db[host]" value="'.AC_DB_HOST.'"></dd>
				
				<dt><label for="id_title">Database Name</label></dt>
				<dd><input type="text" name="db[name]" value="'.AC_DB_NAME.'"></dd>
				
				<dt><label for="id_title">Database Username</label></dt>
				<dd><input type="text" name="db[username]" value="'.AC_DB_USER.'"></dd>
				
				<dt><label for="id_title">Database Password</label></dt>
				<dd><input type="text" name="db[password]" value="'.AC_DB_PASS.'"></dd>
				
				<input type="submit" class="submit" value="Save Configuration" style="width:240px;">
			</dl>
			
			</form>
			</fieldset>
			';
		}
		
	}else{
		$check_item_states[2]="ok";
	}
}

//	database tables
if($check_item_states[2]=="ok"){
	
	
	
	//	check if tables exist
	if(!mysql_is_table(AC_DB_PREFIX."bookings")){
		//	add tables.....
		if(create_tables())		$check_item_states[3]="ok";
		else 					$check_item_states[3]="ko";
	}else{
		$check_item_states[3]="ok";
	}	
}



if($check_item_states[3]=="ok"){
	//	common vars (db and lang)
	$the_file=AC_INLCUDES_ROOT."common.inc.php";
	if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);
	

	//	calendar functions
	$the_file=AC_INLCUDES_ROOT."functions.inc.php";
	if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);
		
	//	admin functions
	$the_file=AC_INLCUDES_ROOT."functions-admin.inc.php";
	if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
	else		require_once($the_file);
	
	if(isset($_POST["add_config"])){
		//	insert calendar config - TO DO
		$mod_item=mod_item(T_BOOKINGS_CONFIG,1,$_POST["mod"],false);
		if($mod_item=="OK")	$check_item_states[4]="ok";
		else 				$check_item_states[4]="ko";
	}else{
	
		//	define calendar configuration settings
		
		//	include lang file
		$the_file=AC_DIR_LANG."en.lang.php";
		if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
		else		require_once($the_file);
		
		
		$cal_config_form='
		<fieldset>
		<legend>Define your calendar options:</legend>
		<form method="post" action="">
		<input type="hidden" name="add_config" value="1">
		<dl>
			<dt><label for="id_title">'.$lang["title"].'</label></dt>
			<dd><input id="id_title" type="text" name="mod[title]" value="'.$row_config["title"].'" style="width:300px;" /></dd>
			
			<dt><label for="id_cal_url">'.$lang["cal_url"].'</label></dt>
			<dd><input id="id_cal_url" type="text" name="mod[cal_url]" value="'.dirname($_SERVER["SCRIPT_NAME"]).'" style="width:110px;" />
				<span class="note">'.$lang["note_cal_url"].'</span>
			</dd>
			
			<dt><label for="id_default_lang">'.$lang["default_lang"].'</label></dt>
			<dd>
				<select id="id_default_lang" name="mod[default_lang]" class="select" style="width:140px;">
					'.$list_languages_config.'
				</select>
			</dd>
			
			<dt><label for="id_num_months">'.$lang["num_months"].'</label></dt>
			<dd>
				<select id="id_num_months" name="mod[num_months]" class="select" style="width:140px;">
					'.list_numbers(1,12,$row_config["num_months"]).'
				</select>
			</dd>
			
			<dt><label for="id_start_day">'.$lang["start_day"].'</label></dt>
			<dd>
				<select id="id_start_day" name="mod[start_day]" class="select" style="width:140px;">
					<option value="sun"'; if($row_config["start_day"]=="sun") $cal_config_form.=' selected="selected"'; $cal_config_form.='>'.$lang["day_0"].'</option>
					<option value="mon"'; if($row_config["start_day"]=="mon") $cal_config_form.=' selected="selected"'; $cal_config_form.='>'.$lang["day_1"].'</option>
				</select>
			</dd>
			
			<dt><label for="id_date_format">'.$lang["date_format"].'</label></dt>
			<dd>
				<select id="id_date_format" name="mod[date_format]" class="select" style="width:140px;">
					<option value="us"'; if($row_config["date_format"]=="us") $cal_config_form.=' selected="selected"'; $cal_config_form.='>'.$lang["date_format_us"].'</option>
					<option value="eu"'; if($row_config["date_format"]=="eu") $cal_config_form.=' selected="selected"'; $cal_config_form.='>'.$lang["date_format_eu"].'</option>
				</select>
			</dd>
			
			<dt><label for="id_click_past_dates">'.$lang["click_past_dates"].'</label></dt>
			<dd>
				<select id="id_click_past_dates" name="mod[click_past_dates]" class="select" style="width:140px;">
					<option value="on"'; if($row_config["click_past_dates"]=="on") $cal_config_form.=' selected="selected"'; $cal_config_form.='>'.$lang["yes"].'</option>
					<option value="off"'; if($row_config["click_past_dates"]=="off") $cal_config_form.=' selected="selected"'; $cal_config_form.='>'.$lang["no"].'</option>
				</select>
			</dd>
			<input type="submit" class="submit" value="Save Configuration" style="width:240px;">
		</dl>
		
		</form>
		</fieldset>
		';
		$check_item_states[4]="current";	
		$check_items[4]["current"]=$cal_config_form;
	}
	
}

$final_message='';
if($check_item_states[4]=="ok"){
	$final_message='
	Congratulations, your calendar is now ready to use.
	<br> - Now <a href="ac-admin/index.php" target="admin">login</a> to login to your admin panel to administrate your calendar(s)
	<br> - Username : "admin"
	<br> - Password : "demo" (You should change these as soon as possible).
	<br><br> - You should now remove this file (ac-install.php) from your ftp and remove the ac-config.inc.php file write permissions (eg set to 644).
	<br><br> - Click <a href="index.php">here</a> to see your calendar.
	<br> - <span style="color:red;">Note - you should now check the css files @ "/ac-contents/themes/default/css/avail-calendar.css" to define the image path to the special state background image.</span>
	
	';
}

$check_list="";
foreach($check_items as $id=>$text){
	//echo "<br>".$id;
	if(array_key_exists($id,$check_item_states))	$this_state=$check_item_states[$id];
	else 											$this_state="pend";
	
	$check_list.='<li class="'.$this_state.'">'.$text["".$this_state.""].'</li>';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Ajax Availability Calendar - Install</title>
		<link rel="stylesheet" href="ac-admin/css/admin.css">
	</head>
	<body>
	<body id="page_install">
	<div id="wrapper">
		<div id="header">
				<img src="http://www.ajaxavailabilitycalendar.com/images/logo_aac.png" title="Availability Calendar">
			</div>
		<div id="contents">
			<p>
				Follow these steps to install the calendar in your server.
			</p>
			<?php 
			echo '
			<ul>
			'.$check_list.'
			</ul>
			<p>
			'.$final_message.'
			</p>
			';
			?>
			<div class="clear"></div>
		</div>
		<div id="footer">
			<div style="float:right;">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="5972777">
				<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." style="border:none;">
				<img alt="" border="0" src="https://www.paypal.com/es_ES/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
			<ul>
				<li><a href="http://www.ajaxavailabilitycalendar.com/">Availability Calendar</a> developed by <a href="http://www.cbolson.com" target="_blank">Chris Bolson</a></li>
				<li>Icons by <a href="http://dryicons.com" target="_blank">http://dryicons.com</a></li>
			</ul>
		</div>
	</div>
	
	</body>
</html>


<?php

//	check db connection
function check_db_connection(){
	//	db connection
	if(!$db = @mysql_connect(AC_DB_HOST,AC_DB_USER,AC_DB_PASS)) {
		//echo "can't connect";
		return false;
	}
	if(!@mysql_select_db(AC_DB_NAME,$db)){
		//echo "can't find database";
		return false;
	}
	return true;
}

//	check table is created
function mysql_is_table($tbl){
	$tables = array();
	$sql="SHOW TABLES";
	$res=mysql_query($sql);
	while ($r = mysql_fetch_array($res)) {
		$tables[] = $r[0];
	}
	if (in_array($tbl, $tables))	return TRUE;
	else							return FALSE;
}

function create_tables(){
$sql=array();
//	Table structure for table `bookings`
$sql["Create Table - BOOKINGS"]="
CREATE TABLE IF NOT EXISTS `".AC_DB_PREFIX."bookings` (
  `id` int(11) NOT NULL auto_increment,
  `id_item` int(20) NOT NULL default '0',
  `the_date` date NOT NULL default '0000-00-00',
  `id_state` int(11) NOT NULL default '0',
  `id_booking` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_item` (`id_item`),
  KEY `id_state` (`id_state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";


//	Table structure for table `bookings_admin`
$sql["Create Table - ADMIN"]="
CREATE TABLE IF NOT EXISTS `".AC_DB_PREFIX."bookings_admin_users` (
  `id` int(11) NOT NULL auto_increment,
  `level` tinyint(1) NOT NULL default '2',
  `username` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `state` tinyint(1) NOT NULL default '1',
  `date_visit` datetime NOT NULL default '0000-00-00 00:00:00',
  `visits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
";
 
//	Dumping data for table `bookings_admin`
$sql["Insert data - ADMIN"]="
INSERT INTO `".AC_DB_PREFIX."bookings_admin_users` SET
	`id`		=	1,
	`level`		=	1,
	`username`	=	'admin',
	`password`	=	'fe01ce2a7fbac8fafaed7c982a04e229',
	`state`		=	1,
	`date_visit`=	'0000-00-00 00:00:00',
	`visits`	=	0
";


// Table structure for table `bookings_config`
$sql["Create Table - CONFIG"]="
CREATE TABLE IF NOT EXISTS `".AC_DB_PREFIX."bookings_config` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `num_months` tinyint(3) NOT NULL default '3',
  `default_lang` varchar(6) NOT NULL default 'en',
  `theme` varchar(50) NOT NULL default 'default',
  `start_day` enum('mon','sun') NOT NULL default 'sun',
  `date_format` enum('us','eu') NOT NULL default 'eu',
  `click_past_dates` enum('on','off') NOT NULL default 'off',
  `cal_url` varchar(255) NOT NULL default '',
  `local_path` varchar(255) NOT NULL default '/calendar',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
";

//	Dumping data for table `bookings_config`
$sql["Insert Data - CONFIG"]="
INSERT INTO `".AC_DB_PREFIX."bookings_config` SET
	`id`				=	1,
	`title`				=	'Availability Calendar',
	`num_months`		=	3,
	`default_lang`		=	'en',
	`start_day`			=	'sun',
	`date_format`		=	'eu',
	`click_past_dates`	=	'off',
	`cal_url`			=	'/calendar'
";

//	Table structure for table `bookings_items`
$sql["Create Table  - ITEMS"]="


CREATE TABLE `".AC_DB_PREFIX."bookings_items` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '1',
  `id_ref_external` int(11) NOT NULL COMMENT 'link to external db table',
  `desc_en` varchar(100) NOT NULL default '',
  `desc_es` varchar(100) NOT NULL default '',
  `list_order` int(11) NOT NULL default '0',
  `state` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_ref_external` (`id_ref_external`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";

//	Dumping data for table `bookings_items` - DEMO ITEM
$sql["Insert Data - ITEMS"]="
INSERT INTO `".AC_DB_PREFIX."bookings_items` SET 
	`id`		=	1,
	`id_user`	=	1,
	`desc_en`	=	'Demo Item',
	`desc_es`	=	'Demostración',
	`list_order`=	1,
	`state`		=	1
";

//	Table structure for table `bookings_last_update`
$sql["Create Table - UPDATE TIME"]="
CREATE TABLE IF NOT EXISTS `".AC_DB_PREFIX."bookings_last_update` (
  `id` int(10) NOT NULL auto_increment,
  `id_item` int(10) NOT NULL default '0',
  `date_mod` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";

//	Table structure for table `bookings_states`
$sql["Create Table - STATES"]="
CREATE TABLE IF NOT EXISTS `".AC_DB_PREFIX."bookings_states` (
  `id` int(11) NOT NULL auto_increment,
  `desc_en` varchar(100) NOT NULL default '',
  `desc_es` varchar(100) NOT NULL default '',
  `code` varchar(10) NOT NULL default '',
  `state` tinyint(1) NOT NULL default '1',
  `list_order` int(11) NOT NULL default '0',
  `class` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
";

//	Dumping data for table `bookings_states`
$sql["Insert Data - STATES"]="
INSERT INTO `".AC_DB_PREFIX."bookings_states` 
	(`id`, `desc_en`, `desc_es`, `code`, `state`, `list_order`, `class`) 
VALUES 
	(1, 'Booked', 'Reservado', 'b', 1, 0, 'booked'),
	(2, 'Booked am', 'Reservado am', 'b_am', 1, 1, 'booked_am'),
	(3, 'Booked pm', 'Reservado pm', 'b_pm', 1, 2, 'booked_pm'),
	(4, 'Provisional', 'Provisional', 'pr', 1, 3, 'booked_pr'),
	(5, 'Provisional am', 'Provisional am', 'pr_am', 1, 4, 'booked_pr_am'),
	(6, 'Provisional pm', 'Provisional pm', 'pr_pm', 1, 5, 'booked_pr_pm'),
	(7, 'Change Over Day', 'Salida-Entrada', '', 1, 6, 'changeover'),
	(8, 'Special Offer', 'Oferta Especial', '', 1, 7, 'offer');
";
	
	//	loop through table create and inserts
	foreach($sql AS $type=>$query){
	//	echo "<br>".$type;
		mysql_query($query) or die("Error creating database table - ".$type."<br>".$query."<br>".mysql_error());
	}
	return true;

}
?>