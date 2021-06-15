  <!-- Info Popup -->

            $(function() {
            	
                $('#activator').click(function(){
                		$('#box').show();
                		$('.info_small').hide();
                		$('.info_close').show();
                		$('#overlay').fadeIn('slow');
                    	$('#box').animate({'top':'60px'},500,function(){
                    });
                });
                $('#boxclose').click(function(){
                	$('.info_small').show();
                	$('.info_close').hide();
                    $('#overlay').fadeOut('fast');	
                    $(".info_small:hover").css("opacity", "1");
                    $('#box').animate({'top':'-300px'},500,function(){
                        $("#box").hide();
                    });
                });
				$('#activator2').click(function(){
					$('.info_small').show();
                	$('.info_close').hide();
                	$('#overlay').fadeOut('fast');	
                	$(".info_small:hover").css("opacity", "1");
                    $('#box').animate({'top':'-300px'},500,function(){
                    $("#box").hide();
                    });
                });
            });
  
       $(function() {
       			$('#overlay').click(function(){
				$('.info_small').show();
            	$('.info_close').hide();
            	$('#overlay').fadeOut('fast');	
            	$(".info_small:hover").css("opacity", "1");
                $('#box').animate({'top':'-300px'},500,function(){
                $("#box").hide();
                });
            });
    	});
       
       
            
           
$(document).ready(function(){

if ((navigator.userAgent.match(/iPhone/)) || (navigator.userAgent.match(/iPod/))) {
  $('#hint').hide();  
  $(".info_small:hover").css("opacity", "1");
}

if (navigator.userAgent.match(/Android/)) {
  $('#hint').hide();
  $(".info_small:hover").css("opacity", "1"); 
}

window.onresize = function(event) {
 	$('#hint').delay( 800 ).fadeOut(),100;
};

});

