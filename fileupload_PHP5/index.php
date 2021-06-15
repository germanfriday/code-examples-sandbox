<? error_reporting(E_ALL | E_STRICT) ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>fileupload.class.php example</title>
	<style type="text/css" title="text/css">
	/* <![CDATA[ */
		body { margin: 30px; padding: 0; background: #FEFEFE; font: normal .8em/1.25 'Lucida Grande', Arial, tahoma, verdana, sans-serif; }
		h1 { margin: 40px 0 0 0; padding: 0; font-size: 24px; }
		h2 { margin: 0; padding: 0; font-size: 1em; font-weight: normal; }
		hr { margin: 25px 0; padding: 0; border: none;  color: #999;  background-color: #999;  height: 1px;  }
	/* ]]> */
	</style>
</head>
<body>



<?php

/** Form Processor **/
if (isset($_REQUEST['form_submitted'])) {
	require("fileupload.class.php");
	
	// Preferences
	$upload = new fileupload($_POST['language']);
	$upload->set_max_filesize(15000);
	$upload->set_acceptable_types(array("image"));
	$upload->set_reject_extensions('.png, .php, .php3, .phps'); // comma separated string, or array
	$upload->set_max_image_size(500,500); // width, height
	$upload->set_overwrite_mode(2);
	

	// UPLOAD single file
	$filename = $upload->upload("single", "uploads/");

	print("<h1>Results From Single Upload Box</h1>\n");
	if ($filename) {
		echo '<p style="color:green">';
		echo $filename . ' successfully uploaded';
		echo '</p>';
	} else {
		echo '<p style="color:red">';
		echo $upload->get_error();
		echo '</p>';
	}
	
	
	// UPLOAD mulitple files
	$filename = $upload->upload("multiple", "uploads/");

	print("<h1>Results From Multiple Upload Boxes</h1>\n");
	if ($filename) {
		echo '<p style="color:green">';
		echo implode(', ', $filename) . ' successfully uploaded';
		echo '</p>';
	} else {
		echo '<p style="color:red">';
		echo $upload->get_error();
		echo '</p>';
	}
	echo "<hr />\n\n";
}



/** HTML Form **/
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
	
	<h1>Single Upload</h1>
	<h2><em>Form field named 'single'</em></h2>
	<p><input type="file" name="single" style="width: auto;" /></p>
	
	
	<h1>Multiple Upload</h1>
	<h2><em>Each form field named 'multiple[]', sent as array</em></h2>
	<p><input type="file" name="multiple[]" style="width: auto;" /></p>
	<p><input type="file" name="multiple[]" style="width: auto;" /></p>
	<p><input type="file" name="multiple[]" style="width: auto;" /></p>
	
	<hr />
	
	<p>
		Error Messages:&nbsp;<select name="language">
			<option value="en">English</option>
			<option value="fr">French</option>
			<option value="de">German</option>
			<option value="nl">Dutch</option>
			<option value="it">Italian</option>
			<option value="fi">Finnish</option>
			<option value="es">Spanish</option>
			<option value="no">Norwegian</option>
			<option value="da">Danish</option>
			<option value="se">Swedish</option>
		</select>
	</p>
	
	<p>
		<input type="hidden" name="form_submitted" value="true" />
		<input type="submit" />
	</p>

</form>

</body>
</html>