<?
# create the turbine object:
$turbine = new Turbine7();

# enable debug Turbine window:
$turbine->debug(true);

# log everything: errors [e] warnings [w] and informations [i]:
$turbine->log("errors, warnings, info");

# this will generate an error:
$turbine->create("<image src='image_not_found.gif'>");

# this will generate a warning:
$turbine->create("<text face='missing for sure' pos='10,220'>This text will produce a warning</text>");

# log directly to the log window using the global function turbineDebug():
$turbine->script("turbineDebug('logging from script');");
$turbine->script("turbineDebug('this is a debug text');");
$turbine->script("turbineDebug('this is another debug text');");

# browsers should not cache this request:
header ("Expires: Sat, 01 Jan 2000 01:01:01 GMT");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>