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
    
	<h2 class=""><i>Nasi darczyńcy:</i></h2>
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
		<div class="col-sm-6"></div>
	</div>

  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
