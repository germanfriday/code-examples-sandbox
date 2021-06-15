<?
# create the turbine object:
$turbine = new Turbine7();

# set native width and height:
$turbine->width(600);
$turbine->height(200);

# load a variables file:
$turbine->loadVar("varfile.txt");

# display two loaded variables by using two <text> tags:
$turbine->create("<text pos='10,10'>var1 = {var1}</text>");
$turbine->create("<text pos='10,30'>var2 = {var2}</text>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>