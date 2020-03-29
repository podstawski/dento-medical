<?php

class AlterTableUsersAddLink extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    protected $_columnName1 = 'link';
    
    
    
    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'TEXT', null, array('notnull' => false ));
           
    }
    

    public function down()
    {

        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
