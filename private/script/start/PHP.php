<?php
	session_start(); ob_start();
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/script/function/utility.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	require_once($APP_RootDir."private/script/function/autoCDN.php");

	if ($APP_CONST["environment"] == "DEV") {
		ini_set("display_errors", "1");
		ini_set("display_startup_errors", "1");
		error_reporting(E_ALL);
	}

	require_once($APP_RootDir."private/script/function/checkPermission.php");
	require($APP_RootDir."private/script/function/checkAuthorization.php");
	if ($useRedirectRule ?? true) isAllowedToViewPage();

	require_once($APP_RootDir."private/script/start/HTML.php");
?>