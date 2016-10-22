<?php

class churchController extends Controller {
    
    protected static $dows=['niedziela','poniedziałek','wtorek','środa','czwartek','piątek','sobota'];
    
    protected function change_dow($when)
    {
        
        $dow=date('w',$when);
        if ($dow==0) return $dow;
        
        $year=date('Y',$when);
        $d=0+date('d',$when);
        $m=0+date('m',$when);
        
        $sundays=array_merge(Bootstrap::$main->getConfig('fest.all.free'),Bootstrap::$main->getConfig('fest.'.$year.'.free')?:[]);
        
        foreach($sundays AS $sunday)
        {
            $s=explode('.',$sunday);
            if ($d==$s[0] && $m==$s[1]) $dow=0;
        }
        
        if ($dow==0) return $dow;
        
        $fests=array_merge(Bootstrap::$main->getConfig('fest.all.work'),Bootstrap::$main->getConfig('fest.'.$year.'.work')?:[]);
        
        foreach($fests AS $fest)
        {
            $s=explode('.',$fest);
            if ($d==$s[0] && $m==$s[1]) $dow=8;
        }        
        
        return $dow;
        
    }
    
    public function get_search()
    {
        $opt=$this->nav_array(Bootstrap::$main->getConfig('church.search.limit'));
    
        $opt['pri']=$this->data('pri')+0;
        $distance=Bootstrap::$main->getConfig('church.search.distance');
        $data=[];
    
        $church=new churchModel();
    
        $geo=explode(',',$this->data('geo'));
        
        if (!isset($geo[1]))
        {
            $geo=Tools::geoip();
            $geo=@[$geo['location']['latitude'],$geo['location']['longitude']];
            
            $warsaw=Bootstrap::$main->getConfig('pl.center');
            if (abs($church->distance($geo[0],$geo[1],$warsaw[0],$warsaw[1]))>1000) $geo=$warsaw;
        
        }

        if ($geo[0]+0 && isset($geo[1]) && $geo[1]+0)
        {
            
            $when=strtotime(date('Y-m-d'));
            if ($this->data('date_submit')) $when=strtotime($this->data('date_submit'));
            
            $time=$this->data('now')?:date('H:i');
            if ($this->data('date_submit')) $time='05:30';
            if ($this->data('time_submit')) $time=$this->data('time_submit');
            
            $data=[];
            $safeguard=0;
            
            
            if ($this->data('when')) {
                $when=$this->data('when');
                //$time=date('H:i',$when);
                $time=$this->int2time($when);
            }
            
            $time=$this->time2int($time)-1;
            
            $token='geo_churches_'.$geo[0].'_'.$geo[1];
            $all_churches=Tools::memcache($token);
            if (!$all_churches) {
                $all_churches=$church->search_no_mass($geo[0],$geo[1],$distance);
                if ($all_churches) Tools::memcache($token,$all_churches);
            }
            
            $masses=new massModel();
            
            while (!count($data))
            {
                $month=date('m',$when);
                $dow=date('w',$when);
                $dow_requested=$dow;
                $dow=$this->change_dow($when);
            
                if ($safeguard) $time=$this->time2int('5:30');
    
                
                $data=$church->search($geo[0],$geo[1],$distance,$time,$month,$dow,$time,$opt['limit'],$opt['offset'])?:[];
                if (!count($data) && $dow!=$dow_requested)
                    $data=$church->search($geo[0],$geo[1],$distance,$time,$month,$dow_requested,$time,$opt['limit'],$opt['offset'])?:[];
                
                if ($this->data('when')) break;
                if (++$safeguard>7) break;
                if (count($data)) break;
                
                $when+=24*3600;

            }
            
            
            if ($all_churches && count($all_churches) ) {
                
                
                if ( ($data && count($data) && count($data)<$opt['limit'])
                     ||
                     ($data && count($data)==0 && $opt['offset']>0)
                ) {
                    foreach ($all_churches AS $i=>$church2) {
                        
                        $all_churches[$i]['description']='';
                        $all_churches[$i]['kids']='';
                        $all_churches[$i]['youth']='';
                        
                        
                        $masstoken='masses_'.$church2['church_id'].'_'.$dow;
                        $masscount=Tools::memcache($masstoken);
                        if (!$masscount) {
                            $masscount=$masses->count([
                                'church'=>$church2['church_id'],
                                'dow'=>$dow
                            ]);
                            Tools::memcache($masstoken,$masscount);
                        }
                        
                        
                        if ($masscount) {
                            unset($all_churches[$i]);
                            continue;
                        }
                        
                        $all_churches[$i]['nomassthisday']=1;
                        
                        
                    }
                    
                    if (count($all_churches)) $data=array_merge($data,$all_churches);
                    //mydie([count($data),$opt['limit'],count($all_churches),$masscount]);
                }
                
                
                
                
            }
            
            
            $opt['when']=$this->data('when')?:$when+$time;
            $opt['count']=count($data);
            
            
            $this->clear_data($data,true,self::$dows[$dow_requested]);
            
        }
    
        //mydie($data);
    
        return array('status'=>true,'options'=>$opt,'data'=>$data);
    }
    
    
    protected function clear_data(&$data,$geo=false,$downame='')
    {
        foreach ($data AS &$rec)
        {
            $rec['name_url']=Tools::str_to_url($rec['name']);
            if (isset($rec['time'])) $rec['time']=$this->int2time($rec['time']);
            if (isset($rec['distance'])) $rec['distance']=round($rec['distance']);
            unset($rec['password']);
            unset($rec['md5hash']);
            if ($geo) {
                unset($rec['lat']);
                unset($rec['lng']);
            }
            $rec['address']=preg_replace('/[0-9][0-9].[0-9][0-9][0-9]/','',$rec['address']);
            if ($downame) $rec['downame']=$downame;
            
            if (!isset($rec['time'])) {
                $rec['time']='-';
                $rec['description']=$downame.' brak';
        
            }
            
            if (!isset($rec['nomassthisday'])) {
                $rec['nomassthisday']=0;
            }
        }
    }
    
    protected function int2time($i)
    {
        $delta = Bootstrap::$main->appengine ? 3600 : 0;
        $delta = 3600;
        return date('H:i',$i+$delta);
    }
    
    protected function time2int($time)
    {
        
        $t=explode(':',$time);
        if (strlen($t[0])==1) $t[0]='0'.$t[0];
        if (strlen($t[1])==1) $t[1]='0'.$t[1];
        $time=implode(':',$t);
        
        $delta = Bootstrap::$main->appengine ? 3600 : 0;
        $delta = 3600;
        return strtotime("1970-01-01 $time")-$delta;
    }
    

    public function post()
    {
        if (!isset(Bootstrap::$main->user['id'])) return $this->status(['info'=>'Należy być zalogowanym','url'=>''],false);
        
        $file='church-pending/'.date('Ymd-His-').Tools::str_to_url($this->data('name')).','.$this->id.'.json';
        $this->data['change_author']=Bootstrap::$main->user['id'];
        $this->data['change_time']=Bootstrap::$main->now;
        $this->data['change_ip']=Bootstrap::$main->ip;
        $this->data['trust']=false;
        Tools::save($file,json_encode($this->data,JSON_NUMERIC_CHECK));
        
        $church=new churchModel($this->id);
        
        if ($church->change_author == Bootstrap::$main->user['id'] && !$church->active)
        {
            foreach($this->data AS $k=>$v) {
                if (in_array($k,['id','change_author','change_time','change_ip','md5hash','active'])) continue;
                if (is_array($v)) continue;
                $church->$k=$v;
            }
            $church->save();
        }
        
        $url=Tools::str_to_url($church->name).','.$church->id;
        
        Tools::mail([
            'from'=>Bootstrap::$main->user['email'],
            'to'=>'piotr.podstawski@kiedymsza.pl',
            'subject' => 'Akceptuj '.$church->name,
            'msg'=>'Zmiana do akceptacji, wejdź na https://www.kiedymsza.pl/admin/pending/<br/><br/>'.Bootstrap::$main->user['firstname'].' '.Bootstrap::$main->user['lastname'].' ['.Bootstrap::$main->user['trust'].']'
        ]);
        
        return $this->status(['info'=>'Po weryfikacji, dane zostaną opublikowane. Dziękuję!','url'=>$url]);
    }
    
    public function post_route()
    {
        $masses=[];
        $points=[];
        $last_distance=0;
        $last_time=0;
        
        $church=new churchModel();
        
        $date_submit=$this->data('date_submit')?:date('Y-m-d');
        $dow=$this->change_dow(strtotime($date_submit));
        $month=date('m',strtotime($date_submit));
        
        $time=$this->data('time_submit')?:$this->data('now');
        if ($this->data('date_submit')) $time='07:00';
        if ($this->data('time_submit')) $time=$this->data('time_submit');
        
        $time_org=$time;
              
        
        
        foreach ($this->data('steps')?:[] AS $step)
        {
            $distance=$step['distance']['value'];
            $time=$step['duration']['value'];
            
            //mydie($step['lat_lngs'],$distance);
            $i=1;
            foreach($step['lat_lngs'] AS $latlng)
            {
                if (!is_array($latlng)) continue;
                if (count($latlng)<2) continue;
                
                $points[$last_distance+round($i*$distance/count($step['lat_lngs']))] = [
                    'time'=>$last_time+round($i*$time/count($step['lat_lngs'])),
                    'latlng'=>$latlng];
                $i++;
            }
            
            $last_distance+=$distance;
            $last_time+=$time;
        }
        
        $time=$this->time2int($time_org)-1;  
        $last_distance=0;
        $step=1000;
        if (count($points)>1000) $step+=count($points);
  
        
        foreach ($points AS $distance=>$point)
        {
            if ($distance-$last_distance<$step) continue;
            $last_distance=$distance;
            
            $mass=$church->search($point['latlng'][0],$point['latlng'][1],$step/1000,$time+$point['time'],$month,$dow,$time+$point['time'],1,0)?:[];
            
            if (count($mass) && !isset($masses[$mass[0]['church_id']]) )
            {
                $time_difference=$mass[0]['time']-$time-$point['time'];
                $mass[0]['time_difference']=$time_difference;
                $this->clear_data($mass);
                
                $church_id=$mass[0]['church_id'];
                
                if (!isset($masses[$church_id]) || $masses[$church_id]['distance']>$mass[0]['distance']) $masses[$church_id] = $mass[0];
            }
            
        }
        
        $masses2=[];
        $distances=0;
        foreach($masses AS $mass)
        {
            $time_difference=$mass['time_difference'];
            while(isset($masses2[$time_difference])) $time_difference++;
            $masses2[$time_difference]=$mass;
            $distances+=$mass['distance'];
        }
        if (count($masses)) $avg_distance=$distances/count($masses);
        
        ksort($masses2);
        
        $i=0;
        $ak2=array_keys($masses2);
        foreach($ak2 AS $k) {
            $masses2[$k]['time_difference']=round($masses2[$k]['time_difference']/60);
            if ($i++>=2 && $masses2[$k]['distance'] > $avg_distance) unset($masses2[$k]);
            if ($i>9) unset($masses2[$k]);
        }
        
        return $this->status($masses2,true,'churches');
    }
}