<?
# be compatible with old PHP engine versions that don't define $_POST (before PHP 4.1.0):
if (!isset($_POST))
  $_POST=$HTTP_POST_VARS;

# create the turbine object:
$turbine = new Turbine7();

# define Turbine variables:
$turbine->setVariable("bill_to", $_POST["bill_to"]);
$turbine->setVariable("ship_to", $_POST["ship_to"]);
$turbine->setVariable("order_number", $_POST["order_number"]);
$turbine->setVariable("desc_01", $_POST["desc_01"]);
$turbine->setVariable("qty_01", $_POST["qty_01"]);
$turbine->setVariable("price_01", $_POST["price_01"]);
$turbine->setVariable("amount_01", $_POST["amount_01"]);
$turbine->setVariable("desc_02", $_POST["desc_02"]);
$turbine->setVariable("qty_02", $_POST["qty_02"]);
$turbine->setVariable("price_02", $_POST["price_02"]);
$turbine->setVariable("amount_02", $_POST["amount_02"]);
$turbine->setVariable("total", $_POST["total"]);

# load .swf template media:
$turbine->load("invoice.swf");

# now generate the movie to the web browser:
$turbine->generatePDF();

?>