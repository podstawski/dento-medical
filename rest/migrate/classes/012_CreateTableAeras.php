<?php

class CreateTableAeras extends Doctrine_Migration_Base
{
    private $_tableName = 'areas';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(200)',
                'notnull' => false,
            ),
            'administrative_area' => array(
                'type' => 'varchar(200)',
                'notnull' => false,
            ),
            'lat' => array(
                'type' => 'DECIMAL(18,9)',
                'notnull' => false,
            ),
            'lng' => array(
                'type' => 'DECIMAL(18,9)',
                'notnull' => false,
            ),
            'zoom' => array(
                'type' => 'Integer(2)',
                'notnull' => false,
            ),
            
                      
        ), array('charset'=>'utf8'));
        
        $this->addColumn('churches', 'area', 'Integer', null, array('notnull' => false ));
        $this->addIndex('churches','churches_area_key',array('fields'=>array('area')));

    }
    
    
    
    public function down()
    {
        $this->removeColumn('churches', 'area');
        $this->dropTable($this->_tableName);
    }
}
