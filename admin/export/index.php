<?php

	include __DIR__.'/../base.php';
	$church=new churchModel();

	$all=$church->getAll();
	foreach ($all AS &$c) {
		foreach (array_keys($c) AS $k) if ($k!='lat' && $k!='lng') unset($c[$k]);
	}
	$file='export/heatmap.json';
        $real_path=Tools::saveRoot($file);
	file_put_contents($real_path,json_encode($all,JSON_NUMERIC_CHECK));
	unset($all);
	
	if (isset($_GET['all'])) {
		ini_set('display_errors',1);
		@ini_set('max_execution_time',300);
		$file='export/'.date('Ymd-His').'.json';
		$real_path=Tools::saveRoot($file);
		
		$file_handle=fopen($real_path,'w');
		$church->export($file_handle);
		fclose($file_handle);
	}
	
	$size=filesize($real_path);
	
	if (!isset($_GET['all'])) return;
?>
<script>
	alert('File exported to <?php echo $real_path;?>, size=<?php echo $size;?>');
	history.go(-1);
</script>
