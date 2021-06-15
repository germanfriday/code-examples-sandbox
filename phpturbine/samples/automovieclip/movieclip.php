<?
# create the turbine object:
$turbine = new Turbine7();

# load movieclip.mml:
$turbine->load("movieclip.mml");

# set a convenient native width and height:
$turbine->width(450);
$turbine->height(100);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>