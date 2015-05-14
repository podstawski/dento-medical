<?php

class CreateTableImages extends Doctrine_Migration_Base
{
    private $_tableName = 'images';
    private $_fkName1 = 'fk_images_church';

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
            'src' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),            
            'url' => array(
                'type' => 'varchar(255)',
                'notnull' => false,
            ),
            'title' => array(
                'type' => 'text',
                'notnull' => false,
            ), 
            'author' => array(
                'type' => 'text',
                'notnull' => false,
            ), 
            'd_taken' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'd_uploaded' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),            
        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_church_key',array('fields'=>array('user')));
        
        
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
