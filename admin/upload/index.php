<?php
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    include_once __DIR__.'/upload.php';
    
    $church=new churchModel();
    $searches=0;
    
    if (isset($_GET['id']) && $_GET['latlng']) {
        $church->get($_GET['id']);
        $latlng=explode(',',$_GET['latlng']);
        $church->lat=$latlng[0];
        $church->lng=$latlng[1];
        $church->save();
    }
    
    
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
        ini_set('max_execution_time',300);

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
            $line=explode("|",przecinek2strumien($data[$iii]));
            $rec=[];
            
            foreach($line AS $i=>$r) $rec[$header[$i]]=$r;
            
            if (!$rec['phone']) continue;
            if (!$rec['address']) continue;
            
            $phone=preg_replace('/[^0-9]/','',$rec['phone']);
            $md5hash=md5('PL'.substr($phone,0,9));
            
            $ch=$church->find_one_by_md5hash($md5hash);
            
            if (!isset($ch['id'])) {
                $church->load(['name'=>$rec['name'],'md5hash'=>$md5hash,'country'=>'PL'],true);
                $church->save();
            }
            
            if (strlen($rec['address'])<8) continue;
            
            $matches=[];
            preg_match('/([0-9][0-9]\-[0-9][0-9][0-9])/',$rec['address'],$matches);
            $postal='';
            if (isset($matches[1])) $postal=$matches[1];
            
            if (!$rec['latlng'] && (!$church->lat || !$church->lng) )
            {
                if (++$searches==10) break;
                
                echo "Searching google maps for ".$rec['name'].' / '.$rec['address'].'<br/>';
                
                $options=[];
                $latlng=find_latlng($postal,$rec['address']);
                if (!$latlng['latlng']) {
                    if (isset($latlng['addresses'])) $options=array_merge($options,$latlng['addresses']);
                
                    $latlng=find_latlng($postal,$rec['name'].', '.$rec['address']);
                    if (!$latlng['latlng']) {
                        if (isset($latlng['addresses'])) $options=array_merge($options,$latlng['addresses']);
                        $latlng=find_latlng($postal,$rec['name'].', '.$postal);
                        if (!$latlng['latlng']) {
                            if (isset($latlng['addresses'])) $options=array_merge($options,$latlng['addresses']);
                        }                
                
                    }                
                }
                
                if ($latlng['latlng']) $rec['latlng']=$latlng['latlng'];
                
                if (!$latlng['latlng'] && count($options))
                    die('<pre>'.print_r($rec,1).'</pre>'.form($key,$church->id,$options));
            }
            
            $church->tel=$phone;
            foreach ($rec AS $k=>$v) $church->$k=$v;
            if ($rec['latlng']){
                $latlng=explode(',',$rec['latlng']);
                if ($latlng[0] && $latlng[1]) {
                    $church->lat=$latlng[0];
                    $church->lng=$latlng[1];
                }
            }
            $church->save();
        }
        
        
    }
    
?>

<form method="get">
    <input type="hidden" name="reread" value="0"/>
    <input type="checkbox" name="reread" value="1" <?php if (isset($_GET['reread']) && $_GET['reread']) echo 'checked';?>/>
    Re-Read
    <input type="text" value="<?php echo $key?>" name="key" placeholder="spreadsheet key"/><input type="submit" value="go!" />
</form>

<?php
    include __DIR__.'/../foot.php';
    