<?php

class CreateTableCodes extends Doctrine_Migration_Base
{
    private $_tableName = 'codes';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'code' => array(
                'type' => 'varchar(200)',
                'notnull' => true,
            ),            
            'origin' => array(
                'type' => 'varchar',
                'notnull' => false,
            ), 
            'd_given' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
                      
        ), array('charset'=>'utf8'));
        
          
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
