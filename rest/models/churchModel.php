<?php

class churchModel extends Model {
	protected $_table='churches';
	
	
	public function remove_masses($id=null)
	{
		if (is_null($id)) $id=$this->id;
		
		$sql="DELETE FROM masses WHERE church=?";
		
		return $this->conn->execute($sql,[$id]);
	}
}
