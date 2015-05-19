<?php
  $title='Gdzie i kiedy msza';
  $description='Łatwo i szybko znajdź najbliższy kościół i mszę';
  $url='http://www.kiedymsza.pl';
  $image='';
  $keywords='msza,msze,kiedy msza,gdzie msza,kościół';
  $basedir='.';
?>
<html>
    
<head>
  <?php include __DIR__.'/html/head.phtml';?>  
  
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/picker.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/picker.date.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/picker.time.js"></script>
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/legacy.js"></script>  
  <script src="<?php echo $basedir;?>/js/pickadate.js/lib/translations/pl_PL.js"></script>
  <script src="<?php echo $basedir;?>/js/grid.js"></script>
  <script src="<?php echo $basedir;?>/js/home.js"></script>
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/html/topmenu.phtml';?>
  
  <form id="kiedyMszaForm">
  <div class="container">
    
    <div class="row">
      <div class="col-sm-6">
        
          <input name="geo" id="geo" type="hidden"/>
          <input type="text" class="date" placeholder="kiedy msza ..." name="date"/>
        
      </div>
      
      <div class="col-sm-6">
        <input type="text" class="where" placeholder="gdzie jestem ..." id="where"/>
      
        <input id="navigator_missing" readonly value="Proszę wyraź zgodę na udostępnienie swojej lokalizacji"/>
      </div>
    </div>  
  
  </div>
  </form>

  
</div>



<div id="kiedymsza_results" class="container"></div>
<div id="kiedymsza_results_template" style="display:none">
  <div class="row">
    <div class="col-sm-2 time">[time]</div>
    <div class="col-sm-8 church">
      <h4><a href="kosciol/[name_url],[church_id]">[name]</a></h4>
      <span class="address">[address]</span>
    </div>
    <div class="col-sm-2 distance">[distance] km</div>
  </div>
</div>


</body>
</html>