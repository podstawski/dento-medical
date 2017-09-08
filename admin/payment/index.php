<?php

    $title='Add a payment';
    $menu='payment';
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    
    $payment=new paymentModel();
    
    
    
    if (isset($_POST['amount']) && $_POST['amount'] && $_POST['date'] && $_POST['initials']) {
        $payment->amount=$_POST['amount'];
        $payment->date=strtotime($_POST['date']);
        $payment->initials=mb_strtoupper($_POST['initials'],'UTF-8');
        $payment->email=$_POST['email'];
        $payment->save();
    } elseif (isset($_GET['del'])) $payment->remove($_GET['del']);
    
    
    $all=$payment->select([],'date DESC,id DESC');
    
    //mydie($all);

?>

<form method="post">
    <input name="amount" type="number" placeholder="amount" style="width: 100px; height: 40px"/>
    <input name="date" type="date" placeholder="when" style="width: 200px;height: 40px"/>
    <input name="initials" type="text" placeholder="who" maxlength="2" style="width: 60px; height: 40px; text-transform:uppercase"/>
    <input name="email" type="email" placeholder="email" style="width: 300px; height: 40px; text-transform:uppercase"/>
    <input type="submit" value="Add!" style="width: 100px;height: 40px"/>
</form>

<?php foreach ($all AS $p): ?>

<div class="row">
    <div class="col-sm-2"><?php echo $p['initials'];?></div>
    <div class="col-sm-4"><?php echo $p['email'];?></div>
    <div class="col-sm-2"><?php echo $p['amount'];?></div>
    <div class="col-sm-2"><?php echo date('d-m-Y',$p['date']);?></div>
    <div class="col-sm-2"><a href="./?del=<?php echo $p['id'];?>">Usuń</a></div>
</div>
<?php endforeach;?>

