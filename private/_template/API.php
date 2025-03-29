<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	// Execute
	switch (API::$action) {
		case "": {
			switch (API::$command) {
				case "": {

				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: API::errorMessage(1, "Invalid type"); break;
	} API::sendOutput();
?>