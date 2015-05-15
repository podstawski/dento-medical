<?php

class CreateTableChurches extends Doctrine_Migration_Base
{
    private $_tableName = 'churches';

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
                'notnull' => false,
            ),
            'md5hash' => array(
                'type' => 'character varying(32)',
                'notnull' => false,
            ),
            'www' => array(
                'type' => 'character varying(255)',
                'notnull' => false,
            ),            
            'password' => array(
                'type' => 'character varying(32)',
                'notnull' => false,
            ),
            'name' => array(
                'type' => 'character varying(200)',
                'notnull' => true,
            ),
            'country' => array(
                'type' => 'varchar(3)',
                'notnull' => true,
            ),
            'city' => array(
                'type' => 'varchar(200)',
                'notnull' => false,
            ),
            'address' => array(
                'type' => 'varchar',
                'notnull' => false,
            ),
            'postal' => array(
                'type' => 'varchar(10)',
                'notnull' => false,
            ),
            'phone' => array(
                'type' => 'varchar(200)',
                'notnull' => false,
            ),
            'tel' => array(
                'type' => 'varchar(20)',
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
            'about' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'active' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'rector' => array(
                'type' => 'character varying(255)',
                'notnull' => false,
            ),
            
            'sun' => array(
                'type' => 'varchar(100)',
                'notnull' => false,
            ),
            'week' => array(
                'type' => 'varchar(100)',
                'notnull' => false,
            ),
            'fest' => array(
                'type' => 'varchar(100)',
                'notnull' => false,
            ),
            'change_author' => array(
                'type' => 'varchar(200)',
                'notnull' => false,
            ),
            'change_author_email' => array(
                'type' => 'varchar(200)',
                'notnull' => false,
            ),
            'change_time' => array(
                'type' => 'Integer',
                'notnull' => false,
            ),
            'change_ip' => array(
                'type' => 'varchar(50)',
                'notnull' => false,
            ),            

        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_email_key',array('fields'=>array('email')));
        $this->addIndex($this->_tableName,$this->_tableName.'_md5hash_key',array('type'=>'unique','fields'=>array('md5hash')));
        $this->addIndex($this->_tableName,$this->_tableName.'_geo_key',array('fields'=>array('lat','lng')));
        
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
