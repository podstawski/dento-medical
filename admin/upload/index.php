<?php
    $title='Upload spreadsheet';
    $menu='upload';
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    include_once __DIR__.'/upload.php';
    
    $church=new churchModel();
    $church2=new churchModel();
    
    $mass=new massModel();
    $searches=0;
    
   
    
    if (isset($_GET['id']) && ($_GET['latlng'] || $_GET['latlng2'])) {
        $church->get($_GET['id']);
        $latlng=explode(',',$_GET['latlng']?:$_GET['latlng2']);
        $church->lat=$latlng[0];
        $church->lng=$latlng[1];
        $church->save();
    }
    
    $church->deduplicate();
    
    if (isset($_GET['masses']) && $_GET['masses']==2) include __DIR__.'/masses.php';
    
    $key=isset($_GET['key'])?$_GET['key']:'';
    if ($key) {
        if (substr($key,0,8)=='https://')
        {
            $key=str_replace('?','/',$key);
            $k='';
            foreach(explode('/',$key) AS $p) if (strlen($p)>strlen($k)) $k=$p;
            $key=$k;
        }
        
        $token='spreadsheet:'.$key;
        $csv=Tools::memcache($token);
        ini_set('max_execution_time',3000);

        if (!$csv || (isset($_GET['reread']) && $_GET['reread']))
        {
            $url='https://docs.google.com/spreadsheet/ccc?key='.$key.'&output=csv';
            $csv=file_get_contents($url);
            Tools::memcache($token,$csv);
        }
        

        
        $data=explode("\n",$csv);
        $header=explode("|",przecinek2strumien($data[0]));
        

        for($iii=1;$iii<count($data);$iii++)
        {
            if (isset($_GET['startfrom']) && $iii<$_GET['startfrom']) continue;
            
            $line=explode("|",przecinek2strumien($data[$iii]));
            $rec=[];
            
            foreach($line AS $i=>$r) $rec[$header[$i]]=$r;
            
            //if (!$rec['phone']) continue;

            if (strlen($rec['address'])<8) continue;
            
            $matches=[];
            preg_match('/([0-9][0-9][\-\‑]*[0-9][0-9][0-9])/',$rec['address'],$matches);
            $postal='';
            if (isset($matches[1])) $postal=$matches[1];
            
            $postal=str_replace('‑','-',$postal);
            $rec['postal']=$postal;
            //$rec['matches']=$matches;
            
                        
            $phone=preg_replace('/[^0-9]/','',$rec['phone']);
            $postal2=preg_replace('/[^0-9]/','',$postal);
            
            $md5hash='PL'.$postal2.','.substr($phone,0,9);
            if (!$phone) $md5hash=md5($rec['address']);
            
            $latlng_known=false;
            if ($rec['latlng'] && preg_match('/^[0-9,. ]+$/',$rec['latlng']))
            {
                $latlng=explode(',',$rec['latlng']);
                $md5hash=substr($latlng[0],0,15).','.substr($latlng[1],0,15);
                $latlng_known=true;
            }
            

            
            $ch=$church->find_one_by_md5hash($md5hash);
            
            
            if (!isset($ch['id'])) {
                $church->load(['name'=>$rec['name'],'md5hash'=>$md5hash,'country'=>'PL','active'=>1],true);
                $church->save();
            }
            
            
            /*
            if (!$church->lat || !$church->lng)
            {
                $ll=$church2->find_one_by_address($rec['address']);
                if ( isset($ll['lat']) && isset($ll['lng']) )
                {
                    $church->lat=$ll['lat'];
                    $church->lng=$ll['lng'];
                }
                
            }
            */
            
            
            if ( !$latlng_known && (!strlen($church->lat) || !strlen($church->lng)) )
            {
                //if (++$searches==5) break;
                
                echo "$iii. Searching google maps for ".$rec['name'].' / '.$rec['address'].'<br/>';
                
                $options=[];
                $latlng=[];
                $latlng['latlng']=false;
                
                /*
                if ($rec['latlng']) {
                    $latlng=find_latlng($postal,$rec['latlng']);
                    $rec['latlng']='';
                }
                */
                
                
                if (!$latlng['latlng']) {
                    
                    if (isset($latlng['addresses'])) $options=array_merge($options,$latlng['addresses']);
                
                    $latlng=find_latlng($postal,$rec['address']);
                    
                    if (!$latlng['latlng']) {
                        if (isset($latlng['addresses'])) $options=array_merge($options,$latlng['addresses']);  
                    } 
                }
                
                if ($latlng['latlng']) $rec['latlng']=$latlng['latlng'];
                
                if (!$latlng['latlng'] && count($options) && $rec['email']) {
                    $options['0,0']='Nie moge znalezc';
                
                    die('<pre>'.print_r($rec,1).'</pre>'.form($key,$church->id,$options,$iii));
                }
                
                if (!$latlng['latlng'] && (!$rec['email'] || !count($options)) ) {
                    $rec['latlng']='0,0';
                }
                
            }
            
            $church->tel=$phone;
            
            $rec['sun']=deduplicate_masses($rec['sun']);
            $rec['week']=deduplicate_masses($rec['week']);
            $rec['fest']=deduplicate_masses($rec['fest']);
            
            
            foreach ($rec AS $k=>$v) $church->$k=$v;
            
            if (strlen($rec['latlng']) && preg_match('/^[0-9,. ]+$/',$rec['latlng'])){
                $latlng=explode(',',$rec['latlng']);
                if (strlen($latlng[0]) && strlen($latlng[1])) {
                    $church->lat=$latlng[0];
                    $church->lng=$latlng[1];
                }
            }
    
            $church->save();
            
            $masses=[];
            $masses=array_merge($masses,analyze_mass([0],$rec['sun']));
            $masses=array_merge($masses,analyze_mass([1,2,3,4,5,6],$rec['week']));
            $masses=array_merge($masses,analyze_mass([8],$rec['fest']));
            
            
            if (isset($_GET['masses']) && $_GET['masses']==1) add_masses($church,$masses);
        }
        
        
    }
    
?>
<a name="ff"></a>
<form method="get" action="index.php#ff">
    <input type="hidden" name="t" value="<?php echo time();?>"/>
    <input type="hidden" name="reread" value="0"/>
    <input type="checkbox" name="reread" value="1" <?php if (isset($_GET['reread']) && $_GET['reread']) echo 'checked';?>/>
    Re-Read
    | <input type="checkbox" name="masses" value="1" <?php if (isset($_GET['masses']) && $_GET['masses']) echo 'checked';?>/>
    Masses
    <br/>
    <input type="text" value="<?php echo $key?>" name="key" placeholder="spreadsheet key"/><input type="submit" value="go!" />
</form>

<?php
    include __DIR__.'/../foot.php';
    