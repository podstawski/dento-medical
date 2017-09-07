<?php

class CreateTablePayments extends Doctrine_Migration_Base
{
    private $_tableName = 'payments';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'initials' => array(
                'type' => 'varchar(5)',
                'notnull' => true,
            ),            
            'date' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
			'amount' => array(
                'type' => 'DECIMAL(18,2)',
                'notnull' => false,
            ),
			'email' => array(
                'type' => 'varchar(200)',
                'notnull' => true,
            )

                      
        ), array('charset'=>'utf8'));
        
          
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
