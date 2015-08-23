<?php

class aeraModel extends Model {
	protected $_table='aeras';
		

	public function churches_count($id=null)
	{
		if (!$id) $id=$this->id;
		
		$sql="SELECT count(*) FROM churches WHERE aera=?";
		return $this->conn->fetchOne($sql,[$id]);
	}
	
	public function churches($id=null)
	{
		if (!$id) $id=$this->id;
		
		$sql="SELECT * FROM churches WHERE aera=?";
		return $this->conn->fetchAll($sql,[$id]);
	}
	
	public function find($lat,$lng)
	{
		
		foreach ([14=>3.5,13=>8,12=>17,11=>33] AS $zoom=>$distance)
		{
			$sql="SELECT id FROM aeras";
			$sql.=" WHERE zoom=$zoom AND geo_distance(lat,lng,$lat,$lng)<$distance"; 
			$sql.=" AND lat BETWEEN ".($lat-$distance*0.9/100)." AND ".($lat+$distance*0.9/100);
			$sql.=" AND lng BETWEEN ".($lng-$distance*1.48/100)." AND ".($lng+$distance*1.48/100);
			$sql.=" ORDER BY geo_distance(lat,lng,$lat,$lng)";
			
			$id = $this->conn->fetchOne($sql);
			if ($id) return $id;
		}
		
		return null;
		
	}
	
	public function add($church_id,$id=0)
	{
		if ($id) $aera=new aeraModel($id);
		else {
			$aera=new aeraModel(['zoom'=>14],true);
			$aera->save();
		}
		
		
		$church=new churchModel($church_id);
		$church->aera=$aera->id;
		$church->save();
		
		
		$churches=$aera->churches_count();
		if ($churches > 7) $aera->zoom=13;
		if ($churches > 15) $aera->zoom=12;
		if ($churches > 30) $aera->zoom=11;
		
		$minlat=$maxlat=$minlng=$maxlng=0;
		
		foreach($aera->churches() AS $ch)
		{
			if (!$minlat) $minlat=$maxlat=$ch['lat'];
			if (!$minlng) $minlng=$maxlng=$ch['lng'];
			
			$minlat=min($minlat,$ch['lat']);
			$maxlat=max($maxlat,$ch['lat']);
			$minlng=min($minlng,$ch['lng']);
			$maxlng=max($maxlng,$ch['lng']);
		}
		
		$aera->lat=($minlat+$maxlat)/2;
		$aera->lng=($minlng+$maxlng)/2;
		
		$aera->save();
	}
}
