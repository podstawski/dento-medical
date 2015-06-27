<?php

class imageModel extends Model {
	protected $_table='images';
		

	public function activateTrusted($trust,$codeOrigin='')
	{
		$cond="active IS NULL
			AND author_id IN
			(SELECT id FROM users WHERE author_id=users.id AND trust>=$trust)";
		
		if ($codeOrigin) {
			$code=new codeModel();
			$sql="SELECT * FROM images WHERE $cond";
			$images=$this->conn->fetchAll($sql)?:[];
			foreach ($images AS $img)
			{
				$this->get($img['id']);
				$this->code=$code->getCode($codeOrigin);
				$this->save();
			}
		}
		
		$sql="UPDATE images SET active=1 WHERE $cond";
	
		return $this->conn->execute($sql);
		
	}
}
