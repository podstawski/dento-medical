<?php

class churchController extends Controller {
    
    protected static $dows=['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'];
    
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
    
        $distance=Bootstrap::$main->getConfig('church.search.distance');
        $data=[];
    
        $geo=explode(',',$this->data('geo'));
        
        if (!isset($geo[1]))
        {
            $geo=Tools::geoip();
            $geo=@[$geo['location']['latitude'],$geo['location']['longitude']];
        }
        

        if ($geo[0]+0 && isset($geo[1]) && $geo[1]+0)
        {
            $church=new churchModel();
        
        
            $when=strtotime(date('Y-m-d'));
            if ($this->data('date_submit')) $when=strtotime($this->data('date_submit'));
            
            $time=$this->data('now')?:date('H:i');
            if ($this->data('date_submit')) $time='05:30';
            if ($this->data('time_submit')) $time=$this->data('time_submit');
            
            $data=[];
            $safeguard=0;
            
            
            if ($this->data('when')) {
                $when=$this->data('when');
                $time=date('H:i',$when);
            }
            
            $time=$this->time2int($time)-1;
            
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
        }
    }
    
    protected function int2time($i)
    {
        $delta = Bootstrap::$main->appengine ? 3600 : 0;
        return date('H:i',$i+$delta);
    }
    
    protected function time2int($time)
    {
        
        $t=explode(':',$time);
        if (strlen($t[0])==1) $t[0]='0'.$t[0];
        if (strlen($t[1])==1) $t[1]='0'.$t[1];
        $time=implode(':',$t);
        
        $delta = Bootstrap::$main->appengine ? 3600 : 0;
        return strtotime("1970-01-01 $time")-$delta;
    }
    

    public function post()
    {
        if (!isset(Bootstrap::$main->user['id'])) return $this->status('Należy być zalogowanym',false);
        
        $file='church-pending/'.date('Ymd-His-').Tools::str_to_url($this->data('name')).','.$this->id.'.json';
        $this->data['change_author']=Bootstrap::$main->user['id'];
        $this->data['change_time']=Bootstrap::$main->now;
        $this->data['change_ip']=Bootstrap::$main->ip;
        $this->data['trust']=false;
        Tools::save($file,json_encode($this->data,JSON_NUMERIC_CHECK));
        return $this->status('Po weryfikacji, dane zostaną opublikowane. Dziękuję!');
    }
}