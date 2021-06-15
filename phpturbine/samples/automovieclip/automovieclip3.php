<?
# create the turbine object:
$turbine = new Turbine7();

# load DataSet from the buttons_dataset.txt file:
$turbine->loadDataSet("buttons_dataset.txt", "buttonsDataSet");

# include movieclip.mml:
$turbine->include("movieclip.mml");

# create an <AutoMovieClip> tag:
$turbine->create("<AutoMovieClip dataSet='buttonsDataSet' defaultId='button_movieclip_id' cols='4' defaultDy='56' defaultDx='227' />");

# set a convenient native width and height:
$turbine->width(550);
$turbine->height(250);

# now generate the movie to the web browser:
$turbine->generateFlash();

?>