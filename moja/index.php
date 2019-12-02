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
	
	if (!$u) die('<script>history.go(-1);</script>');
	
    $church=new churchModel();
    $churches=$church->my_churches($u['id'],true);
	

	if (!$churches) die('<script>history.go(-1);</script>');
	
	foreach ($churches AS &$ch)
	{
		$ch['url']='../kosciol/'.Tools::str_to_url($ch['name']).','.$ch['id'];
		$ch['name']=str_replace('"','',$ch['name']);
		foreach(explode(',','id,email,md5hash,www,password,phone,about,address,active,change_author,change_ip,change_time,successor,sun,fest,week,rector,area,country,city,tel,postal') AS $k) unset($ch[$k]);
	}
	
    if (count($churches)==0) die('<script>history.go(-1);</script>');
	
    $title='KiedyMsza - użytkownika';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza';
    
	
	//mydie($churches);

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

    <script src="<?php echo $basedir;?>/js/omine.js?v=<?php echo time()?>"></script>

</head>

<body>

<div class="map">
    <div class="head">
	<?php
	    $moremenu=[
			'<a target="_blank" href="'.$u['url'].'"><img class="mojefoto" src="'.$u['photo'].'"></a>'
		];
	    include __DIR__.'/../html/topmenu.phtml';
	?>
    </div>
	
    <div id="map-canvas">
    </div>
	
	
	
</div>

</body>
</html>
