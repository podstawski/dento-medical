<?php

    foreach($church->get_unmassed()?:[] AS $rec)
    {
        $masses=[];
        $masses=array_merge($masses,analyze_mass([0],$rec['sun']));
        $masses=array_merge($masses,analyze_mass([1,2,3,4,5,6],$rec['week']));
        $masses=array_merge($masses,analyze_mass([8],$rec['fest']));
        
        add_masses($church,$masses,$rec['id']);
    }