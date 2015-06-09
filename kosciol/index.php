<?php
    
    include __DIR__.'/../rest/library/backend/include/all.php';    
    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);

    $config=json_config(__DIR__.'/../rest/configs/application.json');
    $bootstrap = new Bootstrap($config);

    $url=$_SERVER['REQUEST_URI'];
    $pos=strpos($url,'?');
    if ($pos) $url=substr($url,0,$pos);
    $_url=explode(',',$url);
    $id=end($_url);
    
    if ($id+0==0) return;
    
    $church=new churchModel($id);
    
    $right_url=Tools::str_to_url($church->name).','.$id;
    
    if (!strstr($_SERVER['REQUEST_URI'],$right_url))
    {
        if ( (isset($_SERVER['HTTP_REFERER']) && strstr(strtolower($_SERVER['HTTP_REFERER']),'google'))
         || (isset($_SERVER['HTTP_USER_AGENT']) && strstr(strtolower($_SERVER['HTTP_USER_AGENT']),'google')) )
        {
            header("HTTP/1.1 301 Moved Permanently"); 
            header("Location: ".$right_url); 
        }
        return;
    }
    
    $title=$church->name;
    $description='Msze '.$church->address;
    $image='';
    $keywords='msza,msze,kiedy msza,gdzie msza,'.$church->address;
    $basedir='..';
    
    $user=new userModel();
    $imageModel=new imageModel();
    $images=$imageModel->select(['church'=>$church->id,'active'=>1],'rand()')?:[];
    $active=false;
    foreach($images AS &$img) {
        if (!$image) $image=$img['url'];
        $img['active']=$active?'':'active';
        $active=true;
        $img['author']=$user->get($img['author_id']);
        unset($img['author_id']);
        if (isset($img['author']['id'])) unset($img['author']['id']);
        if (isset($img['author']['email'])) unset($img['author']['email']);
        
        
    }
    //mydie($images);
    //mydie($church);
    if ($church->change_author) $change_author=$user->get($church->change_author);
?>
<html>
    
<head>    
    <?php include __DIR__.'/../html/head.phtml';?>

    <script src="<?php echo $basedir;?>/js/church.js"></script>
  
</head>

<body>

<div class="head">
  <?php
    $moremenu=['<a href="../edit/'.$church->id.'" class="a_update">Aktualizuj dane</a>'];
  ?>
  
  <?php include __DIR__.'/../html/topmenu.phtml';?>
  
</div>
  
  <div class="container">
    
    <div class="row">
      <div class="col-sm-6">

        <div id="churchCarousel" class="carousel slide">

          <!-- Carousel items -->
          
          
          <div class="carousel-inner">

            <?php foreach($images AS &$img): ?>
            <div class="<?php echo $img['active'];?> item">
                <a href="<?php echo $img['url'];?>" class="fancybox"
                            title="<?php
                                echo $church->name;
                                echo ', fot. '.$img['author']['firstname'].' '.$img['author']['lastname'];
                                if ($img['d_taken']) echo date(' d-m-Y',$img['d_taken']);
                            ?>">
                    <img src="<?php echo str_replace('s960-c','s900-c',$img['square']);?>"/>
                </a>
                <?php if(isset($img['author']['url'])): ?>
                <div class="carousel-caption">
                    <h4>Przesłał(a):
                        <a href="<?php echo $img['author']['url'];?>" target="_blank">
                        <img src="<?php echo $img['author']['photo'];?>"/> <?php echo $img['author']['firstname'];?> <?php echo $img['author']['lastname'];?>
                        </a>
                    </h4>
                    
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach;?>
            
            <div class="item <?php if(!count($images)) echo 'active';?>">
                
		<form id="upload" method="post" action="" enctype="multipart/form-data">
                    <div id="drop">
                            <a rel="<?php echo $church->id;?>">Dodaj zdjęcie</a>
                            <input type="file" name="upl" multiple xaccept="image/*" capture="camera"/>
                    </div>

                    <ul>
                            <!-- The file uploads will be shown here -->
                    </ul>

		</form>
                
                <img src="../img/dodaj.jpg"/>
                
            </div>
            


          </div>
          <!-- Carousel nav -->
          <a class="carousel-control left" href="#churchCarousel" data-slide="prev">&lsaquo;</a>
          <a class="carousel-control right" href="#churchCarousel" data-slide="next">&rsaquo;</a>
        </div>        
        
      </div>
      
      <div class="col-sm-6">
        <h1>
            <?php if ($church->www) echo '<a target="_blank" href="http://'.$church->www.'">';?>
            <?php echo $church->name;?>
            <?php if ($church->www) echo '</a>';?>

        </h1>
        <h2><?php echo $church->address;?></h2>
        <?php if ($church->phone): ?>
            <h2>Tel.: <a href="tel:<?php echo $church->tel;?>"><?php echo $church->phone;?></a></h2>
        <?php endif; ?>
        <?php if ($church->rector): ?>
            <h3><b>Proboszcz:</b>
                <?php if ($church->email) echo '<a href="mailto:'.$church->email.'">';?>
                <?php echo $church->rector?:'email'; ?>
                <?php if ($church->email) echo '</a>';?>
            </h3>
        <?php endif; ?>
        <h3><b>Msze św:</b></h3>
        <?php if ($church->sun): ?>
            <h3><b>Niedziele i święta:</b> <?php echo $church->sun; ?></h3>
        <?php endif; ?>        
        <?php if ($church->week): ?>
            <h3><b>Dni powszednie:</b> <?php echo $church->week; ?></h3>
        <?php endif; ?>
        <?php if ($church->fest): ?>
            <h3><b>Święta zniesione:</b> <?php echo $church->fest; ?></h3>
        <?php endif; ?>
        
        <div class="church-map" title="<?php echo $church->name; ?>" lat="<?php echo $church->lat;?>" lng="<?php echo $church->lng;?>"></div>
      
        <div class="church-update">
            <a href="../edit/<?php echo $church->id; ?>" class="a_update a_bottom">Aktualizuj dane</a>
            <?php if ($church->change_author): ?>
            Aktualizował(a): <a href="<?php echo $change_author['url']?>" target="_blank">
                <img src="<?php echo $change_author['photo']?>"/>
                <?php echo $change_author['firstname']?> <?php echo $change_author['lastname']?>
            </a>
            <?php endif;?>
        </div>
        </div>
    </div>  
  
  </div>


<?php include __DIR__.'/../html/footer.phtml';?> 
</body>
</html>
