
var heatmap,heatmapVisible=false;

var where_from_latlng=null;
var where_to_latlng=null;

var map, directionsService, directionsDisplay, markerarray=[];

var last_route_result=null;

var navigator_pos=null;

function clear_markers()
{
    for (var i = 0; i < markerarray.length; i++) {
        map.removeLayer(markerarray[i]);
    }
    markerarray=[];    
}


function draw_route()
{
    if (last_route_result!=null) {
        computeTotalDistance(last_route_result);
        return;
    }
    
    
    if (where_from_latlng!=null && where_to_latlng!=null) {
        clear_markers();
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

    var data=result.routes[0].legs[0];
    
    if (typeof(data.steps[0].lat_lngs[0].lat)=='function') for(var i=0;i<data.steps.length;i++)
    {
        var lat_lngs=[];
        for (var j=0; j<data.steps[i].lat_lngs.length; j++)
        {
            lat_lngs.push([data.steps[i].lat_lngs[j].lat(),data.steps[i].lat_lngs[j].lng()]);
        }
        data.steps[i].lat_lngs=lat_lngs;
    }
    
    last_route_result=result;
    
    var date=$('input[name="date_submit"]').val();
    if (date.length) data.date_submit=date;
    var time=$('input[name="time_submit"]').val();
    if (time.length) data.time_submit=time;
    
    var d = new Date();
    data.now = d.getHours()+':'+d.getMinutes();        
    
    
    $('#map_search').modal();
    $('#map_search .modal-footer').fadeIn(500);
    $('#map_search .modal-body').fadeOut(500);
    
    clear_markers();
    
    $.ajax({
        type: "POST",
        data: JSON.stringify(data),
        url: url,
        dataType: 'json',
        contentType: 'application/json; charset=UTF-8',
        success: function(result) {
            
            churches=result.churches;
            var labelIndex=1;
            for (var i in churches)
            {
                
                lat=churches[i].lat;
                lng=churches[i].lng;
                
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat,lng),
                    map: map,
                    label: String(labelIndex++),
                    time: churches[i].time,
                    icon: '../img/gmap_icon.png',
                    title: churches[i].time+' - '+churches[i].name,
                    name: churches[i].name,
                    address: churches[i].address,
                    wait: churches[i].time_difference,
                    url: '../kosciol/'+churches[i].name_url+','+churches[i].church_id
                });
                google.maps.event.addListener(marker, 'click', function() {
                    //window.location.href = this.url;
                    
                    var html='<div class="map_info_win"><h1>'+this.time+'</h1>';
                    html+='<p><a href="'+this.url+'" target="_blank">'+this.name+'</a></p>';
                    html+='<p>'+this.address+'</p>';
                    html+='<p>'+this.wait+' min. przed czasem</p>';
                    html+='</div>';
                    var infowindow = new google.maps.InfoWindow({
                        content: html,
                        maxWidth: 200,
                    });                     
                    
                    infowindow.open(map, this);
                });
                
                markerarray.push(marker);
            }
            
            
            $('#map_search .modal-footer').fadeOut(500, function () {
                $('#map_search .modal-body').show();
                $('#map_search').modal('hide');
            });
            
        }
    });
    
    
}

function search_map_my_position(pos) {
    var geocoder = new google.maps.Geocoder();
    where_from_latlng=new google.maps.LatLng(pos.coords.latitude,pos.coords.longitude);
    draw_route(false);
    geocoder.geocode( { 'address': pos.coords.latitude+','+pos.coords.longitude}, function(results, status) {
        
        if(status=='OK') $('#map_search #where_from').val(results[0].formatted_address);
        
    });
}


// view-source:https://leafletjs.com/examples/custom-icons/example.html

function initialize(lat,lng,zoom,here) {
    
    
    $('a.menu_szukaj').click(function () {
        $('#map_search').modal();
        if (!$('.navbar-toggle').hasClass('collapsed')) $('.navbar-toggle').trigger('click');
    });
    
    
    
    map = L.map('map-canvas');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);
    
    
    var idle = function(ev) {
        var bounds = map.getBounds();
        
        
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        var center=map.getCenter();
        
        
        
        var latlng=center.lat+','+center.lng+','+map.getZoom();
        
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
        
        
    
        var url=BASEDIR+'/mapa/index.php?lat1='+ne.lat+'&lng1='+ne.lng+'&lat2='+sw.lat+'&lng2='+sw.lng;
        


        

        if (where_from_latlng==null || where_to_latlng==null) $.get(url,function(churches) {
            
        
            clear_markers();
            

            if (churches.length==0 && !heatmapVisible) {
                setTimeout(function(){
                   heatmap.addTo(map); 
                },heatmap?1:2000);
                heatmapVisible=true;
            }
  
            if (churches.length>0 && heatmapVisible) {
                map.removeLayer(heatmap);
                heatmapVisible=false;
            }  
  
            for(var i=0;i<churches.length; i++)
            {
                lat=churches[i].lat;
                lng=churches[i].lng;
                
                var marker = L.marker([lat, lng], {
                    title: churches[i].name,
                    url: churches[i].url,
                    icon: L.icon({
                        iconUrl: '../img/gmap_icon.png',
                        iconSize: [23, 43],
                        iconAnchor: [11, 43]
                    })
                }).addTo(map).on('click',function(){
                    window.location.href = this.options.url;
                });
                
                
                markerarray.push(marker);
            }
            
        });
    }
    
    map.on('moveend', idle);
    map.setView([lat, lng], zoom);
    
    
    $.get(BASEDIR+'/mapa/heatmap',function(data) {
        heatmap = L.heatLayer(data);
    });
    
    
    return;
    
    directionsService = new google.maps.DirectionsService;
    directionsDisplay = new google.maps.DirectionsRenderer({
      draggable: true,
      map: map
    });
    
    
    directionsDisplay.addListener('directions_changed', function() {
        computeTotalDistance(directionsDisplay.getDirections());
    });
    
    
    

    
    followMe(map);
    
    markerarray=[];

    
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
            last_route_result=null;
        }
        
    });

    google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
        var place = autocomplete_to.getPlace();
        ga('send', 'pageview', '/?route');
        
        if (typeof(place.geometry.location)!='undefined') {
            where_to_latlng = place.geometry.location;
            last_route_result = null;
        }
        
    });
    
    
    $('#map_search .date').pickadate({
        onSet:function() {
            //draw_route(true);
        },
        format: 'dddd, dd mmm yyyy',
        selectYears: false,
    });

    $('#map_search .time').pickatime({
        onSet:function() {
            //draw_route(true);
        },        
        format: 'HH:i',
        formatSubmit: 'HH:i',
        min: [5,0],
        max: [18,0],
        interval: 60
    });
    
    $('#map_search .glyphicon').click(function(){
        
        if (navigator.geolocation && navigator_pos==null) {
            navigator.geolocation.getCurrentPosition(search_map_my_position);
        }
        
        if (navigator_pos!=null) {
            search_map_my_position(navigator_pos);
        }
    });
    
    $('#map_search .submit button').click(function () {
        setTimeout(draw_route,100);
    });
    
}



window.addEventListener("load", function(event) {
    
    
    if (navigator.geolocation && typeof(LATLNG)=='undefined') {
        navigator.geolocation.getCurrentPosition(function (pos) {
            
            navigator_pos=pos;
            initialize(pos.coords.latitude,pos.coords.longitude,14,true);
            
        });
        
    }
    
    if (typeof(LATLNG)!='undefined') {
        var pos=LATLNG.split(',');
        initialize(parseFloat(pos[0]),parseFloat(pos[1]),parseInt(pos[2]),false);
        
    }
    
   
    
});