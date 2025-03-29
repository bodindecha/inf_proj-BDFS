<?php
	$default = "https://source.unsplash.com/random/200x200";
	header("Content-Type: images/jpeg");
	/* header("Content-Length: ".filesize($default));
	fpassthru(fopen($default, "rb")); # readfile($default); */
    $file = file_get_contents($default);
	header("Content-Length: ".strlen($file));
	echo $file;
	exit(0);
?>