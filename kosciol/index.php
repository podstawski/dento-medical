<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    $url=$_SERVER['REQUEST_URI'];
    $pos=strpos($url,'?');
    if ($pos) $url=substr($url,0,$pos);
    $_url=explode(',',$url);
    $id=end($_url);
    
    if ($id+0==0) return;
    
    $church=new churchModel($id);
    
    $title=$church->name;
    $description=$church->address;
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza,'.$church->address;
    $basedir='..';
    
?>
<html>
    
<head>
  <?php include __DIR__.'/../html/head.phtml';?>
  <script src="<?php echo $basedir;?>/js/church.js"></script>
  
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
</div>
  
  <div class="container">
    
    <div class="row">
      <div class="col-sm-6">

        <div id="churchCarousel" class="carousel slide">

          <!-- Carousel items -->
          <div class="carousel-inner">
            <div class="active item">
                
		<form id="upload" method="post" action="../rest/image" enctype="multipart/form-data">
                    <div id="drop">
                            <a>Dodaj zdjęcie</a>
                            <input type="file" name="upl" multiple xaccept="image/*" capture="camera"/>
                    </div>

                    <ul>
                            <!-- The file uploads will be shown here -->
                    </ul>

		</form>
                
                <img src="../img/dodaj.jpg"/>
                
            </div>
            
            <div class="item">
                <img src="../img/dodaj.jpg"/>
            </div>

          </div>
          <!-- Carousel nav -->
          <a class="carousel-control left" href="#churchCarousel" data-slide="prev">&lsaquo;</a>
          <a class="carousel-control right" href="#churchCarousel" data-slide="next">&rsaquo;</a>
        </div>        
        
      </div>
      
      <div class="col-sm-6">
        <h1><?php echo $church->name;?></h1>
        <h2><?php echo $church->address;?></h2>
        <?php if ($church->phone): ?>
            <h2>Tel.: <a href="tel:<?php echo $church->tel;?>"><?php echo $church->phone;?></a></h2>
        <?php endif; ?>
        <?php if ($church->rector): ?>
            <h3><b>Proboszcz:</b> <?php echo $church->rector; ?></h3>
        <?php endif; ?>
        <h3><b>Msze św:</b></h3>
        <?php if ($church->sun): ?>
            <h3><b>Niedziele i święta:</b> <?php echo $church->sun; ?></h3>
        <?php endif; ?>        
        <?php if ($church->week): ?>
            <h3><b>Dni powszednie:</b> <?php echo $church->week; ?></h3>
        <?php endif; ?>
        <?php if ($church->fest): ?>
            <h3><b>Święta zniesione:</b> <?php echo $church->fest; ?></h3>
        <?php endif; ?>
        
        <div class="church-map" title="<?php echo $church->name; ?>" lat="<?php echo $church->lat;?>" lng="<?php echo $church->lng;?>"></div>
      
        <a href="../edit/<?php echo $church->id; ?>" class="a_update">Aktualizuj dane</a>
      </div>
    </div>  
  
  </div>



</body>
</html>
