<?
# create the turbine object:
$turbine = new Turbine7();

# include the component definition file:
$turbine->include("flistboxsymbol.swf");

# place the component and pass a value to the labels argument:
$turbine->create("<Place component='ListBox' pos='10,10' instance='lst'><param name='labels' value=\"['first', 'second', 'one more']\"/></place>");

# set a convenient native height:
$turbine->height(200);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>