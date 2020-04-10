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
	
	$url=$_SERVER['REQUEST_URI'];
    $pos=strpos($url,'?');
    if ($pos) $url=substr($url,0,$pos);
    $_url=explode('/',$url);
    $id=end($_url);

	$church=new churchModel($id);
	


	if (isset(Bootstrap::$main->user['id']) && Bootstrap::$main->user['id']) {
		if (isset($_POST['date_submit']) && isset($_POST['time_submit']) && isset($_POST['link']) && substr($_POST['link'],0,8)=='https://') {
			$date=$_POST['date_submit'].'T'.$_POST['time_submit'];
			$hash=md5($config['online.salt'].$church->id.'addMass'.Bootstrap::$main->user['id']);
			$url=$config['online.url'].'?json=1&action=addMass&userId='.Bootstrap::$main->user['id'].'&hash='.$hash.'&churchId='.$church->id;
			$url.='&start='.$date.'&name='.urlencode($church->name.', '.$church->address);
			$url.='&link='.urlencode($_POST['link']);
			file_get_contents($url);
		}
		
		if (isset($_GET['remove'])) {
			$hash=md5($config['online.salt'].$church->id.'removeMass'.Bootstrap::$main->user['id']);
			$url=$config['online.url'].'?json=1&action=removeMass&userId='.Bootstrap::$main->user['id'].'&hash='.$hash.'&churchId='.$church->id;
			$url.='&id='.urlencode($_GET['remove']);
			file_get_contents($url);
		}
		
	}
	
	$hash=md5($config['online.salt'].$church->id.'listMasses'.'1');
    $url=$config['online.url'].'?json=1&action=listMasses&userId=1&hash='.$hash.'&churchId='.$church->id;
    $online=json_decode(file_get_contents($url),true);
    
	
	date_default_timezone_set('Europe/Warsaw');
	foreach ($online AS &$live) {
		$user=new userModel($live['user']);
		$live['photo'] = $user->photo;
		$live['date'] = date('d-m-Y, H:i',strtotime($live['start']));
		
		
	}
	//mydie($online);
?>
<html lang="pl">
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>
	
	
	<script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.js"></script>
	<script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.date.js"></script>
	<script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/picker.time.js"></script>
	<script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/legacy.js"></script>  
	<script src="<?php echo $basedir;?>/js/pickadate.js/lib/compressed/translations/pl_PL.js"></script>
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
  
  
</div>
  
  <?php foreach($online AS &$live) :?>
  
  <div class="container about">
	<div class="row" style="margin-bottom: 10px;">
	  <img src="<?php echo $live['photo']?>" align="absMiddle" style="width: 50px; border-radius: 50%"/>
	  <a href="<?php echo $live['link']?>" target="_blank">
	  <?php echo $live['date']?>
	  </a>
	  <a href="?remove=<?php echo $live['id']?>">
	  <sup style="margin-left:5px">x</sup>
	  </a>
	</div>
  </div>
  
  <?php endforeach; ?>
  
  <hr/>
  <form class="container about" method="POST">
	Dodaj mszę ONLINE:
	<input type="text" class="date" placeholder="data" name="date" style="width: 20%"/>
	<input type="text" class="time" placeholder="godz" name="time" style="width: 10%"/>
	<input type="text" class="url" placeholder="link" name="link" style="width: 35%"/>
	<input type="submit" class="save btn btn-default button" value="Dodaj" style="width: 10%; display: inline-block; height: 35px; padding: 3px; margin-bottom: 5px;"/>
	
  </form>

<script>
	$('form .date').pickadate({
        onSet:function() {
            //draw_route(true);
        },
        format: 'dddd, dd-mm-yyyy',
		formatSubmit: 'yyyy-mm-dd',
        selectYears: false,
    });

    $('form .time').pickatime({
        onSet:function() {
            //draw_route(true);
        },        
        format: 'HH:i',
        formatSubmit: 'HH:i',
        min: [6,0],
        max: [21,0],
        interval: 30
    });
	
	
	
</script>

<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
