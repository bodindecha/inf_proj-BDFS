<?php
	// Check if the script is run from CLI
	if (php_sapi_name() <> "cli") die("This script can only be run via CLI");

	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/script/function/utility.php");
	require_once($APP_RootDir."private/script/lib/cli/core.php");
	require_once($APP_RootDir."private/script/function/dbConfig.php");
	require($APP_RootDir."private/script/function/database.php");
?>