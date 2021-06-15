<?
# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# disable PHP notices to simplify:
error_reporting(E_ALL ^ E_NOTICE);

# create the turbine object:
$turbine = new Turbine7();

# load one of the text*.mml:
$turbine->load("text" . $_GET["num"] . ".mml");

# set native width and height:
$turbine->width(600);
$turbine->height(200);

if($_GET["media"] == "pdf"){
# now generate the document to the web browser:
  $turbine->generatePDF();
}
else {
# now generate the movie to the web browser:
  $turbine->generateFlash();
}

?>