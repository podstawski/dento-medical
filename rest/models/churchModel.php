<?php

class churchModel extends Model {
	protected $_table='churches';
	
	
	public function remove_masses($id=null)
	{
		if (is_null($id)) $id=$this->id;
		
		$sql="DELETE FROM masses WHERE church=?";
		return $this->conn->execute($sql,[$id]);
	}
	
	
	public function search($lat,$lng,$distance,$time,$m,$dow,$now,$limit,$offset)
	{
		$lat+=0;
		$lng+=0;
		$distance+=0;

		$sql="SELECT *,geo_distance(lat,lng,$lat,$lng) AS distance,churches.id AS church_id";
		$sql.=" FROM churches";
		$sql.=" INNER JOIN masses ON masses.church=churches.id AND moy=$m AND dow=$dow AND time>$time";
		$sql.=" WHERE churches.active=1"; 

		$sql.=" AND lat BETWEEN ".($lat-$distance*0.9/100)." AND ".($lat+$distance*0.9/100);
		$sql.=" AND lng BETWEEN ".($lng-$distance*1.48/100)." AND ".($lng+$distance*1.48/100);
		$sql.=" AND geo_distance(lat,lng,$lat,$lng)<$distance";
		
		$sql.=" ORDER BY";
		if ($time) $sql.=" (100*geo_distance(lat,lng,$lat,$lng))+time-$now";
		else $sql.=" (3000*geo_distance(lat,lng,$lat,$lng))+time";
		
		$sql.=" LIMIT $limit OFFSET $offset";
		
		$churches=$this->conn->fetchAll($sql);
		
		//mydie($churches,date('H:i',$time).$sql);
		
		return $churches;
		
	}
}
