<?php
#ini_set('display_errors', 'On');

// See class.breadcrumb.inc.php or the README files for Usage Directions
include_once('class.breadcrumb.inc.php');
$breadcrumb = new breadcrumb;
$breadcrumb->homepage='homepage'; // sets the home directory name
$breadcrumb->dirformat='ucfirst'; // Show the directory in this style
$breadcrumb->symbol=' || '; // set the separator between directories 
$breadcrumb->showfile=TRUE; // shows the file name in the path
$breadcrumb->linkFile=TRUE; // Link the file to itself
$breadcrumb->_toSpace=TRUE; // converts underscores to spaces
echo "<p>".$breadcrumb->show_breadcrumb()."</p>";
?>