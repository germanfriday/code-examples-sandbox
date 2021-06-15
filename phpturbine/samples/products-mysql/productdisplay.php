<?
# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# database access info, please modify as needed:
$dbHost = "localhost";
$dbUser = "user";
$dbPass = "password";

# create the turbine object:
$turbine = new Turbine7();

if($_GET["id"] == ""){
# display a message complaining about missing id:
  $turbine->create("<Text pos='0,100'>Please select a Flower from the list...</Text>");

# now generate the movie to the web browser:
  $turbine->generateFlash();
  exit;
}

# access the Products table on the MySQL database
#================================================
$conn = mysql_connect( $dbHost, $dbUser, $dbPass );
mysql_select_db("turbine_samples_db");

$query = "SELECT ProductName, ProductDetails, ProductPrice, ProductImage FROM Products WHERE ProductId = " . $_GET["id"];
$recordSet = mysql_query( $query, $conn);

$row = mysql_fetch_row($recordSet);

# set Turbine variables from the query result values:
$turbine->setVariable("ProductName", $row[0]); # ProductName column
$turbine->setVariable("ProductDetails", $row[1]); # ProductDetails column
$turbine->setVariable("ProductPrice", $row[2]); # ProductPrice column

# since images are on the common sub-directory we add "../common/":
$turbine->setVariable("ProductImage", "../common/" . $row[3]); # ProductImage column

mysql_close($conn);

# load the template:
$turbine->load("productdisplay.swf");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>