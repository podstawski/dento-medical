<?php
    use google\appengine\api\cloud_storage\CloudStorageTools;
    
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
		$churches=$church->map($_GET['lat1'],$_GET['lat2'],$_GET['lng1'],$_GET['lng2'],0,0,100)?:[];
	
		
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
	
	
	$url=$_SERVER['REQUEST_URI'];
    $pos=strpos($url,'?');
    if ($pos) $url=substr($url,0,$pos);
    $_url=explode(',',$url);
    $id=end($_url);
    
	$area_churches=[];
    if ($id+0>0) {
		$area=new areaModel($id);
		if ($area->id && !isset($_GET['m'])) {
			if ($area->name) {
				$title=$area->name.' - kościoły';
				$keywords.=', '.$area->name;
			}
			$_GET['m']=$area->lat.','.$area->lng.','.$area->zoom;
			if (isset($_SERVER['HTTP_USER_AGENT']) && strstr(strtolower($_SERVER['HTTP_USER_AGENT']),'google')) $area_churches=$area->churches();
		}
		//mydie($area_churches);
    }
	

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
		$image=CloudStorageTools::getImageServingUrl($file,['size'=>0+Bootstrap::$main->getConfig('image_size')]);
		$description='Mapa - stan na '.date('d-m-Y',$imgDate);
    }
?>
<html lang="pl">
    
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
	<?php
	    $moremenu=[
			'<a style="display:none" href="../edit/0" class="a_mapadd">Dodaj kościół</a>',
			'<input type="text" class="where" placeholder="szukaj miejscowości" id="where"/>'
		];
	    include __DIR__.'/../html/topmenu.phtml';
	?>
    </div>
	
	<?php if (count($area_churches)):?>
	<h1><?php echo $title;?></h1>
	<ul>
		<?php foreach($area_churches AS $ac):?>
			<li><a href="../kosciol/<?php echo Tools::str_to_url($ac['name']).','.$ac['id'];?>"><?php echo $ac['name'];?></a></li>
		<?php endforeach; ?>
	</ul><?php endif;?>	
  
    <div id="map-canvas">
        <input id="navigator_missing" title="Proszę wyraź zgodę na udostępnienie swojej lokalizacji" class="button" readonly value="Proszę wyraź zgodę na udostępnienie swojej lokalizacji"/>
    </div>
	
    <div id="footer" style="display: none">Przybliż mapę, aby zobaczyć kościoły</div>
    
</div>


</body>
</html>
