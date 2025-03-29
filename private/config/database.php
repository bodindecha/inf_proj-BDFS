<?php
	if (!isset($APP_CONST)) {
		if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
		require_once($APP_RootDir."private/config/constant.php");
	}

	$APP_CONST["DB_INFO"] = array(
		array(
			"serv" => "localhost",
			"user" => "tiantcl_42629",
			"pswd" => "WsK5cyugY5K37speceDb",
			"name" => "tiantcl_42629"
		)
	);
?>