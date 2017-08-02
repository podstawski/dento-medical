<?php
    use google\appengine\api\cloud_storage\CloudStorageTools;
    
    include __DIR__.'/../rest/https.php';
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

	$basedir='..';
	$u=null;
	if (isset($_GET['u'])) {
		$user=new userModel();
		$u=$user->get_by_fbid($_GET['u']);
	} elseif (isset(Bootstrap::$main->user) && isset(Bootstrap::$main->user['id'])) {
		$u=Bootstrap::$main->user;
	} else {
		Header('Location: '.$basedir.'/rest/user/facebook?redirect='.urlencode('https://www.kiedymsza.pl'.$_SERVER['REQUEST_URI']));
	}
	
	if (!$u) die();
	
    $church=new churchModel();
    $churches=$church->my_churches($u['id']);
	
	foreach ($churches AS &$ch)
	{
		$ch['url']='../kosciol/'.Tools::str_to_url($ch['name']).','.$ch['id'];
		foreach(explode(',','id,email,md5hash,www,password,phone,about,address,active,change_author,change_ip,change_time,successor,sun,fest,week,rector,area,country,city,tel,postal') AS $k) unset($ch[$k]);
	}
	
    if (count($churches)==0) die();
	
    $title='KiedyMsza - użytkownika';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza';
    
	
	

    $imagePath=Tools::saveRoot('maps');
	$latlang=implode(',',$config['pl.center']).',6';
    
	if (isset($_GET['m']) && count(explode(',',$_GET['m']))==3) $latlang=$_GET['m'];
	
?>
<html lang="pl">
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>
    <?php
		echo '<script>';
		echo 'var LATLNG="'.$latlang.'";';
		echo "var churches=JSON.parse('".json_encode($churches)."')";
		echo '</script>';    
    ?>

    <script src="<?php echo $basedir;?>/js/mine.js"></script>

</head>

<body>

<div class="map">
    <div class="head">
	<?php
	    $moremenu=[
		];
	    include __DIR__.'/../html/topmenu.phtml';
	?>
    </div>
	
    <div id="map-canvas">
    </div>
	
	
	
</div>

</body>
</html>
