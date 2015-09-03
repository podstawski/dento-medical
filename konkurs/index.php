<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='Konkurs fotograficzny - Wakacyjne zdjęcie kościoła';
    $description='Ogłaszamy konkurs na najciekawsze zdjęcie kościoła zrobione w czasie wakacji (od 1 lipca do 31 sierpnia 2015). Zdjęcia należy umieścić na portalu kiedymsza.pl i muszą odpowiadać fizycznej lokalizacji parafii na mapie. W dniach 01-04 września 2015 r. profesjonalne jury dokona oceny zdjęć i przyzna nagrody.';
    
    $keywords='msza,msze,kiedy msza,gdzie msza,konkurs';
    $basedir='..';
    $image=$basedir.'/img/fly.jpg';
  
  
    $user=new userController();
    $user_likes=$user->likes();
    if ($user_likes)
    {
		$image=new imageModel();
		$competition_images=$image->join('church','churches')->select(['author_id'=>Bootstrap::$main->user['id'],'images.active'=>1,'d_uploaded'=>['>',strtotime('2015-07-01')]])?:[];
    }
    
?>
<html lang="pl">
    
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
	<h2>Konkurs roztrzygnięty</h2>
	<p>
	    Jury w dniu 2 września 2015 wyłoniło pierwsze, drugie i dwa trzecie miejsca.
	</p>
	
	<h2>Nagrody</h2>
    
	<ul>
		<li>
			I miejsce - bon o wartości 500zł na wycieczkę wykupioną w <a href="https://fly.pl" target="_blank">FLY.pl</a>
			otrzymał <a href="https://www.facebook.com/velo.nero" target="_blank">Velo Nero</a>
		</li>
		<li>
			II miejsce - bon o wartości 250zł na wycieczkę wykupioną w <a href="https://fly.pl" target="_blank">FLY.pl</a>
			otrzymał <a href="https://www.facebook.com/grzegorz.bien.90" target="_blank">Grzegorz Bień</a>
		</li>
		<li>
			III miejsce - album fotograficzny wydawnictwa  <a href="http://www.wbp.poznan.pl/wydawnictwo/" target="_blank">WBPiCAK</a> w Poznaniu
			otrzymał <a href="https://www.facebook.com/profile.php?id=100009516605359" target="_blank">Piotr Maj</a>
		</li>
		<li>
			III miejsce - album fotograficzny wydawnictwa  <a href="http://www.wbp.poznan.pl/wydawnictwo/" target="_blank">WBPiCAK</a> w Poznaniu
			otrzymał <a href="https://www.facebook.com/rkozielski" target="_blank">Robert Kozielski</a>
		</li>
		
	</ul>
	<p>
	    Wszyscy uczestnicy konkursu spoza podium
	    (każda osoba, która wgrała przynajmniej jedno samodzielnie wykonane
	    zdjęcie prawdziwego kościoła oraz polubi(ła) stronę <a href="https://www.facebook.com/KiedyMsza" target="_blank">Kiedy Msza</a> na Facebooku)
	    otrzymali bon o wartości 100 zł na wycieczkę wykupioną w <a href="https://fly.pl" target="_blank">FLY.pl</a>
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
		<a title="I miejsce, autor: Velo Nero" class="fancybox" href="http://lh3.googleusercontent.com/LmU4oszjWeBPXc0iKr4RlA7ZbYAxI9Jfw9V1D-dGiQEDy6FwI9P72F7qrjyspDV77KnjyS8kCi8BhfjxOKtT=s1140">
			<img src="http://lh3.googleusercontent.com/LmU4oszjWeBPXc0iKr4RlA7ZbYAxI9Jfw9V1D-dGiQEDy6FwI9P72F7qrjyspDV77KnjyS8kCi8BhfjxOKtT=s900-c" style="width:100%; margin-bottom:10px;"/>
		</a>
		
		<a title="II miejsce, autor: Grzegorz Bień" class="fancybox" href="http://lh3.googleusercontent.com/0VcLd6aImNF0IQXuyvwHjUy_lzkLBi6OsrB04iJjf2ak9898cZ0Hd9SqjzbY5X2LwZHMPb1CsIXS00usHSwtfw=s1140">
			<img src="http://lh3.googleusercontent.com/0VcLd6aImNF0IQXuyvwHjUy_lzkLBi6OsrB04iJjf2ak9898cZ0Hd9SqjzbY5X2LwZHMPb1CsIXS00usHSwtfw=s900-c" style="width:100%; margin-bottom:10px;"/>
		</a>		

		<a title="III miejsce, autor: Piotr Maj" class="fancybox" href="http://lh3.googleusercontent.com/jKS5FQyMjT53Qwz1bEgKmlBIZA8JekS3YiusUZ03rcxL22RLf0UpuqWt0ikTV0meRdMxflDckCoyIklOykE=s1140">
			<img src="http://lh3.googleusercontent.com/jKS5FQyMjT53Qwz1bEgKmlBIZA8JekS3YiusUZ03rcxL22RLf0UpuqWt0ikTV0meRdMxflDckCoyIklOykE=s900-c" style="width:100%; margin-bottom:10px;"/>
		</a>
		
		<a title="III miejsce, autor: Robert Kozielski" class="fancybox" href="http://lh3.googleusercontent.com/1M_IKJZUywyCxY__FwAPaZDow0v3E7wqZU2sPyteZhcDtIayHOuuvA0dxjX242jRJleLo9Bc4ixkvtjc4NU=s1140">
			<img src="http://lh3.googleusercontent.com/1M_IKJZUywyCxY__FwAPaZDow0v3E7wqZU2sPyteZhcDtIayHOuuvA0dxjX242jRJleLo9Bc4ixkvtjc4NU=s900-c" style="width:100%; margin-bottom:10px;"/>
		</a>			
		
	<h3>Odbiór nagród</h3>
	<div class="competition">
	    <?php if ($user_likes): ?>

		<?php if (count($competition_images)): ?>
		    <ul>
		    <?php foreach($competition_images AS $img): ?>
			<li>
			    <a href="<?php echo $img['url'];?>" title="<?php echo $img['name'].', '.$img['address'];?>" class="fancybox"><img src="<?php echo $img['thumb'];?>"/></a>
			    <i>Twój kod rabatowy:<br/><b><?php echo $img['code']?:'NW108796078'?></b></i>
			    <hr clear="all"/>
			    
			</li>
		    <?php endforeach; ?>
		    </ul>
		    <a href="http://fly.pl/regulaminkupony/" target="_blank">Jak zrealizować bon wakacyjny?</a>
		<?php else: ?>
		    Nie wgrano jeszcze żadnego zdjęcia, lub te, które zostały wgrane oczekują na akceptację.
		<?php endif;?>
	    <?php else: ?>
		<a href="<?php echo $basedir;?>/login/" class="a_login">Polub stronę i zaloguj się aby odebrać bon na 100zł</a>
	    <?php endif;?> 
	</div>
	
	<!-- Konkurs -->
	<ins class="adsbygoogle"
	     style="display:block"
	     data-ad-client="ca-pub-3681218186493233"
	     data-ad-slot="8609440708"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	
	
      </div>
    </div>  
  
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
