<?
# create the turbine object:
$turbine = new Turbine7();

# include the component definition file:
$turbine->include("flistboxsymbol.swf");

# place the component and pass a value to the labels argument:
$turbine->create("<Place component='ListBox' pos='10,10' instance='lst'/>");

# read a DataSet:
$turbine->loadDataSet("dataset.txt", "data", "row", "value");

# pass the data to the component:
$turbine->injectData("data", "lst");

# set a convenient native height:
$turbine->height(200);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>