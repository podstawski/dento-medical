<?php

class CreateTableUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';

    public function up()
    {
        
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'email' => array(
                'type' => 'character varying(255)',
                'notnull' => true,
            ),
            'md5hash' => array(
                'type' => 'character varying(32)',
                'notnull' => true,
            ),
    
            'firstname' => array(
                'type' => 'character varying(200)',
                'notnull' => true,
            ),
            'lastname' => array(
                'type' => 'character varying(200)',
                'notnull' => true,
            ),
            'url' => array(
                'type' => 'character varying(255)',
                'notnull' => true,
            ),
           
            'photo' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'trust' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
           

        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_email_key',array('type'=>'unique','fields'=>array('email')));
        $this->addIndex($this->_tableName,$this->_tableName.'_md5hash_key',array('type'=>'unique','fields'=>array('md5hash')));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
