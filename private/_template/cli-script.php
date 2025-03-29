<?php
	$APP_RootDir = $_SERVER["PWD"]."/".substr($_SERVER["PHP_SELF"], 0, strpos($_SERVER["PHP_SELF"], "private/script"));
	require_once($APP_RootDir."private/script/start/CLI.php");

	// Run something

	$APP_DB[0] -> close();
?>