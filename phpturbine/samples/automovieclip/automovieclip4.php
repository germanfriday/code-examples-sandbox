<?
# create the turbine object:
$turbine = new Turbine7();

# load DataSet from the buttons_dataset.txt file:
$turbine->loadDataSet("buttons_dataset.txt", "buttonsDataSet");

# include movieclip.mml:
$turbine->include("movieclip.mml");

# create an <AutoMovieClip> tag:
$turbine->create("<AutoMovieClip id='automovieclip_buttons' dataSet='buttonsDataSet' defaultId='button_movieclip_id' cols='4' defaultDy='56' defaultDx='227' flags='define' />");

# include the ScrollPane component:
$turbine->include("scrollpane.swf");

# place a ScrollPane component referring to the automovieclip_buttons id we created above:
$turbine->create("<Place component='ScrollPane' pos='0,45' size='440,140'><Param name='Scroll Content' value='automovieclip_buttons'/></Place>");

# set a convenient native width and height:
$turbine->width(550);
$turbine->height(250);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>