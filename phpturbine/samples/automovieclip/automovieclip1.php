<?
# create the turbine object:
$turbine = new Turbine7();

# load DataSet from the buttons_dataset.txt file:
$turbine->loadDataSet("buttons_dataset.txt", "buttonsDataSet");

# include movieclip.mml:
$turbine->include("movieclip.mml");

# create an <AutoMovieClip> tag:
$turbine->create("<AutoMovieClip dataSet='buttonsDataSet' defaultId='button_movieclip_id' defaultDy='56'/>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>