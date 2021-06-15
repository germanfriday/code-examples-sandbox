<?
# create the turbine object:
$turbine = new Turbine7();

# set native width and height:
$turbine->width(600);
$turbine->height(200);

# load a DataSet file:
$turbine->loadDataSet("dataset.txt", "data");

# display DataSet cells by using some <text> tags -
# we escape $name as \$name to avoid being interpreted as PHP variables:
$turbine->create("<text pos='10,{data\$y.0}' color='{data\$color.0}'>row 0 url: {data\$url.0}</text>");
$turbine->create("<text pos='10,{data\$y.1}' color='{data\$color.1}'>row 1 url: {data\$url.1}</text>");
$turbine->create("<text pos='10,{data\$y.2}' color='{data\$color.2}'>row 2 url: {data\$url.2}</text>");
$turbine->create("<text pos='10,{data\$y.3}' color='{data\$color.3}'>row 3 url: {data\$url.3}</text>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>