<?
# create the turbine object:
$turbine = new Turbine7();

# set native width and height:
$turbine->width(600);
$turbine->height(200);

# load a DataSet from an XML file:
$turbine->loadDataSet("xml_xmldatasetfile.txt", "book", "chapter", "pages, title, interesting, topics");

# display DataSet cells by using some <text> tags -
# we escape $name as \$name to avoid being interpreted as PHP variables:
$turbine->create("<text pos='10,10'>book\$title.0 = {book\$title.0}</text>");
$turbine->create("<text pos='10,30'>book\$pages.0 = {book\$pages.0}</text>");
$turbine->create("<text pos='10,50'>book\$interesting.0 = {book\$interesting.0}</text>");
$turbine->create("<text pos='10,70'>book\$topics.0 = {book\$topics.0}</text>");
$turbine->create("<text pos='10,100'>book\$title.1 = {book\$title.1}</text>");
$turbine->create("<text pos='10,120'>book\$pages.1 = {book\$pages.1}</text>");
$turbine->create("<text pos='10,140'>book\$interesting.1 = {book\$interesting.1}</text>");
$turbine->create("<text pos='10,160'>book\$topics.1 = {book\$topics.1}</text>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>