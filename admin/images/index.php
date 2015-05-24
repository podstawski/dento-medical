<?php
    $title='Images moderation';
    $menu='images';
    include __DIR__.'/../base.php';
    
    $image=new imageModel();
    $user=new userModel();
    
    if (isset($_GET['trust'])) $image->activateTrusted($_GET['trust']);
    
    if (isset($_GET['mod']) && is_array($_GET['mod'])) {
        foreach ($_GET['mod'] AS $id=>$active)
        {
            $image->get($id);
            $image->active=$active;
            $image->save();
            $user->get($image->author_id);
            if ($active) $user->trust++;
            $user->save();
        }
        die('OK');
    }
    
    include __DIR__.'/../head.php';
  
    
    
    $images=$image->select(['images.active'=>null])?:[];
    
    
?>
<style>
    .mod-box {
        position: absolute;
        width:150px;
        display: inline-block;
        padding:4px;
        background-color: rgba(0,0,0,0.5);
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
<span>
<div class="mod-box" rel="<?php echo $img['id'];?>">
    <a class="no">NO</a>
    <a class="yes">YES</a>
</div>
<a class="fancybox" href="<?php echo $img['url'];?>">
    <img class="mod-img" src="<?php echo $img['thumb'];?>"/>
</a>
</span>
<?php endforeach;?>

<div class="all">
    <input type="button" class="b-no" value="No to all"/>
    <input type="button" class="b-yes" value="Yes to all"/>
    
</div>

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
    