<?php
  $title='Gdzie i kiedy msza';
  $description='Łatwo i szybko znajdź najbliższy kościół i mszę';
  $url='http://www.kiedymsza.pl';
  
  $keywords='msza,msze,kiedy msza,gdzie msza,kościół';
  $basedir='.';
  $image=$basedir.'/img/fb-baner.jpg';
?>
<html>
    
<head>
  <?php include __DIR__.'/html/head.phtml';?>  
  
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.date.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.time.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/legacy.js"></script>  
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/translations/pl_PL.js"></script>
  
  <script src="<?php echo $basedir;?>/js/grid.js"></script>
  <script src="<?php echo $basedir;?>/js/home.js"></script>
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
          <input type="text" class="time" placeholder="godz" name="time"/>
      </div>
     
      <div class="col-sm-6">
        <input type="text" class="where" placeholder="gdzie jestem ..." id="where"/>
      
        <input id="navigator_missing" readonly value="Proszę wyraź zgodę na udostępnienie swojej lokalizacji" title="Proszę wyraź zgodę na udostępnienie swojej lokalizacji"/>
      </div>
    </div>  
  
  </div>
  </form>

  
</div>



<div id="kiedymsza_results" class="container-fluid"></div>
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
    </div>
    <div class="col-md-2 hidden-sm hidden-xs distance">[distance] km</div>
  </div>
</div>


<?php include __DIR__.'/html/footer.phtml';?> 
</body>
</html>
