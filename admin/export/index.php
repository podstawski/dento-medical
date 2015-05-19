<?php

	include __DIR__.'/../base.php';
	$church=new churchModel();

	
	$file='export/'.date('Ymd-His').'.json';
	$real_path=Tools::saveRoot($file);
	
	$file_handle=fopen($real_path,'w');
	$church->export($file_handle);
	fclose($file_handle);
	
	$size=filesize($real_path);
?>
<script>
	alert('File exported to <?php echo $real_path;?>, size=<?php echo $size;?>');
	history.go(-1);
</script>