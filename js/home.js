





$(function(){
    $('.date').pickadate({
        onSet:grid_start,
        format: 'dddd, dd mmm yyyy',
        selectYears: false,
    });

    setTimeout(grid_start,400);
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (pos) {
            $('#geo').val(pos.coords.latitude+','+pos.coords.longitude);
            $('#navigator_missing').fadeOut();
            grid_start();
            
            var geocoder = new google.maps.Geocoder();
            
            geocoder.geocode( { 'address': $('#geo').val()}, function(results, status) {
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
        grid_start();
    });
    
    
    
});



