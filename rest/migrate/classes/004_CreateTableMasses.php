<?php

class CreateTableMasses extends Doctrine_Migration_Base
{
    private $_tableName = 'masses';
    private $_fkName1 = 'fk_masses_church';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'church' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'dm_from' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),            
            'dm_to' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'dow' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'time' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'kids' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'youth' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'description' => array(
                'type' => 'Text',
                'notnull' => false,
            ),            
        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_church_key',array('fields'=>array('church')));
        
        
        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'church',
             'foreign'       => 'id',
             'foreignTable'  => 'churches',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
          
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
