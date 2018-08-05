

$(function(){
 
    var geocoder = new L.Control.Geocoder.Nominatim();
 
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (pos) {
            $('#geo').val(pos.coords.latitude+','+pos.coords.longitude);
            $('#xlat').val(pos.coords.latitude);
            $('#xlng').val(pos.coords.longitude);
            $('#navigator_missing').attr('found',true).fadeOut();
            
            if (proceed_prority<=1) { 
                proceed_prority=1;
                if (typeof(ga)!='undefined') ga('send', 'pageview', '/?navigator');
                grid_start('navigator',1);
            }
            
            geocoder.geocode($('#geo').val(), function(results) {
                //console.log(results);
                if(results.length>0) $('#where').val(results[0].name);
                
            });
            
        });
    }
    

    
    
    
    var lastKeypress = 0;
    
    $('#where').keypress(function (e) {
        var self = $(this);
        if (self.val().trim().length==0) return true;
        
        if (e.which != 13 && (lastKeypress==0 || Date.now()-lastKeypress<1000) ) {
            lastKeypress = Date.now();
            return true;
        }
        
        self.addClass('searching');
        geocoder.geocode(self.val().trim(), function(results) {
            self.removeClass('searching');
            
            if (results.length==0) {
                //code      
                
                return;
            }
            
            $('#geo_search_results').fadeIn().width(self.width()*1.01).css({
                left: self.offset().left,
                top:self.offset().top + self.height()/0.5
            });
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
    });
    
    $(document).on('click', '#geo_search_results li', function(e){
        $('#geo_search_results').fadeOut();
        $('#where').val($(this).text());
        $('#geo').val($(this).attr('rel'));
        
        proceed_prority=2;
        ga('send', 'pageview', '/?location');
        grid_start('autocompleted',2);
    });
    
    
    
});



