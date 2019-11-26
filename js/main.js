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

var where_autocomplete_cb={};
var geocoder;
function where_autocomplete(id,cb,scaleW,scaleH) {
    if (Object.keys(where_autocomplete_cb).length==0) {
        geocoder = new L.Control.Geocoder.Nominatim();
        
        $(document).on('click', '#geo_search_results li', function(e){
            $('#geo_search_results').fadeOut();
            var id=$('#geo_search_results').attr('rel');
            $('#'+id).val($(this).text());
            
            
            if (typeof(where_autocomplete_cb[id])=='function') 
                where_autocomplete_cb[id]($(this).attr('rel'));
            
            
        });
    }

    where_autocomplete_cb[id]=cb;
    if ($('#geo_search_results').length==0) {
        $('body').append('<div id="geo_search_results"></div>');
    }
    
    var lastKeypress = 0;
    
    if (!scaleW) scaleW=1;
    if (!scaleH) scaleH=1;
    
    $('#'+id).on('input',function (e) {
        var self = $(this);
        
        setTimeout(function(){
            var selfVal = self.val().trim();
            var selfLength = selfVal.length;
            
            //console.log(proceed_prority,selfLength,e);
            
            
            if (selfLength===0) {
                if (typeof proceed_prority!=='undefined') {
                    proceed_prority=0;
                }
                return true;
            }
            
            if (typeof proceed_prority!=='undefined') {
                proceed_prority=2;
            }
            
            if (e.which != 13 && (lastKeypress==0 || Date.now()-lastKeypress<1000)) {
                lastKeypress = Date.now();
                return true;
            }
            
            self.addClass('searching');
            geocoder.geocode(selfVal, function(results) {
                self.removeClass('searching');
                console.log('searched',selfVal,results.length)
                if (results.length==0) {
                    return;
                }
                
                $('#geo_search_results').fadeIn().width(self.width()*scaleW).css({
                    left: self.offset().left,
                    top:self.offset().top + self.height()*scaleH
                }).attr('rel',id);
                
                var html='<ul>';
                for (var i=0;i<results.length; i++){
                    html+='<li rel="'+results[i].center.lat+','+results[i].center.lng+'">';
                    html+=results[i].name;
                    html+='</li>';
                }
                html+='</ul>';
                
                $('#geo_search_results').html(html);
    
            });
            
            return true;
        },250,e);
    });
    
}

