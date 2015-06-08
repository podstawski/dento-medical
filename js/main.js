function login_fb()
{
    var url=REST+'/user/facebook?redirect='+encodeURIComponent(location.href);
    $('.a_login,#drop a').attr('href',url);
    
    $('#drop a').text('Zaloguj się, aby dodać zdjęcie');
    
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

var iamhere;
function followMe(map) {
    if (navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(function (pos) {
    
            if (typeof(map)=='object') {
        
                iamhere = new google.maps.Marker({
                    position: new google.maps.LatLng(pos.coords.latitude,pos.coords.longitude),
                    map: map,
                    icon: '../img/iamhere.png',
                    title: 'Tu jestem'
                });
                
                google.maps.event.addListener(iamhere, 'click', function() {
                    this.getMap().setZoom(15);
                    this.getMap().setCenter(new google.maps.LatLng(this.getPosition().lat(),this.getPosition().lng()));
                    
                });
            } else {
                iamhere.setPosition( new google.maps.LatLng( pos.coords.latitude,pos.coords.longitude ) );
            }
            setTimeout(followMe,15000);
        });
        
    }  
}
