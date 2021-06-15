<?
# be compatible with old PHP engine versions that don't define $_GET (before PHP 4.1.0):
if (!isset($_GET))
  $_GET=$HTTP_GET_VARS;

# database access info, please modify as needed:
$dbDSN = "turbine_samples_dsn";
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

# access the Products table on the products.mdb database
#=======================================================
$conn = odbc_connect( $dbDSN, $dbUser, $dbPass);

$query = "SELECT ProductName, ProductDetails, ProductImage, ProductPrice FROM Products WHERE ProductId = " . $_GET["id"];
$recordSet = odbc_exec( $conn, $query );

odbc_fetch_row($recordSet);

# set Turbine variables from the query result values:
$turbine->setVariable("ProductName", odbc_result($recordSet, "ProductName"));
$turbine->setVariable("ProductDetails", odbc_result($recordSet, "ProductDetails"));
$turbine->setVariable("ProductPrice", odbc_result($recordSet, "ProductPrice"));

# since images are on the common sub-directory we add "../common/":
$turbine->setVariable("ProductImage", "../common/" . odbc_result($recordSet, "ProductImage"));

odbc_close($conn);

# load the template:
$turbine->load("productdisplay.swf");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>