<?php

class areaModel extends Model {
	protected $_table='areas';
	protected $distances=[14=>3.5,13=>8,12=>17,11=>33];

	public function churches_count($id=null)
	{
		if (!$id) $id=$this->id;
		
		$sql="SELECT count(*) FROM churches WHERE area=?";
		return $this->conn->fetchOne($sql,[$id]);
	}
	
	public function churches($id=null)
	{
		if (!$id) $id=$this->id;
		
		$sql="SELECT * FROM churches WHERE area=?";
		return $this->conn->fetchAll($sql,[$id]);
	}
	
	public function find($lat,$lng)
	{
		
		foreach ($this->distances AS $zoom=>$distance)
		{
			$sql="SELECT id FROM areas";
			$sql.=" WHERE zoom=$zoom AND geo_distance(lat,lng,$lat,$lng)<$distance"; 
			$sql.=" AND lat BETWEEN ".($lat-$distance*0.9/100)." AND ".($lat+$distance*0.9/100);
			$sql.=" AND lng BETWEEN ".($lng-$distance*1.48/100)." AND ".($lng+$distance*1.48/100);
			$sql.=" ORDER BY geo_distance(lat,lng,$lat,$lng)";
			
			$id = $this->conn->fetchOne($sql);
			if ($id) return $id;
		}
		
		return null;
		
	}
	
	public function setZoom($churches=null)
	{
		if (is_null($churches)) $churches=$this->churches_count();
		
		$zoom=14;
		if ($churches > 7) $zoom=13;
		if ($churches > 15) $zoom=12;
		if ($churches > 30) $zoom=11;
		
		return $zoom;
	}
	
	public function add($church_id,$id=0)
	{
		if ($id) $area=new areaModel($id);
		else {
			$area=new areaModel(['zoom'=>$this->setZoom(0)],true);
			$area->save();
		}
		
		
		$church=new churchModel($church_id);
		$church->area=$area->id;
		$church->save();
		
		$churches=$area->churches_count();
		$area->zoom=$this->setZoom($churches);
		
		$middle=$this->middle($area->id);
		
		$area->lat=$middle[0];
		$area->lng=$middle[1];
		
		$area->save();
	}
	
	
	public function middle($id)
	{
		$minlat=$maxlat=$minlng=$maxlng=0;
		
		foreach($this->churches($id) AS $ch)
		{
			if (!$minlat) $minlat=$maxlat=$ch['lat'];
			if (!$minlng) $minlng=$maxlng=$ch['lng'];
			
			$minlat=min($minlat,$ch['lat']);
			$maxlat=max($maxlat,$ch['lat']);
			$minlng=min($minlng,$ch['lng']);
			$maxlng=max($maxlng,$ch['lng']);
		}
		
		return [($minlat+$maxlat)/2,($minlng+$maxlng)/2];
	}
	
	public function deduplicate($echo=false,$dups=fals)
	{
		
		
		$sql="SELECT name FROM areas WHERE name IS NOT NULL GROUP BY name HAVING count(*)>1";
		if (!$dups) $dups=$this->conn->fetchColumn($sql);
		
		
		
		foreach($dups AS $name) {
			if ($echo) echo "<h3>$name</h3><ol>";
			$sql="SELECT * FROM areas WHERE name=? ORDER BY rand()";
			$group=$this->conn->fetchAll($sql,[$name]);
			
			for ($i=1;$i<count($group);$i++)
			{
				$dist=$this->distance($group[0]['lat'],$group[0]['lng'],$group[$i]['lat'],$group[$i]['lng']);
				$distance_allowed=3*$this->distances[min($group[0]['zoom'],$group[$i]['zoom'])];
			
				if ($distance_allowed==10.5) $distance_allowed=21;
			
				if ($echo) echo "<li>$dist < $distance_allowed ? [".$group[0]['id'].",".$group[$i]['id']."]</li>";
				if ($dist<=$distance_allowed)
				{
					$area=new areaModel($group[0]);
					$sql="UPDATE churches SET area=? WHERE area=?";
					$this->conn->execute($sql,[ $group[0]['id'], $group[$i]['id'] ]);
					$middle=$this->middle($area->id);
					$area->lat=$middle[0];
					$area->lng=$middle[1];
					$area->save();
					
					//$this->remove($group[$i]['id']);
					
					
				}
			}
			if ($echo) echo '</ol>';
			

		}
		
		
		
		
	}
	
    public function distance($lat1,$lng1,$lat2,$lng2)
    {
		$sql="SELECT geo_distance($lat1,$lng1,$lat2,$lng2)";
		return $this->conn->fetchOne($sql);
    }	
}
