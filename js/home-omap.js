

$(function(){
 
    
    where_autocomplete('where',function(rel){
        $('#geo').val(rel);
        proceed_prority=2;
        ga('send', 'pageview', '/?location');
        grid_start('autocompleted',2);
    },1.01,2);
    
    if (navigator.geolocation) {
        setTimeout(function(){
            navigator.geolocation.getCurrentPosition(function (pos) {
                $('#geo').val(pos.coords.latitude+','+pos.coords.longitude);
                $('#xlat').val(pos.coords.latitude);
                $('#xlng').val(pos.coords.longitude);
                $('#navigator_missing').attr('found',true).fadeOut();
                
                if (proceed_prority<=1) { 
                    proceed_prority=1;
                    if (typeof(ga)!='undefined') ga('send', 'pageview', '/?navigator');
                    grid_start('navigator',1);
                    
                    geocoder.geocode($('#geo').val(), function(results) {
                        //console.log(results);
                        if(results.length>0) $('#where').val(results[0].name);
                        
                    });    
                }
                
                
                
            });
        },3000);
    }
        
    
    
    
    
});



