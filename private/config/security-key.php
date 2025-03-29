<?php
	if (!isset($APP_CONST)) {
		if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
		require_once($APP_RootDir."private/config/constant.php");
	}

	$APP_CONST["SECURITY_KEY"] = array(
		"crypto_key" => "TianTcl-is_Awe50me"
	);
?>