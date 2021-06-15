jQuery(document).ready(
function($)
{

$('#slideShow').nivoSlider({
		effect:'fade', //Specify sets like: 'fold,fade,sliceDown'
		slices:15,
		animSpeed:400,
		pauseTime:3500,
		directionNav:false, //Next & Prev
		directionNavHide:true, //Only show on hover
		controlNav:false, //1,2,3...
		pauseOnHover:true, //Stop animation while hovering
		slideshowEnd: function(){} //Triggers after all slides have been shown
	});

});