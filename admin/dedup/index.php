<?php
    $title='Deduplication';
    $menu='debup';
    include __DIR__.'/../base.php';
    
    
    
    
    if (isset($_GET['trash'])) $church1=@new churchModel(end(explode(',',$_GET['trash'])));
    if (isset($_GET['dest'])) $church2=@new churchModel(end(explode(',',$_GET['dest'])));
    
    include __DIR__.'/../head.php';
  
    if (isset($church1) && $church1->id && isset($church2) && $church2->id)
    {
        $church1->successor = $church2->id;
        $church1->save();
    }
    
    
    
?>

<form method="GET">
    <input type="text" name="trash" placeholder="kościół do kosza" value="<?php if (isset($_GET['trash'])) echo $_GET['trash'];?>"/>
    &raquo;
    <input type="text" name="dest" placeholder="kościół docelowy" value="<?php if (isset($_GET['dest'])) echo $_GET['dest'];?>"/>
    
    <input type="submit" value="ok"/>
</form>


<?php
    
    include __DIR__.'/../foot.php';
    