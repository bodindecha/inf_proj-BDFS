<?php
	$APP_CONST = array(
		"name" => "INF Sandbox",
		"domain" => "https://inf.bodin.ac.th",
		"baseURL" => "/",
		"defaultCDN" => "TH", // Default to TH if geolocation fails
		"publicDir" => "public_html",
		"environment" => "PROD",
		"PERM_TYPES" => array("r", "w", "e", "o"),
		"PERM_CACHE_DUR" => 60, # 1 minute
		"PERM_MOD_GROUP" => array("owner", "admin", "moderator", "dev"),
		"USER_NO_SHADOW" => array(),
		"USER_TYPE" => array("", "_sys_", "_bot_", "_guest_", "_unknown_"),
		"SYSTEM_OWNER" => array("TianTcl", "42629", "test02", "99999"),
		"ENCRYPTION" => array(
			"SECRET" => "32 in length",
			"IV" => "16 in length"
		),
		"TH" => array(
			"month" => ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"],
			"month_abbr" => ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."]
		),
		"REGEX" => array(
			"email" => "/^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,13})$/",
			"URL" => "/^((http(s)?:)?\/\/|)?((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z0-9\-_]+\.)+[a-zA-Z]{2,13}))((\/|\?|#)\S*)?$/",
			"phone" => "/^(\+\d{1,3}\ )?(\d{8,13}|\d{1,3}((\ |\-)\d{3,4}){2}|\d{1,3}(\ |\-)\d{4,7})$/",
			"telephone" => "/^0[1-9]\d{8}$/",
			"date" => "/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/",
			"BASE64" => "/^[A-Z0-9a-z\-_+\/]+$/",
			"UUIDv4" => "/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i",
			"AES" => "/^[A-Z0-9a-z\/+=]+$/",
			"AES-URL" => "/^[A-Z0-9a-z\-_]+$/",
			"vToken" => "/^[A-Z0-9a-z]{1,9}$/"
		),
		"SEARCH_RESULT_LIMIT" => 50
	);
?>