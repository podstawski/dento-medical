<?php

    foreach($church->get_unmassed()?:[] AS $rec)
    {
        $masses=[];
        $masses=array_merge($masses,analyze_mass([0],$rec['sun']));
        $masses=array_merge($masses,analyze_mass([1,2,3,4,5,6],$rec['week']));
        $masses=array_merge($masses,analyze_mass([8],$rec['fest']));
        
        echo '<p><a href="../../kosciol/xxx,'.$rec['id'].'" target="_blank">'.$rec['name'].'</a></p>';
        add_masses($church,$masses,$rec['id']);
    }