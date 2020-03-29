<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='KiedyMsza św - lista Mszy ONLINE';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza, online';
    $basedir='..';
	
	if (isset($_GET['v'])) {
		$iframeSrc="https://www.youtube.com/embed/".$_GET['v']."?autoplay=1";
  
	} else {
		$iframeSrc='https://calendar.google.com/calendar/embed?height=500&amp;wkst=1&amp;bgcolor=%23C0CA33&amp;ctz=Europe%2FWarsaw&amp;src=cmVzZWxsZXIud2Via2FtZWxlb24uY29tX3V2N2psYzNlMzdxZjJyY2wzYm9vdmc4cmU4QGdyb3VwLmNhbGVuZGFyLmdvb2dsZS5jb20&amp;color=%233F51B5&amp;showTz=0&amp;showCalendars=0&amp;showPrint=0&amp;showTitle=0&amp;mode=AGENDA';
	}

    
?>
<html lang="pl">
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
  
  
</div>
  
  
  <div class="container about">
    
	<iframe src="<?php echo $iframeSrc;?>" style="border-width:0" width="100%" height="100%" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
