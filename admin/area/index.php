<?php
require_once __DIR__.'/../base.php';

Header('Content-type: text/html; charset=utf-8');

function getCity($lat,$lng)
{
    $keys=['AIzaSyBMpDPg7BibacB6R8CdznzHS1cZrfLgSv0','AIzaSyBkqSKFVBFadz9ri2N-Kl3b2ZiNff_SHls',Bootstrap::$main->getConfig('maps.server_key')];
    $url='https://maps.google.com/maps/api/geocode/json?address='.urlencode($lat.','.$lng);
    $url.='&key='.Bootstrap::$main->getConfig('maps.server_key');
    //$url.='&key='.$keys[rand(0,count($keys)-1)];
    
    $token='place:'.md5($url);
    $place=Tools::memcache($token);
    if (!$place)
    {
        $place=json_decode(file_get_contents($url),true);
        if (isset($place['status']) && $place['status']=='OK') Tools::memcache($token,$place);
    }
    
    if ($place['status']!='OK') {
        //return false;
        mydie($place,$url);
    }
    
    foreach ($place['results'] AS $r) foreach($r['address_components'] AS $adr)
    {
        if ( in_array('locality',$adr['types']) )
        {
            return $adr['long_name'];
        }
    }
    
    return false;
}


ini_set('max_execution_time',30000);

$church=new churchModel();
$area=new areaModel();

//$area->deduplicate(true);


//$areas=$area->select(['name'=>null]);

$areas=$area->getAll();

//mydie($areas);
foreach($areas AS $a)
{
    $area->load($a);
    $m=$area->middle($a['id']);
    $area->lat=$m[0];
    $area->lng=$m[1];
    $area->zoom=$area->setZoom();
    $area->save();
    //mydie($area);
    continue;
    
    
    if (!$area->churches_count($a['id'])) {
        $area->remove($a['id']);
        continue;
    }
    
    $lp++;
    

    $area->load($a);
    $area->zoom=$area->setZoom();
    $area->save();
    
    if (!$a['lat']) continue;
    
    
    $name=getCity($a['lat'],$a['lng']);
    if ($name) {
        $area->name=$name;
        $area->save();
        continue;
    } else {
        $churches=[];
        foreach ($area->churches() AS $church)
        {
            $name=getCity($church['lat'],$church['lng']);
            if ($name)
            {
                if (!isset($churches[$name])) $churches[$name]=1;
                else $churches[$name]++;
            }
            
        }
        arsort($churches);
        $ak=array_keys($churches);
        if (isset($ak[0]) && $ak[0])
        {
            $area->name=$ak[0];
            $area->save();
            continue;  
        }
        
    }
    
    echo "LP:$lp, id=".$a['id'].'<br>';
}

echo $lp;

mydie($areas);

/*
$churches=$church->getAll();


echo "START<br>\n";
foreach($churches AS $ch)
{
    if (++$lp%100==0) echo "$lp<br>\n";
    
    $area_id=$area->find($ch['lat'],$ch['lng']);
    $area->add($ch['id'],$area_id);
}


*/

