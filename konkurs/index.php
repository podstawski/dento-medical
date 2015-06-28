<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='Konkurs fotograficzny - Wakacyjne zdjęcie kościoła';
    $description='Ogłaszamy konkurs na najciekawsze zdjęcie kościoła zrobione w czasie wakacji (od 1 lipca do 31 sierpnia 2015). Zdjęcia należy umieścić na portalu kiedymsza.pl i muszą odpowiadać fizycznej lokalizacji parafii na mapie. W dniach 01-04 września 2015 r. profesjonalne jury dokona oceny zdjęć i przyzna nagrody.';
    
    $keywords='msza,msze,kiedy msza,gdzie msza,konkurs';
    $basedir='..';
    $image=$basedir.'/img/konkurs.jpg';
  
  
    $user=new userController();
    $user_likes=$user->likes();
    if ($user_likes)
    {
	$image=new imageModel();
	$competition_images=$image->select(['author_id'=>Bootstrap::$main->user['id'],'active'=>1,'code'=>['<>','']])?:[];
    }
    
?>
<html>
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
</div>
  
  <div class="container">
    
    <div class="row about">
      <div class="col-md-8">
	
	<h1>Konkurs fotograficzny: “Wakacyjne zdjęcie kościoła”</h1>
	<p>
	   <b>Organizator:</b> <a href="../o-projekcie">KiedyMsza.pl</a>
	</p>
	    
	<p>
	    <b>Patronat:</b>
	    <a href="http://www.wbp.poznan.pl/fotografia/" target="_blank">
	    <img src="http://www.wbp.poznan.pl/img/template/top/wbpicak-logo.png" width="200px" border="0" align="absmiddle"/>
	    </a>

	</p>

	<p>
	    <b>Partner:</b>
	    <a href="https://fly.pl" target="_blank">
	    <img src="https://fly.pl/wp-content/uploads/2014/11/flypl_logo.png" width="175px" border="0" align="absmiddle" />
	    </a> 	    
	</p>
	<h2>Termin przysyłania prac</h2>
	<p>
	    Ogłaszamy konkurs na najciekawsze zdjęcie kościoła zrobione w czasie wakacji (od 1 lipca do 31 sierpnia 2015).
	    Zdjęcia należy umieścić na portalu kiedymsza.pl i muszą odpowiadać fizycznej lokalizacji parafii na <a href="../mapa">mapie</a>.
	    W dniach 01-04 września 2015 r. profesjonalne jury dokona oceny zdjęć i przyzna nagrody.
	</p>
	
	<h2>Nagrody</h2>
    
	    <ul>
		<li>I miejsce - bon o wartości 500zł na wycieczkę wykupioną w <a href="https://fly.pl" target="_blank">FLY.pl</a></li>
		<li>II miejsce - bon o wartości 250zł na wycieczkę wykupioną w <a href="https://fly.pl" target="_blank">FLY.pl</a></li>
		<li>III miejsce - album fotograficzny wydawnictwa  <a href="http://www.wbp.poznan.pl/wydawnictwo/" target="_blank">WBPiCAK</a> w Poznaniu</li>
		
	    </ul>
	<p>
	    Wszyscy uczestnicy konkursu spoza podium
	    (każda osoba, która wgra przynajmniej jedno samodzielnie wykonane
	    zdjęcie prawdziwego kościoła oraz polubi stronę <a href="https://www.facebook.com/KiedyMsza" target="_blank">Kiedy Msza</a> na Facebooku)
	    otrzymają bon o wartości 100 zł na wycieczkę wykupioną w <a href="https://fly.pl" target="_blank">FLY.pl</a>
	</p>
	
	<p>
	    W ramach jednej rezerwacji uczestnik konkursu może zrealizować tylko jeden bon.
	</p>
	<h2>Zastrzeżenia prawne</h2>
	<p>
	    Organizator zastrzega sobie prawo do nieodpłatnego wykorzystywania (z poszanowaniem praw autorskich) wszystkich prac do celów ekspozycyjnych oraz publikacji w formie elektronicznej i drukowanej.
	</p>
      </div>
      
      <div class="col-md-4">
	<img src="../img/konkurs.jpg" style="width:100%"/>
	
	<!-- Konkurs -->
	<ins class="adsbygoogle"
	     style="display:block"
	     data-ad-client="ca-pub-3681218186493233"
	     data-ad-slot="8609440708"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	
	<h3>Odbiór nagród</h3>
	<div class="competition">
	    <?php if ($user_likes): ?>
		Konkurs zaczyna się 1 lipca!<br/>
		<?php if (count($competition_images)): ?>
		    <ul>
		    <?php foreach($competition_images AS $img): ?>
			<li>
			    <img src="<?php echo $img['thumb'];?>"/>
			    <i>Twój kod rabatowy:<br/><?php echo $img['code'];?></i>
			    <hr clear="all"/>
			    
			</li>
		    <?php endforeach; ?>
		    </ul>
		<?php else: ?>
		    Nie wgrano jeszcze żadnego zdjęcia, lub te, które zostały wgrane oczekują na akceptację.
		<?php endif;?>
	    <?php else: ?>
		<a href="<?php echo $basedir;?>/login/" class="a_login">Polub stronę i zaloguj się aby odebrać bon na 100zł</a>
	    <?php endif;?> 
	</div>
      </div>
    </div>  
  
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
