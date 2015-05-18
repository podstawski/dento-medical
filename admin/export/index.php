<?php

	include __DIR__.'/../base.php';
	$church=new churchModel();

	//Header("Content-type: application/json; charset=utf8");
	echo json_encode($church->export(),JSON_NUMERIC_CHECK);

	
