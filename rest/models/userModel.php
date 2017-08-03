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
	
	public function rank() {
		$sql="UPDATE users SET
			rank=(SELECT count(distinct(church)) FROM images WHERE author_id=users.id)
			+(SELECT count(*) FROM churches WHERE change_author=users.id)
			+(SELECT count(*) FROM churches WHERE change_author=users.id AND (SELECT count(*) FROM masses WHERE church=churches.id)>0)";
	}
	
	
	public function people($limit) {
		$sql="SELECT * FROM users ORDER BY rank DESC LIMIT ".$limit;
		
		return $this->conn->fetchAll($sql);
	}
}
