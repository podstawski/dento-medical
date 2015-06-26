<?php

class CreateTableTestimonials extends Doctrine_Migration_Base
{
    private $_tableName = 'testimonials';
    private $_fkName1 = 'fk_testimonials_church';

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
            'testimonial' => array(
                'type' => 'text',
                'notnull' => true,
            ),            
            'author' => array(
                'type' => 'text',
                'notnull' => false,
            ), 
            'd_issue' => array(
                'type' => 'Integer',
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
