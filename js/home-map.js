

$(function(){
 
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
            
            var geocoder = new google.maps.Geocoder();
            
            geocoder.geocode( { 'address': $('#geo').val()}, function(results, status) {
                //console.log(results);
                if(status=='OK') $('#where').val(results[0].formatted_address);
                
            });
            
        });
    }
    
    
    var input = document.getElementById('where');
    var options = {
      types: ['geocode'],
      componentRestrictions: {country: 'pl'}
    };    
    autocomplete = new google.maps.places.Autocomplete(input,options);
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        $('#geo').val(place.geometry.location.lat()+','+place.geometry.location.lng());

        proceed_prority=2;
        ga('send', 'pageview', '/?location');
        grid_start('autocompleted',2);
    });

});



