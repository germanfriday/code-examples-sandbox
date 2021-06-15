<?
# disable PHP notices to simplify:
error_reporting(E_ALL ^ E_NOTICE);

# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# create the turbine object:
$turbine = new Turbine7();

# browsers should not cache this request:
header ("Expires: Sat, 01 Jan 2000 01:01:01 GMT");

# tell Turbine to assign the {name} Turbine variable to 
# what was sent on the request:
$turbine->setVariable("name", $_GET["name"]);

# load .swf template media:
$turbine->load("helloworld1.swf");

if($_GET["pdf"]){
# now generate the document to the web browser:
  $turbine->generatePDF();
}
else {
# generate the HTML page with the plug-in if this is the first time
# use the default html page:
  $turbine->generateHTMLFirst();

# now generate the movie to the web browser:
  $turbine->generateFlash();
}

?>