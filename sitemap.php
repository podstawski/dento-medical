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
  
  $time_change=strtotime('2015-06-10');
  
  
  header('Content-type: application/xml; charset=utf-8');
  
  
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">  
<url>
    <loc>http://www.kiedymsza.pl/</loc>
    <lastmod><?php echo sitemap_date(strtotime('2015-05-20'))?></lastmod>
    <priority>1</priority>
  </url>

<?php foreach($churches AS $ch): ?>
  <url>
    <loc>http://www.kiedymsza.pl/kosciol/<?php echo Tools::str_to_url($ch['name'])?>,<?php echo $ch['id'];?></loc>
    <lastmod><?php echo sitemap_date($ch['change_time']>$time_change?$ch['change_time']:$time_change)?></lastmod>
    <priority>0.9</priority>
  </url>
<?php endforeach; ?>
</urlset>

  