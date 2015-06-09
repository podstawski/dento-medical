<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='KiedyMsza - o projekcie';
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
	    szukają mszy w innym kościele, bo są na wakacjach, lub w innym
	    miejscu niż zazwyczaj.
	    Strona sprawdza, skąd wchodzimy (najlepiej przeglądać ze smartphona) i szuka
	    mszy w kościołach w okolicy 10km.
	</p>

	
	<h2>Wspólnie stwórzymy dokładne narzędzie</h2>
	<p>
	    Ponieważ lokalizacje oraz terminy mszy zostały pozyskane
	    z publicznych rejestrów
	    archidiecezji, nie zawsze dysponowałem dokładnymi danymi.
	    Zatem gorąca prośba - poszukaj kościoła, do którego zwykle
	    chodzisz, zrób zdjęcie, zweryfikuj dane, sprawdź na mapie
	    i zaktualizuj.
	    Z góry dziękuję!
	</p>
	
	<div>
	    <a href="https://www.facebook.com/podstawski.piotr" target="_blank">Piotr Podstawski</a>
	</div>
      </div>
    </div>  
  
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
