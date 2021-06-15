<?
# create the turbine object:
$turbine = new Turbine7();

# include the component definition files:
$turbine->include("flistboxsymbol.swf");
$turbine->include("checkbox.swf");
$turbine->include("combobox.swf");
$turbine->include("draggablepane.swf");

# read some data:
$turbine->loadDataSet("dataset.txt", "data", "row", "value");

# place a Listbox:
$turbine->create("<Text pos='10,30' size='10'>First instance of ListBox</Text>");
$turbine->create("<Place component='ListBox' pos='10,50' instance='lst'/>");

# place another Listbox:
$turbine->create("<Text pos='150,30' size='10'>Second instance of ListBox</Text>");
$turbine->create("<Place component='ListBox' pos='150,50' instance='lst_veg'/>");

# fill it with data:
$turbine->injectData("data", "lst_veg");

# place a CheckBox:
$turbine->create("<text pos='100,180' size='10'>Instance of CheckBox</Text>");
$turbine->create("<place component='CheckBox' pos='10,180'/>");

# place a ComboBox and inject some data:
$turbine->create("<Text pos='130,210' size='10'>Instance of ComboBox</Text>");
$turbine->create("<Place component='ComboBox' pos='10,210' instance='combo'/>");
$turbine->injectData("data", "combo");

# place a DraggablePane:
$turbine->create("<Place component='DraggablePane' pos='290,50' size='240,175' alpha='70'><Param name='Pane Title' value='A Custom Title'/></Place>");

# now generate the movie to the web browser:
$turbine->generateFlash();

?>