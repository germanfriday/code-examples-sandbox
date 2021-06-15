<?php
//	include the calendar file
$the_file="ac-includes/cal.inc.php";
if(!file_exists($the_file)) die("<b>".$the_file."</b> not found");
else		require_once($the_file);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>cbolson.com - <?php echo AC_TITLE; ?></title>
		<link rel="stylesheet" href="<?php echo AC_DIR_CSS; ?>avail-calendar.css">
		<script type="text/javascript">		
		//	define vars
		var url_ajax_cal 		= '<?php echo AC_DIR_AJAX; ?>calendar.ajax.php'; // ajax file for loading calendar via ajax
		var img_loading_day		= '<?php echo AC_DIR_IMAGES; ?>ajax-loader-day.gif'; // animated gif for loading	
		var img_loading_month	= '<?php echo AC_DIR_IMAGES; ?>ajax-loader-month.gif'; // animated gif for loading	
		//	don't change these values
		var id_item 			= '<?php echo ID_ITEM; ?>'; // id of item to be modified (via ajax)
		var lang 				= '<?php echo LANG; ?>'; // language
		var months_to_show		= <?php echo AC_NUM_MONTHS; ?>; // number of months to show
		var clickable_past		= '<?php echo AC_ACTIVE_PAST_DATES; ?>'; // previous dates
		</script>
		<script type="text/javascript" src="<?php echo AC_DIR_JS; ?>mootools-1.2.4-core-yc.js"></script>
		<script type="text/javascript" src="<?php echo AC_DIR_JS; ?>mootools-cal-public.js"></script>
	</head>
	<body>
	<?php
	echo '
	<div id="cal_wrapper">
		<div id="cal_controls">
			<div id="cal_prev" title="'.$lang["prev_X_months"].'"><img src="'.AC_DIR_IMAGES.'icon_prev.gif" class="cal_button"></div>
			<div id="cal_next" title="'.$lang["next_X_months"].'"><img src="'.AC_DIR_IMAGES.'icon_next.gif" class="cal_button"></div>
			
			<!-- optional calendar change options -->
			<div id="cal_admin">
				<form method="get">
				<select name="id_item" class="select" onchange="this.form.submit();">
					'.sel_list_items($_REQUEST["id_item"]).'
				</select>
				<select name="lang" class="select" onchange="this.form.submit();">
						'.$list_languages_web.'
					</select>
				</form>
			</div>
			<!-- end options -->
			<div class="clear"></div>
		</div>
		<h2>'.itemTitle(ID_ITEM).'</h2>
		<div id="the_months">
			'.$calendar_months.'
		</div>
		<div id="key_wrapper">
			'.$calendar_states.'
			<div id="footer_data" style="clear:both;">
				'.$lang["last_update"].': '.get_cal_update_date(ID_ITEM).'
			</div>
		</div>
		<div class="clear"></div>
	</div>
	';
	?>
	</body>
</html>