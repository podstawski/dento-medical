<?php
    $title='Images moderation';
    $menu='images';
    include __DIR__.'/../base.php';
    
    $image=new imageModel();
    $user=new userModel();
    
    $codeOrigin='fly';
    if (isset($_GET['trust'])) $image->activateTrusted($_GET['trust'],$codeOrigin);
    
    if (isset($_GET['mod']) && is_array($_GET['mod'])) {
        $code=new codeModel();
        foreach ($_GET['mod'] AS $id=>$active)
        {
            $image->get($id);
            $image->active=$active;
            $image->code=$code->getCode($codeOrigin);
            $image->save();
            $user->get($image->author_id);
            if ($active) $user->trust++;
            $user->save();
        }
        die('OK');
    }
    
    include __DIR__.'/../head.php';
  
    
    
    $images=$image->select(['active'=>null])?:[];
    
    $images2=[];
    $images2_cmd='';
    
    if (isset($_GET['from']) && isset($_GET['to']))
    {
        $where=['images.active'=>1];
        if ($_GET['from']) $where['d_uploaded']=['>=',strtotime($_GET['from'])];
        if ($_GET['to']) $where['d_uploaded ']=['<=',strtotime($_GET['to'])];
        $images2=$image->select($where)?:[];
    }
   
    foreach ($images AS $i=>$img) {
	if ($img['lat'])
		$images[$i]['mapurl']='../../mapa/?m='.$img['lat'].','.$img['lng'].',14';
	if ($img['church']<=0)
		continue;
	$church=new churchModel($img['church']);
	$images[$i]['churl']='../../kosciol/'.Tools::str_to_url($church->name).','.$church->id;
    }
    
?>
<style>
    .square {
	position: relative;
	float: left;
	margin: 4px;
    }
    .mod-box {
        position: absolute;
        width:150px;
        display: inline-block;
        padding:4px;
        background-color: rgba(0,0,0,0.5);
    }

    .link {
        position: absolute;
        width:150px;
        display: inline-block;
	bottom: 0;
        padding:4px;
        background-color: rgba(0,0,0,0.5);
	text-align: center;
    }
    .link a {
	text-decoration: none;
	color: white;
	font-size: 11px;
    }
    
    .mod-box a.no {
        float: right;
        
    }
    .mod-box a {
        color:#fff;
        cursor: pointer;
    }
    
    .mod-img {
        width:150px;
    }
    
    .all {
        margin: 3em;
    }
    
    .b-no {
        float: right;
    }
    
</style>

<link rel="stylesheet" href="../../css/jquery.fancybox.css" type="text/css" media="screen" />
<script type="text/javascript" src="../../js/jquery.fancybox.js"></script>


<?php foreach($images AS $img): ?>
<div class="square">
<div class="mod-box" rel="<?php echo $img['id'];?>">
    <a class="no">NO</a>
    <a class="yes">YES</a>
</div>
<div class="link">
	<a target="_blank" href="<?php echo $img['churl'];?>">LINK</a>
	&nbsp;
	<a target="_blank" href="<?php echo $img['mapurl'];?>">MAPA</a>
</div>
<a class="fancybox" href="<?php echo $img['url'];?>" title="<?php
    $user->get($img['author_id']);
    echo $user->firstname.' '.$user->lastname;
?>">
    <img class="mod-img" src="<?php echo $img['thumb'];?>"/>
</a>
</div>
<?php endforeach;?>

<br clear="all"/>


<div class="all" style="<?php if (!count($images)) echo 'display:none';?>">
    <input type="button" class="b-no" value="No to all"/>
    <input type="button" class="b-yes" value="Yes to all"/>
    
</div>

<div class="all" style="<?php if (count($images)) echo 'display:none';?>">
    <form>
    <input type="date" value="<?php echo isset($_GET['from'])?$_GET['from']:''?>" placeholder="uploaded from" name="from"/>
    <input type="date" value="<?php echo isset($_GET['to'])?$_GET['to']:''?>" placeholder="uploaded to" name="to"/>
    <input type="submit" value="search"/>
    </form>
</div>

<?php foreach($images2 AS $img): ?>
<span>
<a class="fancybox" href="<?php echo $img['url'];?>" title="<?php
    $user->get($img['author_id']);
    echo $user->firstname.' '.$user->lastname;
    $images2_cmd.='mv "'.$img['src'].'" '.Tools::str_to_url($user->firstname.$user->lastname).substr($user->md5hash,2).'_'.str_replace(['?','='],'',basename($img['src'])).(strstr($img['src'],'.jpg')?'':'.jpg')."\n";
?>">
    <img class="mod-img" src="<?php echo $img['thumb'];?>"/>
</a>
</span>
<?php endforeach;?>

<pre style="margin-top:20px;<?php if (!count($images2)) echo 'display:none';?>"><?php echo $images2_cmd;?></pre>


<script>
    
    $(document).ready(function() {
      $(".fancybox").fancybox();
    });    
    
    $('.mod-box a.yes').click(function () {
        $(this).parent().parent().fadeOut(1000,function(){$(this).remove()}); 

        var id=$(this).parent().attr('rel');
        $.get(location.href+'?mod['+id+']=1');
    });
    
    $('.mod-box a.no').click(function () {
        $(this).parent().parent().fadeOut(1000,function(){$(this).remove()});
        
        var id=$(this).parent().attr('rel');
        $.get(location.href+'?mod['+id+']=0');        
    });    
    
    $('.all .b-yes').click(function () {
        $('.mod-box a.yes').click();
        
    });
    
    $('.all .b-no').click(function () {
        $('.mod-box a.no').click();
    });    
    
</script>
<?php
    
    include __DIR__.'/../foot.php';
    
