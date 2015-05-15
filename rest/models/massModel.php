<?php

class massModel extends Model {
	protected $_table='masses';
		
	public function remove_masses($id=null)
	{
		if (is_null($id)) $td=$this->id;
		
		$sql="DELETE FROM masses WHERE church=?";
		return $this->conn->excute($sql,[$id]);
	}
}
