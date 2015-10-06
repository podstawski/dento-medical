
var heatmap,heatmapVisible=false;

var where_from_latlng=null;
var where_to_latlng=null;

var map, directionsService, directionsDisplay, markerarray;

function draw_route()
{
    if (where_from_latlng!=null && where_to_latlng!=null) {
        directionsService.route({
            origin: where_from_latlng,
            destination: where_to_latlng,
            travelMode: google.maps.TravelMode.DRIVING,
            avoidTolls: true
          }, function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
            } else {
                console.log(status);
            }
          });
    }
}

function computeTotalDistance(result)
{
    var url='../rest/church/route';
    
    var d = new Date();
    result.routes[0].legs[0].now = d.getHours()+':'+d.getMinutes();
    var data=JSON.stringify(result.routes[0].legs[0]);
    
    
    $.ajax({
        type: "POST",
        data: data,
        url: url,
        dataType: 'json',
        contentType: 'application/json; charset=UTF-8',
        success: function(result) {
            console.log(result);
        }
    });
    
    
}

function clear_markers()
{
    for (var i = 0; i < markerarray.length; i++) {
        markerarray[i].setMap(null);
    }
    markerarray=[];    
}

function initialize(lat,lng,zoom,here) {
    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
        zoom: zoom,
        center: myLatlng
    }
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    
    
    directionsService = new google.maps.DirectionsService;
    directionsDisplay = new google.maps.DirectionsRenderer({
      draggable: true,
      map: map
    });
    
    
    directionsDisplay.addListener('directions_changed', function() {
        computeTotalDistance(directionsDisplay.getDirections());
    });
    
    
    $.get('heatmap',function(data) {
        var latlngData=[];
        for(i=0;i<data.length;i++) latlngData[latlngData.length] = new google.maps.LatLng(data[i].lat, data[i].lng);
        var pointArray = new google.maps.MVCArray(latlngData);
        heatmap = new google.maps.visualization.HeatmapLayer({
            data: pointArray
        });
    });

    
    followMe(map);
    
    markerarray=[];
        
    google.maps.event.addListener(map, 'idle', function(ev){
        var bounds = map.getBounds();
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        var center=map.getCenter();
        var latlng=center.lat()+','+center.lng()+','+map.getZoom();
        
        var lh=location.href;
        var pyt=lh.indexOf('?');
        if (pyt>0) lh=lh.substr(0,pyt);
        lh+='?m='+latlng
        history.pushState('', 'Mapa', lh);
        ga('send', 'pageview', lh);
        $('#where').val('');
        
        var add=lh.replace('/mapa/','/edit/0');
        if(!user_logged_id) add=REST+'/user/facebook?redirect='+encodeURIComponent(add);
        $('.a_mapadd').attr('href',add);

        if (map.getZoom()>=15) {
            $('.a_mapadd').fadeIn();
        } else {
            $('.a_mapadd').fadeOut();
        }
        
        var url='index.php?lat1='+ne.lat()+'&lng1='+ne.lng()+'&lat2='+sw.lat()+'&lng2='+sw.lng();
        

        if (where_from_latlng!=null && where_to_latlng!=null)
        {
            clear_markers();           
        }


        if (where_from_latlng==null || where_to_latlng==null) $.get(url,function(churches) {
            
            clear_markers();
            

            if (churches.length==0 && !heatmapVisible) {
                heatmap.setMap(map);
                heatmapVisible=true;
            }
  
            if (churches.length>0 && heatmapVisible) {
                heatmap.setMap(null);
                heatmapVisible=false;
            }  
  
            for(var i=0;i<churches.length; i++)
            {
                lat=churches[i].lat;
                lng=churches[i].lng;
                 
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat,lng),
                    map: map,
                    icon: '../img/gmap_icon.png',
                    title: churches[i].name,
                    url: churches[i].url
                });
                google.maps.event.addListener(marker, 'click', function() {
                    window.location.href = this.url;
                });
                

                
                markerarray.push(marker);
            }
            
        });
        
    });        


    var input = document.getElementById('where');
    var options = {
      types: ['geocode'],
      componentRestrictions: {country: 'pl'}
    };    
    autocomplete = new google.maps.places.Autocomplete(input,options);
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();

        ga('send', 'pageview', '/?location');
        
        if (typeof(place.geometry.location)!='undefined') {
            $('#map_search').modal('hide');
            map.panTo(place.geometry.location);
            map.setZoom(12);
            
            
        }
        
    }); 

    
    var where_from = document.getElementById('where_from');
    var where_to = document.getElementById('where_to');

    
    autocomplete_from = new google.maps.places.Autocomplete(where_from,options);
    autocomplete_to = new google.maps.places.Autocomplete(where_to,options);
    
    
    google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
        var place = autocomplete_from.getPlace();
        ga('send', 'pageview', '/?route');
        
        if (typeof(place.geometry.location)!='undefined') {
            where_from_latlng = place.geometry.location;
            draw_route();
        }
        
    });

    google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
        var place = autocomplete_to.getPlace();
        ga('send', 'pageview', '/?route');
        
        if (typeof(place.geometry.location)!='undefined') {
            where_to_latlng = place.geometry.location;
            draw_route();
        }
        
    }); 
}



google.maps.event.addDomListener(window, 'load', function() {
    
    
    if (navigator.geolocation && typeof(LATLNG)=='undefined') {
        navigator.geolocation.getCurrentPosition(function (pos) {
            
            initialize(pos.coords.latitude,pos.coords.longitude,14,true);
            
        });
        
    }
    
    if (typeof(LATLNG)!='undefined') {
        var pos=LATLNG.split(',');
        initialize(parseFloat(pos[0]),parseFloat(pos[1]),parseInt(pos[2]),false);
        
    }
    
   
    
});