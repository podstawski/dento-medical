<?php

class ChangeTableImages extends Doctrine_Migration_Base
{
    

    public function up()
    {
                
        Doctrine_Manager::connection()->exec("
            ALTER TABLE images CHANGE author active Integer; 
        ");
        
        Doctrine_Manager::connection()->exec("
            ALTER TABLE images CHANGE title square Text; 
        ");
                
        
    
          
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("
            ALTER TABLE images CHANGE active author Text; 
        ");
        
        Doctrine_Manager::connection()->exec("
            ALTER TABLE images CHANGE square title Text; 
        ");
             
    }
}
