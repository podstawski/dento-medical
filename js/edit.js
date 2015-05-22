
$('.carousel').carousel();

var marker;
var map;

google.maps.event.addDomListener(window, 'load', function(){

    $('.church-map').each(function() {

        var myLatlng = new google.maps.LatLng($(this).attr('lat'),$(this).attr('lng'));
        
        var mapOptions = {
          zoom: 16,
          center: myLatlng
        }
        map = new google.maps.Map(this, mapOptions);
        
        marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: $(this).attr('title'),
            icon: '../img/gmap_icon.png'
        });
        
        google.maps.event.addListener(map, 'click', function (event) {
        
          $('#lat').val(event.latLng.lat());
          $('#lng').val(event.latLng.lng());
          
          marker.setPosition( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
          //map.panTo( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );

        });
        
    });

});

function table_manimupation()
{
  $('.table .time input').focus(function(){
    if ($(this).val().length==0) {
      if ($(this).parent().parent().next().length==0) {
        var tr=$(this).parent().parent().clone();
        tr.find('input').each(function(){
          var n=$(this).attr('name');
          n=n+'';
          n=n.replace('_new_','_new__');;

          $(this).attr('name',n);
        });
        $(this).parent().parent().parent().append(tr);
        table_manimupation();
      }
    }
  });
  
  $('.table .time input').focusout(function(){
    if ($(this).val().length==0) {
      var next=$(this).parent().parent().next();
      if (next.length>0) {
        next.remove();
      }
    }
  });

  $('.table td.rm').click(function(){
    $(this).parent().fadeOut(1000, function(){
      $(this).remove();
    });
  });
  
  $('.table input.chkall').click(function(){
    var ch=$(this).prop('checked');
    $(this).parent().parent().find('input[type="checkbox"]').prop('checked',ch);
  });
  
  $('.table input.desc').focus(function(){
    $(this).parent().nextAll().hide();
    $(this).parent().attr('colspan','14');
    $(this).css('width','150px');
    $(this).parent().find('span').fadeIn(500);
    
  });
  
  $('.table .desc span a').click(function(){
    $(this).parent().hide();
    $(this).parent().parent().find('input').css('width','');
    $(this).parent().parent().nextAll().fadeIn(300);
    $(this).parent().parent().attr('colspan','');
  });
}


$(function() {
  
  $('#iamhere').click(function(){
   
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (pos) {
          $('#lat').val(pos.coords.latitude);
          $('#lng').val(pos.coords.longitude);
          
          marker.setPosition( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
          map.panTo( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
      });
    }
  
  });


  
  $('button.save').click(function(){
    var data=$('#churchForm').serialize();
    $.post(REST+'/church',data,function (resp) {
      alert(resp.church);
    });
  });
  
  
  $('input[name="sun"]').change(function() {
    $('h6.sun').text($(this).val());
  });
  $('input[name="fest"]').change(function() {
    $('h6.fest').text($(this).val());
  });
  $('input[name="week"]').change(function() {
    $('h6.week').text($(this).val());
  });
  

  table_manimupation(); 
});