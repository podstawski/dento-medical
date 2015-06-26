<?php

class AlterTableImagesAddCode extends Doctrine_Migration_Base
{
    private $_tableName = 'images';
    protected $_columnName1 = 'code';
    
    
    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'Varchar(200)', null, array('notnull' => false ));
        
    }
    

    public function down()
    {
 
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
