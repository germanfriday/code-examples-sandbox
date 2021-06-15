<?
# create the turbine object:
$turbine = new Turbine7();

# browsers should not cache this request:
header ("Expires: Sat, 01 Jan 2000 01:01:01 GMT");

# cache for 1 minute from now, if there was one at the cache serve and exit:
$turbine->cache("++100", "flash");

# sleep for a while, just to take some time, in case it was not served from the cache:
sleep(4);

# load cache.swf to serve as template:
$turbine->load("cache.swf");

# now generate the movie to the web browser:
$turbine->generate("flash");

?>