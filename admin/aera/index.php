<?php
require_once __DIR__.'/../base.php';

ini_set('max_execution_time',30000);

$church=new churchModel();
$aera=new aeraModel();
$churches=$church->getAll();

$lp=0;
echo "START<br>\n";
foreach($churches AS $ch)
{
    if (++$lp%100==0) echo "$lp<br>\n";
    
    $aera_id=$aera->find($ch['lat'],$ch['lng']);
    $aera->add($ch['id'],$aera_id);
}


