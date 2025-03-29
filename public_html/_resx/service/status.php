<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json");
	# http_status_code(200);
	echo json_encode(array(
		"Status" => "OK",
		"Code" => 200
	));
?>