<?php
    
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    if (strstr($_SERVER['REQUEST_URI'],'heatmap')) {
	Header('Content-type: application/json');
	$file='export/heatmap.json';
        $real_path=Tools::saveRoot($file);
	die(file_get_contents($real_path));
    }
    
    
    
    if (isset($_GET['lat1']) && isset($_GET['lat2']) && isset($_GET['lng1']) && isset($_GET['lng2'])) {
	if ($_GET['lat1']>$_GET['lat2']) {
	    $lat=$_GET['lat1'];
	    $_GET['lat1']=$_GET['lat2'];
	    $_GET['lat2']=$lat;
	}
    
	if ($_GET['lng1']>$_GET['lng2']) {
	    $lng=$_GET['lng1'];
	    $_GET['lng1']=$_GET['lng2'];
	    $_GET['lng2']=$lng;
	}
    
	$church=new churchModel();
	$churches=$church->map($_GET['lat1'],$_GET['lat2'],$_GET['lng1'],$_GET['lng2'],0,0,50)?:[];

	
	foreach ($churches AS &$ch)
	{
	    $ch['url']='../kosciol/'.Tools::str_to_url($ch['name']).','.$ch['id'];
	}
    
	Header('Content-type: application/json; charset=utf8');
	die(json_encode($churches,JSON_NUMERIC_CHECK));

    }
    
    $title='KiedyMsza - mapa';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza';
    $basedir='..';

    $imagePath=Tools::saveRoot('maps');
    $file='';
    $date2compare=isset($_GET['kiedy'])?strtotime($_GET['kiedy']):Bootstrap::$main->now;
    $imgDate=0;
    foreach(scandir($imagePath) AS $f)
    {
	$d=strtotime(str_replace('.jpg','',$f));
	
	if ($d<=$date2compare) {
	    $file="$imagePath/$f";
	    $imgDate=$d;
	}
    }
    if ($file && Bootstrap::$main->appengine) {
	$image=CloudStorageTools::getImageServingUrl($file,['size'=>0+Bootstrap::$main->getConfig('square_size'),'crop'=>true]);
	$description='Mapa - stan na '.date('d-m-Y',$d);
    }
?>
<html>
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>

    <?php
    
	if (isset($_GET['m'])) echo '<script>var LATLNG="'.$_GET['m'].'";</script>';    
    ?>
    <script src="<?php echo $basedir;?>/js/map.js"></script>
  
</head>

<body>

<div class="map">
    <div class="head">
	<?php include __DIR__.'/../html/topmenu.phtml';?>
    </div>
  
    <div id="map-canvas">
        <input id="navigator_missing" title="Proszę wyraź zgodę na udostępnienie swojej lokalizacji" class="button" readonly value="Proszę wyraź zgodę na udostępnienie swojej lokalizacji"/>
    </div>
    
    <div id="footer" style="display: none">Przybliż mapę, aby zobaczyć kościoły</div>
    
</div>
</body>
</html>
