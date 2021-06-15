<?
# database access info, please modify as needed:
$dbHost = "localhost";
$dbUser = "user";
$dbPass = "password";

# load the dataset_import.php library:
require_once("../../libraries/dataset_import.php");

# create the turbine object:
$turbine = new Turbine7();

# load the template:
$turbine->load("productlist1.swf");

# set current frame to 0, so that the list is filled right on the first frame:
$turbine->frame(0);

# access the Products table on the MySQL database
#================================================
$conn = mysql_connect( $dbHost, $dbUser, $dbPass );
mysql_select_db("turbine_samples_db");

$query = "SELECT ProductName as label, ProductId as data FROM Products ORDER BY ProductName";
$recordSet = mysql_query( $query, $conn);

loadDataSetMySQL($turbine, $recordSet, "ProductsDataSet");

mysql_close($conn);

# fill the ProductListBox with the DataSet:
$turbine->injectData("ProductsDataSet", "ProductListBox");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>