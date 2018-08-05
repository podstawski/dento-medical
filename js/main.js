function login_fb()
{
    var url=REST+'/user/facebook?redirect='+encodeURIComponent(location.href);
    $('.a_login,#drop a').addClass('notlogged').attr('href',url);
    
    $('.carousel-inner #drop a').text('Zaloguj się, aby dodać zdjęcie');
    
    $('.a_update').each(function(){
        url=REST+'/user/facebook?redirect='+encodeURIComponent(location.href+'/../'+$(this).attr('href'));
        $(this).attr('href',url);
    });

    
}

var user_logged_id=false;

(function() {
    var cx = '004362178973919568600:w_jsfptrqhk';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
})();


$(function() {
    
    
    $.get(REST+'/user',function(data) {
        if (data.status) {
            user_logged_id=true;
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
    
    if (navigator.userAgent.search("Windows Phone") >= 0) {
        navireq='ms.jpg';
    } else if (navigator.userAgent.search("IEMobile") >= 0) {
        navireq='ms.jpg';
    } else if (navigator.userAgent.search("iPhone") >= 0) {
        navireq='iphone.jpg';
    } else if (navigator.userAgent.search("Android") >= 0) {
        navireq='android.jpg';
    } else if (navigator.userAgent.search("Chrome") >= 0) {
        navireq='chrome.jpg';
    } else if (navigator.userAgent.search("MSIE") >= 0) {
        navireq='ie.jpg';
    } else if (navigator.userAgent.search("Firefox") >= 0) {
        navireq='ff.jpg';
    } else if (navigator.userAgent.search("Windows") >= 0) {
        navireq='ie.jpg';
    }
    
    if (navireq.length>0) {
        $('#navigator_missing').addClass('fancybox').attr('href',BASEDIR+'/img/'+navireq);
    }
    
    $(".fancybox").fancybox();
    
    setTimeout(navigator_request_blinker,2000);
});


function navigator_request_blinker() {
    $('#navigator_missing').fadeOut(1000, function() {
        if ($('#navigator_missing').attr('found')) return;
        $('#navigator_missing').fadeIn(1000,function() {
            setTimeout(navigator_request_blinker,3000);
        });
    });
}
