<?
# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# database access info:
$dbFile = "turbine_samples.db";

# create the turbine object:
$turbine = new Turbine7();

if($_GET["id"] == ""){
# display a message complaining about missing id:
  $turbine->create("<Text pos='0,100'>Please select a Flower from the list...</Text>");

# now generate the movie to the web browser:
  $turbine->generateFlash();
  exit;
}

# access the Products table on the SQLite database
#================================================
$db = sqlite_open($dbFile);

$query = "SELECT ProductName, ProductDetails, ProductPrice, ProductImage FROM Products WHERE ProductId = " . $_GET["id"];
$recordSet = sqlite_query($db, $query);

$row = sqlite_fetch_array($recordSet);

# set Turbine variables from the query result values:
$turbine->setVariable("ProductName", $row['ProductName']); # ProductName column
$turbine->setVariable("ProductDetails", $row['ProductDetails']); # ProductDetails column
$turbine->setVariable("ProductPrice", $row['ProductPrice']); # ProductPrice column

# since images are on the common sub-directory we add "../common/":
$turbine->setVariable("ProductImage", "../common/" . $row[3]); # ProductImage column

sqlite_close($db);

# load the template:
$turbine->load("productdisplay.swf");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>