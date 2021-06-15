<?
# disable PHP notices to simplify:
error_reporting(E_ALL ^ E_NOTICE);

# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# create the turbine object:
$turbine = new Turbine7();

# tell Turbine to load the DataSet values from a file:
$turbine->loadDataSet("transactions_dataset.txt", "Transactions");

# pass the type inside Turbine:
$turbine->setVariable("Type", $_GET["type"]);

# pass the urlColumn inside Turbine:
if($_GET["url"] == "Yes")
  $turbine->setVariable("UrlColumn", "url");
else
  $turbine->setVariable("UrlColumn", "");

# pass the tooltipColumn inside Turbine:
if($_GET["tooltip"] == "Yes")
  $turbine->setVariable("TooltipColumn", "to");
else
  $turbine->setVariable("TooltipColumn", "");

# pass 3d params inside Turbine:
$turbine->setVariable("threeDDepth", $_GET["threeDDepth"]);
$turbine->setVariable("threeDAngle", $_GET["threeDAngle"]);


if($_GET["pdf"]){
# load MML template:
  $turbine->load("charts.mml");

# now generate the document to the web browser:
  $turbine->generatePDF();
}
else {
# load Flash template:
  $turbine->load("chart.swf");

# now generate the movie to the web browser:
  $turbine->generateFlash();
}

?>