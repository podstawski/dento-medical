<?php

class codeModel extends Model {
	protected $_table='codes';
		

	public function getCode($origin)
	{
		$sql="SELECT id,code FROM codes WHERE origin=? AND d_given IS NULL";
		$row=$this->conn->fetchRow($sql,[$origin]);
		if ($row) {
			$this->get($row['id']);
			$this->d_given=Bootstrap::$main->now;
			$this->save();
			return $row['code'];
		}
	}
}
