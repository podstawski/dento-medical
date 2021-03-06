<?php
    $title='Reminder';
    $menu='reminder';
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    $churchusers=[];
    $users=[];
    $emails=[];
    $sent=json_decode(file_get_contents(__DIR__.'/sent.json'),true);
    
    $church=new churchModel();
    $user=new userModel();
    $template=file_get_contents(__DIR__.'/mail.html');
    
    $when=strtotime('2017-08-09');
    $when=0;
    
    $touched=$church->touched($when);
    
    foreach($touched AS &$ch) {
        $ch['authors']=$church->image_authors($ch['id']);
        if ($ch['change_author']>1 && !in_array($ch['change_author'],$ch['authors'])) $ch['authors'][]=$ch['change_author'];
    
        $ch['url']=Tools::str_to_url($ch['name']).','.$ch['id'];
    
        $ch['receiver']=[];
        $churchusers[$ch['id']]=[];
        foreach($ch['authors'] AS $author) {
            $churchusers[$ch['id']][]=$author;
            if (!isset($users[$author])) {
                $users[$author]=$user->get($author);
                $users[$author]['male'] = substr(strtolower($users[$author]['firstname']),-1)!='a';
            }
            $ch['receiver'][]=$users[$author];
        }
        
        //było -1: ostatni parametr
        $neighbours_no_mass = $church->search_no_mass($ch['lat'],$ch['lng'],15,10,0,$ch['id'],0);
        
        if ($neighbours_no_mass && count($neighbours_no_mass)>0) {
            $ch['neighbours_no_mass']=$neighbours_no_mass;
        }
    }
    
    foreach($touched AS &$ch) {
        foreach($ch['receiver'] AS $receiver) {
            if (!strstr($receiver['email'],'@')) continue;
            
            
            
            $ch['neighbours']=[];
            
            if (isset($ch['neighbours_no_mass'])) {
                $neighbours=$ch['neighbours_no_mass'];
                foreach($neighbours AS $i=>$neighbour) {
                    $neighbours[$i]['url']=Tools::str_to_url($neighbour['name']).','.$neighbour['id'];
                    if (isset($churchusers[$neighbour['id']]) && in_array($receiver['id'],$churchusers[$neighbour['id']]) )
                        unset($neighbours[$i]);    
                }
                
                $ch['neighbours'] = $neighbours;
                
            }
            
            //if ($ch['masses']>0 || count($ch['neighbours'])==0) continue;
            if(count($ch['neighbours'])==0) $ch['neighbours']=false;
            
            $ch['user']=$receiver;
            
            //$sent[$receiver['email']]=true;
            if (isset($sent[$receiver['email']])) continue;
            
            $emails[]=[
                'sent'=>false,
                'to'=>$receiver['email'],
                'subject'=>$ch['name'],
                'masses'=>$ch['masses']>0,
                'mail'=>Smekta::smektuj($template,$ch)
            ];
            
            //die($emails[0]['mail']);
        }

    }
    echo count($emails);
    file_put_contents(__DIR__.'/newsletter.json',json_encode($emails,JSON_NUMERIC_CHECK));
    file_put_contents(__DIR__.'/sent.json',json_encode($sent,JSON_NUMERIC_CHECK));
    
    //mydie($emails);
    //mydie($touched);
?>


<?php

    
    include __DIR__.'/../foot.php';
    