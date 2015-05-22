<?php

class AlterTableImagesAddThumb extends Doctrine_Migration_Base
{
    private $_tableName = 'images';
    protected $_columnName1 = 'thumb';
    
    
    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'Varchar(200)', null, array('notnull' => false ));
        
    }
    
    public function postUp()
    {
        Doctrine_Manager::connection()->exec("
            UPDATE images SET thumb=replace(square,'=s960-c','=s250-c'); 
        ");
    }

    public function down()
    {
 
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
