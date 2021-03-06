jQuery(function($) {
    // These first three lines of code compensate for Javascript being turned on and off. 
    // It simply changes the submit input field from a type of "submit" to a type of "button".

    var paraTag = $('input#submit').parent('div');
    $(paraTag).children('input').remove();
    $(paraTag).append('<input type="button" name="submit" id="submit" value="Send message">');

    $('#contact-form input#submit').click(function() {
        $('#contact-form').append('<img src="images/loader.gif" class="loaderIcon" alt="Loading..." />');

        var name = $('input#name').val();
        var email = $('input#email').val();
        var subject = $('input#subject').val();
        var comments = $('textarea#comments').val();

        $.ajax({
            type: 'post',
            url: 'sendEmail.php',
            data: 'name=' + name + '&email=' + email + '&subject=' + subject + '&comments=' + comments,

            success: function(results) {
                $('#contact-form img.loaderIcon').fadeOut(1000);
                $('#response').html(results);
            }
        }); // end ajax
    });
});
		