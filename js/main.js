function login_fb()
{
    var url=REST+'/user/facebook?redirect='+encodeURIComponent(location.href);
    $('.a_login,#drop a').attr('href',url);
    
}

$(function() {
    $.get(REST+'/user',function(data) {
        if (data.status) {
            $('.a_login').html('Wyloguj').click(function(){
                $.get(REST+'/user/logout',function(data) {
                     $('.a_login').html('Login');
                    login_fb();
                });
                return false;
            });

            $('#drop a').click(function(){
                // Simulate a click on the file input button
                // to show the file browser dialog
                $(this).parent().find('input').click();
            });
            
            
        } else {
            login_fb();
        }
    });

});