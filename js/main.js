function login_fb()
{
    var url=REST+'/user/facebook?redirect='+encodeURIComponent(location.href);
    $('.a_login,#drop a').attr('href',url);
    
    $('.a_update').each(function(){
        url=url=REST+'/user/facebook?redirect='+encodeURIComponent(location.href+'/../'+$(this).attr('href'));
        $(this).attr('href',url);
    });
    
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
                
                if ($(this).attr('rel').length==0) return;
                
                $.get(REST+'/image/'+$(this).attr('rel'),function(data){
                    $('#upload').attr('action',data.url);
                });
                
                $(this).parent().find('input').click();
            });
            
            
        } else {
            login_fb();
        }
    });

    var navireq='chrome.jpg';
    
    if (navigator.userAgent.search("Android") >= 0) {
        navireq='android.jpg';
    } else if (navigator.userAgent.search("Chrome") >= 0) {
        navireq='chrome.jpg';
    } else if (navigator.userAgent.search("MSIE") >= 0) {
        navireq='ie.jpg';
    } else if (navigator.userAgent.search("Firefox") >= 0) {
        navireq='ff.jpg';
    }
    
    if (navireq.length>0) {
        $('#navigator_missing').addClass('fancybox').attr('href',BASEDIR+'/img/'+navireq);
    }
    
    $(".fancybox").fancybox();
    
    //document.write(navigator.userAgent);
});


