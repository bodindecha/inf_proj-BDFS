<?php
	if (!isset($APP_CONST)) {
		if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
		require_once($APP_RootDir."private/config/constant.php");
	}
	if (!isset($APP_CONST["DB_INFO"])) require_once($APP_RootDir."private/config/database.php");
	
	if (!isset($APP_DB)) $APP_DB = array();
	function connect_to_database(int $database=0): object|array {
		global $APP_CONST, $APP_DB;
		$selectedDB = $APP_CONST["DB_INFO"][$database];
		try {
			$db = new mysqli($selectedDB["serv"], $selectedDB["user"], $selectedDB["pswd"], $selectedDB["name"]);
			$db -> set_charset("utf8");
			if ($db -> connect_errno) return array(
				"DB_ERROR_NO" => $db -> connect_errno,
				"DB_ERROR_MSG" => $db -> connect_error
			); $APP_DB[$database] = $db;
			return $APP_DB[$database];
		} catch (exception $exception) { return array("DB_ERROR_EXCEPTION" => $exception); } 
	} connect_to_database();

	function escapeSQL(int|string|float $input): string {
		global $APP_DB;
		return $APP_DB[0] -> real_escape_string(trim(strval($input)));
	}

	function syslog_a(string|int|null $doer, string $flow, string $action, string $impact, string $detail="", bool $state=true, string $attr="", string $remark="", bool $force=false, bool $close_db_connection=false): mixed {
		// Check connection
		global $APP_CONST, $APP_DB, $USER_IP, $APP_RootDir;
		if (!isset($USER_IP)) require($APP_RootDir."private/script/function/utility.php");

		// Clean data
		if ($doer == null) $doer = strval($_SESSION["auth"]["user"] ?? $APP_CONST["USER_TYPE"][0]); else
		$doer	= escapeSQL($doer);
		$flow	= escapeSQL($flow);
		$action	= escapeSQL($action);
		$impact	= escapeSQL($impact);
		$detail	= escapeSQL($detail);
		$state	= ($state ? "PASS" : "FAIL");
		$attr	= escapeSQL($attr);
		$remark	= escapeSQL($remark);
		
		// Filter user
		if (!$force && in_array($doer, $APP_CONST["USER_NO_SHADOW"])) return false;
		
		// Record
		$success = $APP_DB[0] -> query("INSERT INTO log_action_v2 (doer,flow,action,impact,detail,state,attr,remark,ip) VALUE ('$doer','$flow','$action','$impact','$detail','$state','$attr','$remark','$USER_IP')");
		
		// Close connection
		$refID = $success ? $APP_DB[0] -> insert_id : false;
		if ($close_db_connection) $APP_DB[0] -> close();

		return $refID;
	}

	function multiQueryCheck(mysqli|bool $queryResult, int $database = 0): bool {
		global $APP_DB;
		if (gettype($queryResult) == "boolean") return $queryResult;
		$allSuccess = true;
		do {
			if ($result = $APP_DB[$database] -> store_result()) $result -> free();
			else if ($APP_DB[$database] -> errno) $allSuccess = false;
		} while ($APP_DB[$database] -> more_results() && $APP_DB[$database] -> next_result());
		return $allSuccess;
	}
?>