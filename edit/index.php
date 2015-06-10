<?php
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    $url=$_SERVER['REQUEST_URI'];
    $pos=strpos($url,'?');
    if ($pos) $url=substr($url,0,$pos);
    $_url=explode('/',$url);
    $id=end($_url);
    
    
    if (isset(Bootstrap::$main->user['id']) && Bootstrap::$main->user['id'] && isset($_GET['m'])) {
	$m=explode(',',$_GET['m']);
	if ($id+0==0 && count($m)>1 && $m[0]+0>0 && $m[1]+0>0) {
	    $church=new churchModel();
	    $church->lat=$m[0];
	    $church->lng=$m[1];
	    $church->change_author=Bootstrap::$main->user['id'];
	    $church->change_ip=Bootstrap::$main->ip;
	    $church->change_time=Bootstrap::$main->now;
	    $church->md5hash=substr($m[0],0,15).','.substr($m[1],0,15);
	    $church->save();
	    $uri=$_SERVER['REQUEST_URI'];
	    if ($pos=strpos($uri,'/edit')) $uri=substr($uri,0,$pos);
	    Header('Location: '.$uri.'/edit/'.$church->id);
	
	}
    }
    
    if ($id+0==0) return;
    
    
    
    $church=new churchModel($id);
    
    $title=$church->name;
    $description=$church->address;
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza,'.$church->address;
    $basedir='..';
    
    $mass=new massModel();
    
    $masses=[];
    for($d=0;$d<7;$d++)
	$masses[$d]=$mass->select(['church'=>$church->id,'dow'=>$d],'time');
    $masses[8]=$mass->select(['church'=>$church->id,'dow'=>8],'time');

    
    function tr_masses($dow,$masses)
    {
	if (!is_array($masses[$dow])) $masses[$dow]=[];
	
	$tr='';
	$mon=['','Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'];
	
	$times=[];
	foreach ($masses[$dow] AS $mass)
	{
	    $t=$mass['time'];
	    $time=date('H:i',$t+(Bootstrap::$main->appengine?3600:0));

	    if (isset($times[$time])) continue;
	    $times[$time]=true;
	    
	    $kids=$mass['kids']?'checked':'';
	    $youth=$mass['youth']?'checked':'';
	    
	    $tr.='<tr>';
	    $tr.='<td class="time">'.$time.'</td>';
	    $tr.='<td class="desc">
		    <input type="text" value="'.$mass['description'].'" placeholder="opis" class="desc" name="masses['.$dow.']['.$t.'][desc]" />
		    <span>
			<input type="checkbox" name="masses['.$dow.']['.$t.'][kids]" value="1" '.$kids.'/> dzieci
			<input type="checkbox" name="masses['.$dow.']['.$t.'][youth]" value="1" '.$youth.'/> młodzież
			<a>[ok]</a>
		    </span>
		</td>';
	    
	    
	    for ($m=1;$m<=12;$m++)
	    {
		$tr.='<td title="'.$mon[$m].'" class="mon m-'.$m.'"><input value="1" type="checkbox"';
		$tr.=' name="masses['.$dow.']['.$t.'][moy]['.$m.']"';
		foreach ($masses[$dow] AS $ms)
		{
		    if ($ms['time']==$t && $ms['moy']==$m) {
			$tr.=' checked';
			break;
		    }
		}
		
		$tr.="/></td>";
	    }
	    $tr.='<td class="rm">x</td>';
	    
	    $tr.='</tr>';
	    
	}

	$tr.='<tr>';
	$tr.='<td class="time"><input type="text" name="masses['.$dow.'][_new_][time]" placeholder="godz."/></td>';
	$tr.='<td class="desc">
		<input type="text" placeholder="opis" class="desc" name="masses['.$dow.'][_new_][desc]" />
		<span>
		    <input type="checkbox" name="masses['.$dow.'][_new_][kids]" value="1" /> dzieci
		    <input type="checkbox" name="masses['.$dow.'][_new_][youth]" value="1" /> młodzież
		    <a>[ok]</a>
		</span>
	    </td>';
	
	
	for ($m=1;$m<=12;$m++)
	{
	    $tr.='<td title="'.$mon[$m].'" class="month-'.$m.'"><input class="month" value="1" type="checkbox"';
	    $tr.=' name="masses['.$dow.'][_new_][moy]['.$m.']"/></td>';
	    
	}
	$tr.='<td><input type="checkbox" class="chkall"/></td>';
	
	$tr.='</tr>';


	return $tr;
    }
    
    function th_masses()
    {
	return '
	      <tr>
		<th>Godz</th>
		<th>Opis</th>
		<th title="Styczeń">1</th>
		<th title="Luty">2</th>
		<th title="Marzec">3</th>
		<th title="Kwiecień">4</th>
		<th title="Maj">5</th>
		<th title="Czerwiec">6</th>
		<th title="Lipiec">7</th>
		<th title="Siepień">8</th>
		<th title="Wrzesień">9</th>
		<th title="Październik">10</th>
		<th title="Listopad">11</th>
		<th title="Grudzień">12</th>
		<th title="Usuń">x</th>
	      </tr>	
	';
	
    }
    
?>
<html>
    
<head>
  <?php include __DIR__.'/../html/head.phtml';?>
  <script src="<?php echo $basedir;?>/js/edit.js"></script>
  
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
</div>
  
<?php if (isset(Bootstrap::$main->user['id']) && Bootstrap::$main->user['id']):?>
  
  <div class="container"><form role="form" id="churchForm">
    <input type="hidden" name="id" value="<?php echo $church->id?>"/>
    
    <div class="row">
      <div class="col-sm-6">

	
	  <div class="form-group">
	    <label for="name">Pod wezwaniem:</label>
	    <input required="true" type="text" class="form-control" name="name" id="name" value="<?php echo $church->name;?>" title="Pod wezwaniem">
	  </div>
	  <div class="form-group">
	    <label for="address">Adres:</label>
	    <input required="true" title="Adres" type="text" class="form-control" name="address" id="address" value="<?php echo $church->address;?>">
	  </div>
	  <div class="form-group">
	    <label for="phone">Telefon:</label>
	    <input required="true" title="Telefon" type="text" class="form-control" name="phone" id="phone" value="<?php echo $church->phone;?>">
	  </div>	  
	  <div class="form-group">
	    <label for="email">E-mail:</label>
	    <input type="text" class="form-control" name="email" id="email" value="<?php echo $church->email;?>">
	  </div>
	  <div class="form-group">
	    <label for="www">WWW:</label>
	    <input type="text" class="form-control" name="www" id="www" value="<?php echo $church->www;?>">
	  </div>	  
	  <div class="form-group">
	    <label for="rector">Proboszcz:</label>
	    <input type="text" class="form-control" name="rector" id="rector" value="<?php echo $church->rector;?>">
	  </div>	  
	  <div class="form-group">
	    <label for="sun">Msze w niedziele i święta (tekst):</label>
	    <input required="true" title="Msze niedzielne" type="text" class="form-control" name="sun" id="sun" value="<?php echo $church->sun;?>">
	  </div>
	  <div class="form-group">
	    <label for="week">Msze w dnie powszednie (tekst):</label>
	    <input type="text" class="form-control" name="week" id="week" value="<?php echo $church->week;?>">
	  </div>
	  <div class="form-group">
	    <label for="fest">Msze w święta zniesione (tekst):</label>
	    <input type="text" class="form-control" name="fest" id="fest" value="<?php echo $church->fest;?>">
	  </div>	  

	  
	  <div class="church-map" title="<?php echo $church->name; ?>" lat="<?php echo $church->lat;?>" lng="<?php echo $church->lng;?>"></div>
	  <button type="button" id="iamhere" class="btn btn-default button visible-xs visible-sm">Stoję teraz przed kościołem</button>
	  
	  <input type="hidden" name="lat" id="lat" value="<?php echo $church->lat;?>"/>
	  <input type="hidden" name="lng" id="lng" value="<?php echo $church->lng;?>"/>
	  



      </div>
      
      <div class="col-sm-6">
 
	<h2>Msze święte w niedziele i święta</h2>
	<h6 class="prompter sun"><?php echo $church->sun;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(0,$masses);?>
	    </tbody>
	</table></div>
	<h2>Msze święte w święta zniesione</h2>
	<h6 class="prompter fest"><?php echo $church->fest;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(8,$masses);?>
	    </tbody>
	</table></div>

	<h2>Msze święte w poniedziałki</h2>
	<h6 class="prompter week"><?php echo $church->week;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(1,$masses);?>
	    </tbody>
	</table></div>
	
	<h2>Msze święte we wtorki</h2>
	<h6 class="prompter week"><?php echo $church->week;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(2,$masses);?>
	    </tbody>
	</table></div>

	<h2>Msze święte w środy</h2>
	<h6 class="prompter week"><?php echo $church->week;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(3,$masses);?>
	    </tbody>
	</table></div>
	<h2>Msze święte w czwartki</h2>
	<h6 class="prompter week"><?php echo $church->week;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(4,$masses);?>
	    </tbody>
	</table></div>
	<h2>Msze święte w piątki</h2>
	<h6 class="prompter week"><?php echo $church->week;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(5,$masses);?>
	    </tbody>
	</table></div>
	<h2>Msze święte w soboty</h2>
	<h6 class="prompter week"><?php echo $church->week;?></h6>
	<div class="table-responsive"><table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(6,$masses);?>
	    </tbody>
	</table></div>
        <button type="button" class="save btn btn-default button">Zapisz</button>
        
     
      </div>
    </div>  
  
  </form></div>

<?php else: ?>

  <div class="container">
    <div class="row">
      <div class="col-sm-12">
	<a href="../rest/user/facebook?redirect=" onclick="this.href+=(location.href)">Zaloguj się</a>
      </div>
    </div>
  </div>
  
  
<?php endif; ?>

<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
