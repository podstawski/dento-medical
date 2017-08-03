<?php
    $title='Pending churches';
    $menu='pending';
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    function time2int($time)
    {
        $time=trim($time);
        if (!$time) return 0;
        
        $time=str_replace('.',':',$time);
        $time=str_replace(',',':',$time);
        $time=str_replace(' ',':',$time);
        if (!strstr($time,':') && strlen($time)<3) $time.=':00';
        
        $ret=strtotime("1970-01-01 $time");
        if (Bootstrap::$main->appengine) $ret-=3600;
        return $ret;
    }

    function mail2changer($church_id,$only_zero_masses=false) {
        
        $church=new churchModel($church_id);
        if (!$church->change_author) return;
        
        if (!$church->name) return;
        if (!$church->active) return;
        if ($church->successor) return;
        
        $user=new userModel($church->change_author);
        if (!$user->email) return;
        if (!strstr($user->email,'@')) return;
        
        $mass=new massModel();
        
        $masses=$mass->count(['church'=>$church_id]);
        
        if ($only_zero_masses && $masses) return;
        
        $msg=Smekta::smektuj(file_get_contents(__DIR__.'/mail.html'),[
            'church'=>$church->data(),
            'user'=>$user->data(),
            'masses'=>$masses,
            'link'=>'https://www.kiedymsza.pl/kosciol/'.Tools::str_to_url($church->name).','.$church_id
        ]);
        
        //echo $church->name.'<br>'; return;
  
        Tools::mail([
            'from'=>'piotr.podstawski@kiedymsza.pl',
            'to'=>$user->email,
            'subject' => 'Zmiana '.$church->name,
            'msg'=>$msg
        ]);

        echo $msg;
    }
    
    //if (isset($_GET['sendmail'])) mail2changer($_GET['sendmail']);
    
    $church=new churchModel();
    $mass=new massModel();
    $path=Tools::saveRoot('church-pending');
    
    $church->cleanup(Bootstrap::$main->now - 3600*24*5);

    //$churches=$church->updated(); foreach($churches AS $ch) mail2changer($ch['id'],true);
    
    $user=new userModel();
    
    echo '<div><ul id="churches">';
    
    foreach (scandir($path) AS $f)
    {
        if ($f[0]=='.') continue;
    
        
        $fname=str_replace('.json','',$f);
        $id=@end(explode(',',$fname));
        if (!$id) {
            unlink("$path/$f");
            continue;
        }
        
        $church1=$church->get($id);
        $church2=json_decode(file_get_contents("$path/$f"),true);
        
        if (!isset($_GET['trust']) && isset($church2['trust']) && $church2['trust']) continue;
        
        $masses1=$mass->select(['church'=>$id])?:[];
        
        foreach(array_keys($masses1) AS $i) {
            unset($masses1[$i]['id']);
            unset($masses1[$i]['church']);
            
        }

        
        $user->get($church2['change_author']);
        
        $masses2=[];
        foreach ($church2['masses'] AS $dow=>$times)
        {
            foreach ($times AS $time=>$props)
            {
                if (strstr("$time",'_new_'))
                {
                    if (!isset($props['time'])) continue;
                    $time=time2int($props['time']);
                    
                }
                if (!$time) continue;
                
                $description=isset($props['desc'])?$props['desc']:null;
                $kids=isset($props['kids'])?1:null;
                $youth=isset($props['youth'])?1:null;
                
                if (isset($props['moy'])) foreach(array_keys($props['moy']) AS $moy)
                {
                    $masses2[]=[
                        'church'=>$id,
                        'moy'=>$moy,
                        'dow'=>$dow,
                        'time'=>$time,
                        'kids'=>$kids,
                        'youth'=>$youth,
                        'description'=>$description
                    ];
                    
                }
                
            }
        }
        
        if (!isset($church1['id']))
        {
            unlink("$path/$f");
            continue;
        }
        
        if (isset($_GET['trust']) && $_GET['trust']>0)
        {
            
            if ((isset($church2['trust']) && $church2['trust']) || ($user->trust>=$_GET['trust'] && (!$church1['change_author'] || $church1['change_author']==$user->id)))
            {
                $arch='arch/'.$church1['md5hash'].':'.$id.'/'.date('Ymd-His').'.json';
                $realarch=Tools::saveRoot($arch);
                $fh=fopen($realarch,'w');
                $church->export($fh,$id);
                fclose($fh);
                if (isset($church2['id'])) unset($church2['id']);
                if (isset($church2['password'])) unset($church2['password']);
                $church2['masses']=$masses2;
                $church2['md5hash']=$church1['md5hash'];
                $church2['tel']=substr(preg_replace('/[^0-9]/','',$church2['phone']),0,9);
                $church2['active']=1;
                
                $changed=$church->import($church2,true,true,true);
                
                mail2changer($changed['data']['id']);

                unlink("$path/$f");
                
            }
            
            continue;
        }
        
        if (isset($_GET[md5($f)]))
        {
            if ($_GET[md5($f)]==1) {
                $user->trust++;
                $church2['trust']=true;
                file_put_contents("$path/$f",json_encode($church2,JSON_NUMERIC_CHECK));
            }
            if ($_GET[md5($f)]==0) {
                $user->trust--;
                unlink("$path/$f");
            }
            
            $user->save();
            
            
            
            continue;
        }
        
        //mydie($church1);
        
        //mydie(array_diff($masses1,$masses2));
        
        echo '<li class="row">';
        echo '<div class="col-md-9 col-sm-9">';
        
        echo '<h3>';
        echo '<a href="https://www.google.pl/maps/place/'.$church2['lat'].','.$church2['lng'].'" target="_blank">';
        echo '<img src="../../img/gmap_icon.png" align="absMiddle"/></a>';
        echo ' <a href="../../kosciol/'.Tools::str_to_url($church1['name']).','.$id.'" target="_blank">';
        echo $church1['name'];
        if ($church1['name']!=$church2['name']) echo ' &raquo; '.$church2['name'];
        echo '</a>';
        
        echo '</h3>';
        
        echo '<h4>';
        echo '<a target="_blank" href="'.$user->url.'">';
        echo $user->firstname.' '.$user->lastname;
        echo '</a> (';
        echo '<a href="mailto:'.$user->email.'">';
        echo date('d-m-Y H:i',$church2['change_time']);
        echo '</a>)</h4>';
        
        
        foreach ($church1 AS $k=>$v)
        {
            foreach (['id','md5hash','postal','country','lat','lng','tel','active','password'] AS $kk)
            {
                if ($k==$kk) {
                    continue 2;
                }
            }
            if (strstr($k,'change_')) continue;
            if (!isset($church2[$k])) $church2[$k]=null;
            
            if ($v!=$church2[$k])  echo '<h5 title="'.$k.'">'.$v.' &raquo; '.$church2[$k].'</h5>';
        }
        $distance=$church->distance($church1['lat'],$church1['lng'],$church2['lat'],$church2['lng']);
        if ($distance>0.0001) echo '<h5>geo move='.$distance.' km</h5>';
        
        if (count($masses1) != count($masses2)){
            echo '<h5>masses: '.count($masses1).' &raquo; '.count($masses2).'</h5>';
        }
        
        echo '</div>';
        
        echo '<div class="col-md-3 col-sm-3">';
        echo '<a class="button" href="index.php?'.md5($f).'=1">TAK</a>';
        echo '<a class="button button-red" href="index.php?'.md5($f).'=0">NIE</a>';
        echo '</div>';
        echo '</li>';
        //<a href=\"$uri?f=$f\">$f</a></li>';
    }
    
    echo '</ul></div>';
    
?>
    
<?php
    $church->no_http_prefix();
    if (isset($_GET['trust']) && $_GET['trust']>0) $user->rank();
    
    include __DIR__.'/../foot.php';
    