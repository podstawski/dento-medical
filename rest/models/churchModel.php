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
		//mydie("$lat,$lng,$distance,$time,$m,$dow,$now,$limit,$offset");
		$lat+=0;
		$lng+=0;
		$distance+=0;

		$sql="SELECT *,1.3*geo_distance(lat,lng,$lat,$lng) AS distance,churches.id AS church_id";
		$sql.=" FROM churches";
		$sql.=" INNER JOIN masses ON masses.church=churches.id AND moy=$m AND dow=$dow AND time>$time";
		$sql.=" WHERE churches.active=1"; 

		$sql.=" AND lat BETWEEN ".($lat-$distance*0.9/100)." AND ".($lat+$distance*0.9/100);
		$sql.=" AND lng BETWEEN ".($lng-$distance*1.48/100)." AND ".($lng+$distance*1.48/100);
		$sql.=" AND geo_distance(lat,lng,$lat,$lng)<$distance";
		
		$sql.=" ORDER BY time,geo_distance(lat,lng,$lat,$lng)";
		/*
		if ($time) $sql.=" (1500*geo_distance(lat,lng,$lat,$lng))+time-$now";
		else $sql.=" (3000*geo_distance(lat,lng,$lat,$lng))+time";
		*/
		
		$sql.=" LIMIT $limit OFFSET $offset";
		
		$churches=$this->conn->fetchAll($sql);
		
		//mydie($churches,date('H:i',$time).$sql);
		
		return $churches;
		
	}
	public function import($data,$restore_masses=true)
	{
		
		if (!isset($data['md5hash'])) return false;
		$masses=isset($data['masses'])?$data['masses']:[];
		unset($data['masses']);
		if (isset($data['id'])) unset($data['id']);

		$data2=$this->find_one_by_md5hash($data['md5hash']);
		
	
		if (!$data2 || !isset($data2['md5hash']) || $data['md5hash']!=$data2['md5hash'])
		{
			$newchurch=true;
			$this->load($data,true);
		}
		else
		{
			$newchurch=false;
			foreach ($data AS $k=>$v) $this->$k=$v;
		}
		
		$this->save();
		
		if(!$restore_masses && !$newchurch) return;
		if (!count($masses)) return;
		
		$this->remove_masses();
		$mass=new massModel();
		foreach($masses AS $m)
		{
			if (isset($m['id'])) unset($m['id']);
			$m['church']=$this->id;
			$mass->load($m,true);
			$mass->save();
		}
		
	}
	public function export($fh,$id=0)
	{
		$mass=new massModel();
		if (!$id) $churches=$this->getAll()?:[];
		else $churches=$this->select(['id'=>$id]);
		
		$image=new imageModel();
		
		foreach($churches AS $i=>$church)
		{
			$church['masses']=$mass->select(['church'=>$church['id']])?:[];
			$church['images']=$image->select(['church'=>$church['id']])?:null;
			
			unset($church['id']);

			foreach ($church['masses'] AS &$m)
			{
				unset($m['id']);
				unset($m['church']);
			}
			
			fwrite($fh,json_encode($church,JSON_NUMERIC_CHECK)."\n");
			unset($churches[$i]);
			
		}
		
        
		return $churches;
	}
	
	public function deduplicate()
	{
		$sql="SELECT address,min(id) AS id FROM churches WHERE address IS NOT NULL GROUP BY address HAVING count(*)>1";
		$churches=$this->conn->fetchAll($sql)?:[];
		foreach ($churches AS $church) $this->remove($church['id']);
		$sql="UPDATE churches SET change_time=".Bootstrap::$main->now." WHERE change_time IS NULL";
		$this->conn->execute($sql);
	}
	
	
    public function map($lat1,$lat2,$lng1,$lng2,$limit=0,$offset=0,$max_distance)
    {
        $sql="SELECT *  FROM ".$this->_table;
	$sql.=" WHERE geo_distance($lat1,$lng1,$lat2,$lng2)<$max_distance";
        $sql.=" AND lat BETWEEN ".$lat1." AND ".$lat2;
        $sql.=" AND lng BETWEEN ".$lng1." AND ".$lng2;

        $sql.=" ORDER BY id";
        if ($limit) $sql.=" LIMIT $limit";
        if ($offset) $sql.=" OFFSET $offset";
        
        //mydie($sql);
        return $this->conn->fetchAll($sql);

    }
    
    public function distance($lat1,$lng1,$lat2,$lng2)
    {
	$sql="SELECT geo_distance($lat1,$lng1,$lat2,$lng2)";
	return $this->conn->fetchOne($sql);
    }

    
    public function get_unmassed()
    {
	$sql="SELECT * FROM churches WHERE (SELECT count(*) FROM masses WHERE churches.id=masses.church)=0 AND sun<>''";
	return $this->conn->fetchAll($sql);
    }
}
