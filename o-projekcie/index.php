<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='KiedyMsza św - o projekcie';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza';
    $basedir='..';

    $imageModel=new imageModel();
      
    
    $testimonial=new testimonialModel();
    $testimonials=$testimonial->join('church','churches')->select([],'rand()')?:[];
    
    
    foreach ($testimonials AS &$t)
    {
	$img=$imageModel->select(['church'=>$t['church'],'active'=>1],'rand()',1)?:[];
	if (!count($img)) {
	    $img=[['thumb'=>$basedir.'/img/dodaj.jpg']];
	}
	$t['image']=$img[0];
	$t['url']=$basedir.'/kosciol/'.Tools::str_to_url($t['name']).','.$t['church'];
    }
    
    //mydie($testimonials);
    
?>
<html>
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
</div>
  
  <div class="container about">
    
    <div class="row">
      <div class="col-sm-12">
	<h1>KiedyMsza.PL - o projekcie</h1>
	<p>
	    Stworzyłem tę stronę z myślą o wygodzie tych, którzy czasami
	    szukają mszy św. w innym kościele, bo są na wakacjach, lub w innym
	    miejscu niż zazwyczaj. Z zawodu jestem informatykiem,
	    tworzę nowoczesne witryny www, zatem zadanie było dla mnie dość proste.
	    Strona sprawdza, skąd się wchodzi (najlepiej przeglądać ze smartphona)
	    i szuka mszy św. w kościołach w okolicy 10km.
	</p>

	
	<h2>Wspólnie stwórzymy dokładne narzędzie</h2>
	<p>
	    Ponieważ lokalizacje oraz terminy mszy św. zostały pozyskane
	    z publicznych rejestrów
	    archidiecezji, nie zawsze dysponowałem dokładnymi danymi.
	    Zatem gorąca prośba - poszukaj kościoła, do którego zwykle
	    chodzisz, zrób zdjęcie, zweryfikuj dane, sprawdź na mapie
	    i zaktualizuj.
	    Z góry dziękuję!
	</p>
	
	<h2>Konkurs fotograficzny: “Wakacyjne zdjęcie kościoła”</h2>
	<p>
	    Znaleźli się wspaniali ludzie (m.in. Władysław Nielipiński
	    - fotograf i firma FLY.pl), którzy postanowili wesprzeć projekt.
	    Ogłosiliśmy zatem <a href="../konkurs">konkurs fotograficzny</a>,
	    dzięki któremu na stronie
	    KiedyMsza.pl znajdzie się wiele atrakcyjnych zdjęć parafii.
	</p>
	
	


	
	<div>
	    <br/>
	    <a href="https://www.facebook.com/podstawski.piotr" target="_blank">Piotr Podstawski</a>
	    <br/><br/>
	</div>
	
	
	
	
	
	
      </div>
    </div>
 
    
    <h3>Referencje</h3>
    
	    <div id="carousel-testimonials" class="carousel slide" data-ride="carousel">
		<!-- Indicators -->
		<ol class="carousel-indicators">
		    <?php for($i=0;$i<count($testimonials);$i++):?>
		    <li data-target="#carousel-testimonials" data-slide-to="<?php echo $i;?>" class="<?php if (!$i) echo 'active';?>"></li>
		    <?php endfor;?>
		</ol>
		<!-- Wrapper for slides -->
		<div class="carousel-inner">
		    <?php for($i=0;$i<count($testimonials);$i++):?>
		    <div class="item <?php if (!$i) echo 'active';?>">
			<div class="row">
			    <div class="col-xs-12">
				<div class="thumbnail adjust1">
				    <div class="col-md-2 col-sm-2 col-xs-12">
					<a href="<?php echo $testimonials[$i]['url'];?>">
					<img class="media-object img-rounded img-responsive" src="<?php echo $testimonials[$i]['image']['thumb']?>">
					</a>
				    </div>
				    <div class="col-md-10 col-sm-10 col-xs-12">
					<div class="caption">
					    <p class="testimonial"><span class="glyphicon glyphicon-thumbs-up"></span> 
					    <?php echo $testimonials[$i]['testimonial'];?>
					    </p>
					    <blockquote class="adjust2"> <p><?php echo $testimonials[$i]['author'];?></p>
						<small>
						    <cite>
							<a href="<?php echo $testimonials[$i]['url'];?>">
							<?php echo $testimonials[$i]['name'];?>
							</a>
						    </cite>
						</small>
					    </blockquote>
					</div>
				    </div>
				</div>
			    </div>
			</div>
		    </div>
		    <?php endfor;?>
		</div>
		<!-- Controls -->
		<a class="left carousel-control" href="#carousel-testimonials" data-slide="prev">
		    <span class="glyphicon glyphicon-chevron-left"></span>
		</a>
		<a class="right carousel-control" href="#carousel-testimonials" data-slide="next">
		    <span class="glyphicon glyphicon-chevron-right"></span>
		</a>
	    </div>
  
  
    <h3>Obejrzyj film: intencje powstania strony, instrukcja ...</h3>
    <iframe width="100%" height="500px" src="https://www.youtube.com/embed/f3KS07OyR8A" frameborder="0" allowfullscreen></iframe>
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
