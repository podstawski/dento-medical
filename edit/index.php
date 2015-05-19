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
	    $time=date('H:i',$t);

	    if (isset($times[$time])) continue;
	    $times[$time]=true;
	    
	    $tr.='<tr>';
	    $tr.='<td class="time">'.$time.'</td>';
	    $tr.='<td class="desc">
		    <input type="text" value="'.$mass['description'].'" placeholder="opis" class="desc" name="masses['.$dow.']['.$t.'][desc]" />
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
	$tr.='<td class="time"><input type="text" placeholder="godz."/></td>';
	$tr.='<td class="desc">
		<input type="text" placeholder="opis" class="desc" name="masses['.$dow.'][_new_][desc]" />
	    </td>';
	
	
	for ($m=1;$m<=12;$m++)
	{
	    $tr.='<td title="'.$mon[$m].'" class="month-'.$m.'"><input value="1" type="checkbox"';
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
		<th>Godzina</th>
		<th>Opis</th>
		<th title="Styczeń">I</th>
		<th title="Luty">II</th>
		<th title="Marzec">III</th>
		<th title="Kwiecień">IV</th>
		<th title="Maj">V</th>
		<th title="Czerwiec">VI</th>
		<th title="Lipiec">VII</th>
		<th title="Siepień">VIII</th>
		<th title="Wrzesień">IX</th>
		<th title="Październik">X</th>
		<th title="Listopad">XI</th>
		<th title="Grudzień">XII</th>
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
	    <input type="text" class="form-control" name="name" id="name" value="<?php echo $church->name;?>">
	  </div>
	  <div class="form-group">
	    <label for="address">Adres:</label>
	    <input type="text" class="form-control" name="address" id="address" value="<?php echo $church->address;?>">
	  </div>
	  <div class="form-group">
	    <label for="phone">Telefon:</label>
	    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $church->phone;?>">
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
	    <input type="text" class="form-control" name="sun" id="sun" value="<?php echo $church->sun;?>">
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
	  <button type="button" id="iamhere" class="btn btn-default button">Stoję przed kościołem</button>
	  
	  <input type="hidden" name="lat" id="lat" value="<?php echo $church->lat;?>"/>
	  <input type="hidden" name="lng" id="lng" value="<?php echo $church->lng;?>"/>
	  



      </div>
      
      <div class="col-sm-6">
 
	<h2>Msze święte w niedziele i święta</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(0,$masses);?>
	    </tbody>
	</table>
	<h2>Msze święte w święta zniesione</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(8,$masses);?>
	    </tbody>
	</table>

	<h2>Msze święte w poniedziałki</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(1,$masses);?>
	    </tbody>
	</table>
	
	<h2>Msze święte we wtorki</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(2,$masses);?>
	    </tbody>
	</table>

	<h2>Msze święte w środy</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(3,$masses);?>
	    </tbody>
	</table>
	<h2>Msze święte w czwartki</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(4,$masses);?>
	    </tbody>
	</table>
	<h2>Msze święte w piątki</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(5,$masses);?>
	    </tbody>
	</table>
	<h2>Msze święte w soboty</h2>
	<table class="table table-hover table-bordered">
	    <thead>
		<?php echo th_masses();?>
	    </thead>
	    <tbody>
		<?php echo tr_masses(6,$masses);?>
	    </tbody>
	</table>
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


</body>
</html>