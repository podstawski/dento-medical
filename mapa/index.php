<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='KiedyMsza - o mapa';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza';
    $basedir='..';

    
?>
<html>
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>


    <script src="<?php echo $basedir;?>/js/church.js"></script>
  
</head>

<body>

<div class="head">
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
</div>
  
  <div class="container">
    
    <div class="row">
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
	    Ponieważ dane zostały pozyskane z publicznych rejestrów
	    archidiecezji, nie zawsze dysponowałem dokładnymi danymi.
	    Zatem gorąca prośba - poszukaj kościoła, do którego zwykle
	    chodzisz, zrób zdjęcie, zweryfikuj dane i zaktualizuj.
	    Z góry dziękuję!
	</p>
	
	<div>
	    Piotr Podstawski
	</div>
      </div>
    </div>  
  
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>