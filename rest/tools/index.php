<?php

	foreach (scandir(__DIR__) AS $f)
	{
		if (strstr($f,'.csv')) echo '<p><a href="'.$f.'">'.$f.'</a></p>';
	}
