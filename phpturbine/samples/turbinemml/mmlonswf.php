<?
# create the turbine object:
$turbine = new Turbine7();

# load mmlonswf.swf template:
$turbine->load("mmlonswf.swf");

# set native width and height:
$turbine->width(600);
$turbine->height(300);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>