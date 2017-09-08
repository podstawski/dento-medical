<?php

class AlterTableImagesAddLatLng extends Doctrine_Migration_Base
{
    private $_tableName = 'images';
    protected $_columnName1 = 'lat';
    protected $_columnName2 = 'lng';
    
    
    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'DECIMAL(18,9)', null, array('notnull' => false ));
        $this->addColumn($this->_tableName, $this->_columnName2, 'DECIMAL(18,9)', null, array('notnull' => false ));
        
    }
    

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName2);
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
