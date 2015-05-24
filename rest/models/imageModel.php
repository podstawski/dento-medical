<?php

class imageModel extends Model {
	protected $_table='images';
		

	public function activateTrusted($trust)
	{
		$sql="UPDATE images SET active=1 WHERE active IS NULL
			AND author_id IN
			(SELECT id FROM users WHERE author_id=users.id AND trust>=$trust)";
	
		return $this->conn->execute($sql);
		
	}
}
