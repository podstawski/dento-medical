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
	
	<div>
	    <br/>
	    <a href="https://www.facebook.com/podstawski.piotr" target="_blank">Piotr Podstawski</a>
	</div>
	<iframe width="100%" height="500px" src="https://www.youtube.com/embed/f3KS07OyR8A" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>  
  
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
