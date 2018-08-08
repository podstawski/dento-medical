
// wdrozyc: http://www.liedman.net/leaflet-routing-machine/

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


function getInstrGeoJson(instr,coord) {
  var formatter = new L.Routing.Formatter();
  var instrPts = {
    type: "FeatureCollection",
    features: []
  };
  for (var i = 0; i < instr.length; ++i) {
    var g = {
      "type": "Point",
      "coordinates": [coord[instr[i].index].lat, coord[instr[i].index].lng]
    };
    var p = {
      "instruction": formatter.formatInstruction(instr[i])
    };
    instrPts.features.push({
      "geometry": g,
      "time": instr[i].time,
      "distance": instr[i].distance
    });
  }
  return instrPts;
}

function draw_route()
{
    if (last_route_result!=null) {
        computeTotalDistance(last_route_result);
        return;
    }
    
    
    if (where_from_latlng!=null && where_to_latlng!=null) {
        clear_markers();
        
        var router=L.Routing.control({
            waypoints: [
                where_from_latlng.split(','),
                where_to_latlng.split(',')
            ],
            routeWhileDragging: true,
            router: L.Routing.mapbox('pk.eyJ1Ijoia2llZHltc3phIiwiYSI6ImNqa2thNHZ4djE2M3kzcHAwZnV4bjc2dzMifQ.CXfAJ42HVG8Z5QWEMO3xGg')
        }).addTo(map).on('routesfound',function(e){
            if (e.routes && e.routes[0] && e.routes[0].instructions && e.routes[0].coordinates) {
                
                computeTotalDistance(getInstrGeoJson(e.routes[0].instructions,e.routes[0].coordinates).features);
            }
        });
        
        
    }
}

function computeTotalDistance(result)
{
    var url='../rest/church/route';

    var data={steps:result};
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
                
                
                var marker = L.marker([lat, lng], {
                    title: churches[i].name,
                    url: churches[i].url,
                    icon: L.icon({
                        iconUrl: '../img/gmap_icon.png',
                        iconSize: [23, 43],
                        iconAnchor: [11, 43]
                    })
                }).addTo(map);
                
                var html='<div class="map_info_win"><h1>'+churches[i].time+'</h1>';
                    html+='<p><a href="'+'../kosciol/'+churches[i].name_url+','+churches[i].church_id+'" target="_blank">'+churches[i].name+'</a></p>';
                    html+='<p>'+churches[i].address+'</p>';
                    html+='<p>'+churches[i].time_difference+' min. przed czasem</p>';
                    html+='</div>';
                    
                marker.bindPopup(html);
                marker.on('click',function(){
                    this.openPopup();
                });
                
                
                markerarray.push(marker);
                
                /*
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat,lng),
                    map: map,
                    label: String(labelIndex++),
                    time: ,
                    icon: '../img/gmap_icon.png',
                    title: churches[i].time+' - '+churches[i].name,
                    name: churches[i].name,
                    address: churches[i].address,
                    wait: churches[i].time_difference,
                    url: '../kosciol/'+churches[i].name_url+','+churches[i].church_id
                });
                google.maps.event.addListener(marker, 'click', function() {
                    //window.location.href = this.url;
                    
                    
                    var infowindow = new google.maps.InfoWindow({
                        content: html,
                        maxWidth: 200,
                    });                     
                    
                    infowindow.open(map, this);
                });
                
                */
            }
            
            
            $('#map_search .modal-footer').fadeOut(500, function () {
                $('#map_search .modal-body').show();
                $('#map_search').modal('hide');
            });
            
        }
    });
    
    
}

function search_map_my_position(pos) {
    
    where_from_latlng=pos.coords.latitude+','+pos.coords.longitude;
    draw_route(false);
    
    geocoder.geocode(pos.coords.latitude+','+pos.coords.longitude, function(results) {
        if(results.length>0) $('#map_search #where_from').val(results[0].name);
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
    
    
    followMe(map);
    
    where_autocomplete('where',function(rel){
        ga('send', 'pageview', '/?location');
        map.setZoom(12);
        map.panTo(rel.split(','));
        $('#map_search').modal('hide');
    },0.992,1.5);
    
    
    where_autocomplete('where_from',function(rel){
        ga('send', 'pageview', '/?route');
        where_from_latlng = rel;
        last_route_result=null;
    },0.98,1.5);
    
    where_autocomplete('where_to',function(rel){
        ga('send', 'pageview', '/?route');
        where_to_latlng = rel;
        last_route_result=null;
    },0.98,1.5);
        
    markerarray=[];
    
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