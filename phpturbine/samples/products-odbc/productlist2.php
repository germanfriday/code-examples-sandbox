<?
# database access info, please modify as needed:
$dbDSN = "turbine_samples_dsn";
$dbUser = "user";
$dbPass = "password";

# load the dataset_import.php library:
require_once("../../libraries/dataset_import.php");

# create the turbine object:
$turbine = new Turbine7();

# load the template:
$turbine->load("productlist2.swf");

# set current frame to 0, so that the list is filled right on the first frame:
$turbine->frame(0);

# access the Products table on the products.mdb database
#=======================================================
$conn = odbc_connect( $dbDSN, $dbUser, $dbPass);

$query = "SELECT ProductName as label, ProductId as data FROM Products ORDER BY ProductName";
$recordSet = odbc_exec( $conn, $query );

loadDataSetODBC($turbine, $recordSet, "ProductsDataSet");

odbc_close($conn);

# fill the ProductListBox with the DataSet:
$turbine->injectData("ProductsDataSet", "ProductListBox");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>