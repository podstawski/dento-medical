<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='Darczyńcy';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza,darczyńcy';
    $basedir='..';

   
	
	$payment=new paymentModel();
	$payments=$payment->select([],'date DESC,id DESC');
	$suma=0;
    
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
	
	<p>
		Razem - <a href="https://www.kiedymsza.pl/o-projekcie/#team">jako zespół</a>
            - budujemy narzędzie dla katolików poszukujących Mszy św. w czasie podróży.
            Jestem dumny, iż jest nas już ponad <b>1000 osób </b>współpracujących przy budowie
            strony.
            Jak wiecie, jest to projekt NON-PROFIT, ale nasz <i>przyjaciel Google</i> tego
            nie rozumie i za niezawodne serwery nalicza sobie ok 100zł miesięcznie, im więcej
            nas jest (tych, którzy korzystamy z tej <i>niezawodności</i>), tym więcej Google
            nalicza. Przychody z reklam to ok 15zł miesięcznie. Tę różnicę pokrywam z własnej
            kieszeni. Dużo czy nie dużo - to pojęcie względne, cieszę się, że mogę Wam
            pomagać pracując i udoskonalając system, szczerze powiedziawszy mam przy tym
            trochę frajdy ;)
	</p>
	
    
	<h2 class="">Nasi darczyńcy:</h2>
	<?php foreach ($payments AS $p) {
		$suma+=$p['amount'];
	?>

	<div class="row">
		<div class="col-sm-3"><?php echo $p['initials'];?></div>
		<div class="col-sm-3" ><?php echo round($p['amount']);?> zł</div>
		<div class="col-sm-6"><?php echo date('d-m-Y',$p['date']);?></div>
	</div>
	<?php };?>
   
	<div class="row" style="border-top: 1px black solid">
		<div class="col-sm-3">Razem</div>
		<div class="col-sm-3" ><?php echo round($suma);?> zł</div>
		<div class="col-sm-6">od początku istnienia strony</div>
	</div>
	
	
	<h2 class="">Dołączysz?</h2>
	
			<a href="https://patronite.pl/kiedymsza" target="_blank">
				<img src="<?php echo $basedir;?>/img/patronite-logo.svg" alt="Logo Patronite" style="width:50%; margin-top:30px;"/>
				<br/>https://patronite.pl/kiedymsza
			</a>

  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
