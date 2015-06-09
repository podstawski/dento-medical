<?php
    $title='Import';
    $menu='import';
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    ini_set('max_execution_time',30000);
    
    $church=new churchModel();
    $path=Tools::saveRoot('export');
    
    
    if (isset($_GET['f']) && file_exists($path.'/'.$_GET['f']))
    {
        $lp=0;
        $restore_masses=isset($_GET['masses']) && $_GET['masses'];
        $handle = fopen($path.'/'.$_GET['f'], "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $data=json_decode($line,true);
                
                //if (!strstr($data['address'],'Rybnik')) continue;
                $lp++;
                $c=$church->import($data,$restore_masses);
                $d=$c['data'];
            
                echo '<p>'.$lp.'. '.$d['name'].' ['.$d['address'].'] <font color="red"><b>'.($c['newchurch']?'NEW':'').'</b></font> ... '.$d['id'].'</p>';
                
            }

            fclose($handle);
        }

    }
    
    
    $uri=$_SERVER['REQUEST_URI'];
    if ($pos=strpos($uri,'?')) $uri=substr($uri,0,$pos);

    echo '<ul class="import">';
    
    foreach (scandir($path) AS $f)
    {
        if ($f[0]=='.') continue;
        echo "<li><a href=\"$uri?f=$f\">$f</a></li>";
    }
    
    echo '</ul>';
    
?>
<div>
<input type="checkbox" class="chk"> Restore masses
</div>

<script>
    $('.chk').click(function() {
        var masses=$(this).prop('checked')?'1':'0';
        $('.import a').each(function() {
            $(this).attr('href',$(this).attr('href')+'&masses='+masses);
        });
    });
</script>
    
<?php
    
    include __DIR__.'/../foot.php';
    