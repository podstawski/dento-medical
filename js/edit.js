
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
            icon: '../img/gmap_icon.png',
            draggable:true
        });
        
        followMe(map);
        
        google.maps.event.addListener(map, 'click', function (event) {
        
          $('#lat').val(event.latLng.lat());
          $('#lng').val(event.latLng.lng());
          
          marker.setPosition( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
          //map.panTo( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );

        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
        
          $('#lat').val(event.latLng.lat());
          $('#lng').val(event.latLng.lng());
          
          marker.setPosition( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );

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
  
  $('.table .time input').focusout(function(event){
    if (typeof($(this).attr('out'))!='undefined') return;
    
    $(this).attr('out','1');
    
    $(this).parent().parent().attr('time',$(this).val());
    
    if ($(this).val().length==0) {
      var next=$(this).parent().parent().next();
      if (next.length>0) {
        next.remove();
      }
    } else {
      $(this).parent().parent().find('input.month,input.chkall').prop('checked',true);
      
      if ($(this).attr('name').indexOf('masses[1]')>=0)
      {
        for (var day=2;day<=6;day++) {
          var tr=$(this).parent().parent().clone();
          tr.find('input').each(function(){
            name=$(this).attr('name')+'';
            if (typeof(name)!='undefined') {
              name=name.replace('masses[1]','masses['+day+']').replace('_new_','_new_'+day);
              $(this).attr('name',name);
              //console.log(name);            
            }
          });
          //$('.new-mass-'+day).insertBefore(tr);
          tr.insertBefore('.new-mass-'+day);
        }
        table_manimupation();
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
    $(this).parent().parent().find('input.month').prop('checked',ch);
    
    var tr=$(this).closest('tr');
    if (tr.find('input.month').first().attr('name').indexOf('masses[1]')==-1) return;
    
    $('.table tr[time="'+tr.attr('time')+'"] input.month').each(function(){
      if ($(this).attr('name').indexOf('masses[0]')>=0) return;
      if ($(this).attr('name').indexOf('masses[1]')>=0) return;
      if ($(this).attr('name').indexOf('masses[8]')>=0) return;
      $(this).prop('checked',ch);
    });
  });
  
  $('.table input.desc').focus(function(){
    $(this).parent().nextAll().hide();
    $(this).parent().attr('colspan','14');
    $(this).css('width','150px');
    $(this).parent().find('span').fadeIn(500);
    
  });
  
  //monday repeat desc
  $('.table input.desc').focusout(function(){
    if ($(this).attr('name').indexOf('masses[1]')==-1) return;
    var tr=$(this).closest('tr');
    var v=$(this).val();
    
   
    $('.table tr[time="'+tr.attr('time')+'"] input.desc').each(function(){
      if ($(this).attr('name').indexOf('masses[0]')>=0) return;
      if ($(this).attr('name').indexOf('masses[1]')>=0) return;
      if ($(this).attr('name').indexOf('masses[8]')>=0) return;
      $(this).val(v);
    });
  });
  
  //monday repeat kids
  $('.table input.kids,.table input.youth').click(function(){
    if ($(this).attr('name').indexOf('masses[1]')==-1) return;
    var tr=$(this).closest('tr');
    var ch=$(this).prop('checked');
    var cl=$(this).attr('class');
    
    $('.table tr[time="'+tr.attr('time')+'"] input.'+cl).each(function(){
      if ($(this).attr('name').indexOf('masses[0]')>=0) return;
      if ($(this).attr('name').indexOf('masses[1]')>=0) return;
      if ($(this).attr('name').indexOf('masses[8]')>=0) return;
      $(this).prop('checked',ch);
    });    

  });
  
  $('.table .desc span a').click(function(){
    $(this).parent().hide();
    $(this).parent().parent().find('input').css('width','');
    $(this).parent().parent().nextAll().fadeIn(300);
    $(this).parent().parent().attr('colspan','');
  });
  
  
  //monday repeat masses
  $('.table input.mass').click(function(){
    if ($(this).attr('name').indexOf('masses[1]')==-1) return;
    
    var tr=$(this).closest('tr');
    var ch=$(this).prop('checked');
    var cl=$(this).parent().attr('class').replace(' ','.');
    
    $('.table tr[time="'+tr.attr('time')+'"] td.'+cl+' input.mass').each(function(){
      if ($(this).attr('name').indexOf('masses[0]')>=0) return;
      if ($(this).attr('name').indexOf('masses[1]')>=0) return;
      if ($(this).attr('name').indexOf('masses[8]')>=0) return;
      $(this).prop('checked',ch);
    }); 
    
  });
  
  $('table.table').each(function(){
    if ($(this).width()>$(window).width()) {
      $(this).css({
        zoom: 0.92*$(window).width()/$(this).width()
      });
    }
    
  });
  
}

var hb = function() {
  $.get(REST+'/church/hb',function () {
    setTimeout(hb,30*1000);
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


  
  $('button.save').click(function() {
    var pass=true;
    $('input[required="true"]').each(function(){
      if (pass && $(this).val().trim().length==0) {
        pass=false;
        alert('Pole '+$(this).attr('title')+' nie może być puste');
        this.focus();
        
      }
    });
  
    if (!pass) return;
    var data=$('#churchForm').serialize();
    $.post(REST+'/church',data,function (resp) {
      alert(resp.church.info);
      if (resp.church.url.length) {
        location.href='../kosciol/'+resp.church.url;
      }
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

  hb();
  
});