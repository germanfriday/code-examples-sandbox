<?
# create the turbine object:
$turbine = new Turbine7();

# set native width and height:
$turbine->width(600);
$turbine->height(200);

# load a DataSet from an XML string
$turbine->dataSetFromString("<chapter><title titleattr='1st launch'>First Launch</title><pages pagesattr='45'/><interesting>90%</interesting><topics>Love, hate, adventure</topics></chapter>", "book", "chapter", "interesting=int, title#titleattr, pages#pagesattr=pg, topics, pages#pagesattr");

# display DataSet cells by using some <text> tags -
# we escape $name as \$name to avoid being interpreted as PHP variables:
$turbine->create("<text pos='10,10'>book\$title#titleattr.0 = {book\$title#titleattr.0}</text>");
$turbine->create("<text pos='10,30'>book\$int.0 = {book\$int.0}</text>");
$turbine->create("<text pos='10,50'>book\$pg.0 = {book\$pg.0}</text>");
$turbine->create("<text pos='10,70'>book\$topics.0 = {book\$topics.0}</text>");
$turbine->create("<text pos='10,90'>book\$pages#pagesattr.0 = {book\$pages#pagesattr.0}</text>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>