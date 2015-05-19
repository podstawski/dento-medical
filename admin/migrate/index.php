<?php
    $title='Migration tool';
    $menu='migrate';

    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    include __DIR__.'/../../rest/library/backend/include/migrate.php';
    
    $ver=null;
    if (isset($_GET['ver'])) $ver=$_GET['ver'];
    $ver=backend_migrate(__DIR__.'/../../rest/configs/application.json',__DIR__.'/../../rest/migrate/classes',$ver);
    
?>

<form method="get">
    <input type="text" value="<?php echo $ver?>" name="ver" size="2"/><input type="submit" value="go!" />
</form>

<?php
    
    include __DIR__.'/../foot.php';
    