<?
# create the turbine object:
$turbine = new Turbine7();

# set native width and height:
$turbine->width(600);
$turbine->height(200);

# make a DataSet "manually":
$turbine->makeDataSet("handmade_dataset", "col1,col2");
$turbine->addDataSetRow("r0.c0,r0.c1");
$turbine->addDataSetValue("handmade_dataset", "col2", 0, "new value");
$turbine->addDataSetColumn("handmade_dataset", "col3", 0, "r0.c2");

# display DataSet cells by using some <text> tags -
# we escape $name as \$name to avoid being interpreted as PHP variables:
$turbine->create("<text pos='10,10'>handmade_dataset\$col1.0 = {handmade_dataset\$col1.0}</text>");
$turbine->create("<text pos='10,30'>handmade_dataset\$col2.0 = {handmade_dataset\$col2.0}</text>");
$turbine->create("<text pos='10,50'>handmade_dataset\$col3.0 = {handmade_dataset\$col3.0}</text>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>