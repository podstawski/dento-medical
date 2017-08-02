<?php

class userModel extends Model {
	protected $_table='users';
		

	public function get_editors($gt=0)
	{
		$sql="SELECT * FROM users WHERE id>? AND id IN (SELECT change_author FROM churches WHERE change_author>0)";
		
		return $this->conn->fetchAll($sql,[$gt]);
	}

	
	public function get_by_fbid ($fbid) {
		$sql="SELECT * FROM users WHERE md5hash=?";
		
		return $this->conn->fetchRow($sql,['fb.'.$fbid]);
	}
}
