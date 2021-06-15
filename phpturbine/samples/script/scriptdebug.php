<?
# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# create the turbine object:
$turbine = new Turbine7();

# enable debug Turbine window:
$turbine->debug(true);

# load one of the script*.mml:
$turbine->load("script" . $_GET["num"] . ".mml");

# set native width and height:
$turbine->width(600);
$turbine->height(200);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>