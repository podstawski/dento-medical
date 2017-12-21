<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    
    $title='KiedyMsza św - polityka prywartności';
    $description='';
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza';
    $basedir='..';
	
	
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
      <h1>Polityka prywatności</h1>
	  
	  
	  
	  <div class="text1"><p><em>Opisana polityka plików cookies odnosi się do wszystkich stron internetowych dostępnych pod adresem www.kiedymsza.pl.</em></p>

<p><strong>Ciasteczka&nbsp;</strong>(ang. cookies) - niewielkie informacje tekstowe, wysyłane przez serwer WWW i zapisywane po stronie użytkownika (zazwyczaj na twardym dysku). Domyślne parametry ciasteczek pozwalają na odczytanie informacji w nich zawartych jedynie serwerowi, który je utworzył. Ciasteczka są stosowane najczęściej w przypadku liczników, sond, sklepów internetowych, stron wymagających logowania, reklam i do monitorowania aktywności odwiedzających.<br>
Za&nbsp;www.wikipedia.pl</p>

<p><strong>Wykorzystujemy pliki cookies w celu:</strong></p>

<ul>
	<li>ułatwienia użytkownikom poruszania się po stronach serwisu i korzystania z niego,</li>
	<li>umożliwienia korzystania z Panelu Klienta w sklepie internetowym i katalogach biblioteki,</li>
	<li>dostosowania naszych usług do preferencji użytkowników,</li>
	<li>pozyskiwania anonimowych danych dotyczących sposobu, w jaki użytkownicy korzystają z serwisu oraz statystyk ruchu na naszych stronach,</li>
	<li>zapewnienia standardów bezpieczeństwa.</li>
</ul>

<p>Stosujemy pliki cookies „stałe” oraz „sesyjne”. Pliki cookies „stałe” pozostają w przeglądarce internetowej urządzenia do czasu ich usunięcia przez użytkownika bądź do z góry ustalonego czasu określonego w parametrach pliku cookies. “Sesyjne” pliki cookies pozostają w przeglądarce do momentu jej wyłączenia lub wylogowania się ze strony internetowej na której zostały zamieszczone.</p>

<p>Wykorzystujemy usługi Google Analytics oraz Facebook, które legitymują się własną polityką prywatności:</p>

<p><a href="http://www.google.pl/intl/pl/analytics/privacyoverview.html" target="_blank">Polityka ochrony prywatności Google Analytics &gt;&nbsp;</a><br>
<a href="https://www.facebook.com/privacy/explanation" target="_blank">Polityka ochrony prywatności Facebook &gt;</a>&nbsp;<br>
&nbsp;<br>
Wszystkie nowoczesne przeglądarki pozwalają na włączenie bądź wyłączenie mechanizmu ciasteczek (domyślnie zazwyczaj jest on włączony). Jeśli użytkownik nie chce otrzymywać plików cookie, może zmienić ustawienia swojej przeglądarki internetowej - może to jednak uniemożliwić pełne i wygodne korzystanie z naszych stron.</p></div>
<br/>
<br/>
<br/>
	 <br/> 
	  
    </div<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>