<?php
  $title='Gdzie i kiedy msza św';
  $description='Katolik na urlopie - łatwo i szybko znajdź najbliższy kościół i mszę św';
  $url='http://www.kiedymsza.pl';
  
  $keywords='msza św,msze św,kiedy msza,gdzie msza,kościół, urlop, katolik';
  $basedir='.';
  $image=$basedir.'/img/fb-baner2.jpg';
  
  $results=[];

  include __DIR__.'/rest/https.php';
  include __DIR__.'/rest/library/backend/include/all.php';    
  autoload([__DIR__.'/rest/classes',__DIR__.'/rest/models',__DIR__.'/rest/controllers']);

  $config=json_config(__DIR__.'/rest/configs/application.json');
  $bootstrap = new Bootstrap($config);  
  
  $geo=Tools::geoip();
 
  
  if (false) if (isset($geo['location']['country']) && strtoupper($geo['location']['country'])!='PL') {
    
    $church=new churchModel();
    $page=isset($_GET['page'])?$_GET['page']:0;
    $results=$church->select(['active'=>1],'id',100,$page);
    $center=Bootstrap::$main->getConfig('pl.center');
    foreach ($results AS $i=>$row)
    {
      $results[$i]['url']=Tools::str_to_url($row['name']).','.$row['id'];
      $results[$i]['distance']=round($church->distance($row['lat'],$row['lng'],$center[0],$center[1]));
    }
  }
?>
<html lang="pl">
    
<head>
  <?php include __DIR__.'/html/head.phtml';?>  
  
  <link rel="stylesheet" href="<?php echo $basedir;?>/css/Control.Geocoder.css">
  
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.date.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.time.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/legacy.js"></script>  
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/translations/pl_PL.js"></script>
  
  <script src="<?php echo $basedir;?>/js/Control.Geocoder.js"></script>
  <script src="<?php echo $basedir;?>/js/grid.js"></script>
  <script src="<?php echo $basedir;?>/js/home.js?v=2"></script>
  <script src="<?php echo $basedir;?>/js/home-omap.js?v=2"></script>


</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/html/topmenu.phtml';?>
  
  <form id="kiedyMszaForm">
  <input name="geo" id="geo" type="hidden"/>
  <div class="container-fluid">
    
    <div class="row">
      <div class="col-sm-6">
        
          <input type="text" class="date" placeholder="kiedy msza św..." name="date"/>
          <input type="text" class="time" placeholder="od godz." name="time"/>
      </div>
     
      <div class="col-sm-6">
        <input type="text" class="where" placeholder="gdzie msza św..." id="where" autocomplete="nope"/>
        
        <input id="navigator_missing" readonly value="Proszę wyraź zgodę na udostępnienie swojej lokalizacji" title="Proszę wyraź zgodę na udostępnienie swojej lokalizacji"/>
      </div>
    </div>  
  
  </div>
  </form>
  

  
</div>


<div class="row" style="margin:0">
<div class="col-md-9 col-sm-12">

<table class="colors" cellspacing="0">
  <tr>
    <td class="distance0">0 km</td>
    <td class="distance1">1 km</td>
    <td class="distance2">2 km</td>
    <td class="distance3">3 km</td>
    <td class="distance4">4 km</td>
    <td class="distance5">5 km</td>
    <td class="distance6">6 km</td>
    <td class="distance7">7 km</td>
    <td class="distance8">8 km</td>
    <td class="distance9">9 km</td>
  </tr>
  
</table>

  <div id="kiedymsza_results" class="container-fluid">
  <center><h1>Msze święte w miejscowości blisko Ciebie</h1></center>
  <?php foreach($results AS $row): ?>
    <div class="row distance<?php echo rand(0,9);?>">
      <div class="col-md-2 col-sm-3 col-xs-3 time">
        <a href="tel:<?php echo $row['tel'];?>"><?php echo $row['tel'];?></a>
        <span class="visible-xs visible-sm"><?php echo $row['distance'];?> km</span>
        <span class="visible-md visible-lg">Dzisiaj</span>
      </div>
      <div class="col-md-8 col-sm-9 col-xs-9 church">
        <div class="mass-desc"></div>
        <h4><a href="kosciol/<?php echo $row['url'];?>"><?php echo $row['name'];?></a></h4>
        <span class="address"><?php echo $row['address'];?></span>
      </div>
      <div class="col-md-2 hidden-sm hidden-xs distance"><?php echo $row['distance'];?> km</div>
    </div>
  <?php endforeach;?>
  
  <?php if (count($results)) for($i=0;$i<101;$i++):?>
  <a href="?page=<?php echo $i+1;?>"><?php echo $i+1;?></a> 
  <?php endfor;?>
  

  
  </div>
</div>
<div class="col-md-3 hidden-sm hidden-xs">
<!-- Prawa szpalta -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:1050px"
     data-ad-client="ca-pub-3681218186493233"
     data-ad-slot="5079846536"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>



  
</div>
</div>
<div id="kiedymsza_results_template" style="display:none">
  <div class="row distance[distance]" style="display:none">
    <div class="col-md-2 col-sm-3 col-xs-3 time">
      [time]
      <span class="visible-xs visible-sm">[distance] km</span>
      <span class="visible-md visible-lg">[downame]</span>
    </div>
    <div class="col-md-8 col-sm-9 col-xs-9 church">
      <div class="mass-desc">[if:description] [description][endif:description][if:kids] dzieci[endif:kids][if:youth] młodzież[endif:youth]</div>
      <h4><a href="kosciol/[name_url],[church_id]">[name]</a></h4>
      <span class="address">[address]</span>
      [if:nomassthisday]
      <div class="nomass hidden-xs">
        Wygląda na to, że w [downame] nie ma tu Mszy Św.
        Jeżeli uważasz, że to błąd, to
        <a href="/edit/[church_id]">proszę popraw.</a>
      </div>
      [endif:nomassthisday]
    </div>
    <div class="col-md-2 hidden-sm hidden-xs distance">[distance] km</div>
  </div>
</div>



<?php include __DIR__.'/html/footer.phtml';?>
<!--  geo: <?php echo implode(',',$geo['location']);?>  -->
</body>
</html>
