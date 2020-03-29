<?php
  include __DIR__.'/rest/library/backend/include/all.php';
  
  autoload([__DIR__.'/rest/classes',__DIR__.'/rest/models',__DIR__.'/rest/controllers']);
  $config=json_config(__DIR__.'/rest/configs/application.json');

  $bootstrap = new Bootstrap($config);
  register_shutdown_function(function () {
      @Bootstrap::$main->closeConn();   
  });
  
  function sitemap_date($t)
  {
    return date('c',$t);
    
  }
  
  $church=new churchModel();
  $churches=$church->getAll();
  
  $time_change=strtotime('2017-01-18');
  
  
  header('Content-type: application/xml; charset=utf-8');
  
  
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">  
<url>
    <loc>https://www.kiedymsza.pl/</loc>
</url>
<url>
    <loc>https://www.kiedymsza.pl/o-projekcie/</loc>
</url>
<url>
    <loc>https://www.kiedymsza.pl/online/</loc>
</url>
<?php foreach($churches AS $ch): ?>
  <url>
    <loc>https://www.kiedymsza.pl/kosciol/<?php echo Tools::str_to_url($ch['name'])?>,<?php echo $ch['id'];?></loc>
  </url>
<?php endforeach; ?>
</urlset>

  
