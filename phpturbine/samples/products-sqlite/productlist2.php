<?
# database access info:
$dbFile = "turbine_samples.db";

# load the dataset_import.php library:
require_once("../../libraries/dataset_import.php");

# create the turbine object:
$turbine = new Turbine7();

# load the template:
$turbine->load("productlist2.swf");

# set current frame to 0, so that the list is filled right on the first frame:
$turbine->frame(0);

# access the Products table on the SQLite database
#================================================
$db = sqlite_open($dbFile);

$query = "SELECT ProductName as label, ProductId as data FROM Products ORDER BY ProductName";
$recordSet = sqlite_query($db, $query);

loadDataSetSQLite($turbine, $recordSet, "ProductsDataSet");

sqlite_close($db);

# fill the ProductListBox with the DataSet:
$turbine->injectData("ProductsDataSet", "ProductListBox");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>