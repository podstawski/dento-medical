<?php
    $title='Testimonials';
    $menu='testimonial';
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    $testimonial=new testimonialModel();
    
    if (isset($_POST['testimonial'])) {
        $testimonial->load($_POST['testimonial'],true);
        $testimonial->d_issue=Bootstrap::$main->now;
        $testimonial->save();
    }
    
?>
<form method="POST">
    <input type="text" placeholder="who" name="testimonial[author]"/>
    <br/><input type="text" placeholder="text" name="testimonial[testimonial]"/>
    <br/><input type="text" size="5" placeholder="church_id" name="testimonial[church]"/>
    
    <br/><input type="submit" value="add"/>
</form>



<?php



    include __DIR__.'/../foot.php';
    