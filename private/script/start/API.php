<?php
	session_start(); ob_start();
	$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	// Connect
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/script/function/utility.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	require_once($APP_RootDir."private/script/function/database.php");
	require_once($APP_RootDir."private/script/function/checkPermission.php");
	require_once($APP_RootDir."private/script/function/dbConfig.php");

	if ($APP_CONST["environment"] == "DEV") {
		ini_set("display_errors", "1");
		ini_set("display_startup_errors", "1");
		error_reporting(E_ALL);
	}

	class API {
		private static $default = array(), $is = array(
			"initialized" => false
		), $return;
		public static $action, $command, $attr, $file;
		function __construct() {
			# if (self::$is["initialized"]) self::initialize();
		}
		public final static function initialize(bool $useNormalParameters=true, bool $useResponseTemplate=true): void {
			if (self::$is["initialized"]) return;
			global $APP_RootDir, $_SERVER;
			$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
			self::$default["APP_RootDir"] = $APP_RootDir;
			$buffer = new self();
			$buffer -> retrieveData();
			$buffer -> setDefault($useNormalParameters, $useResponseTemplate);
			self::$is["initialized"] = true;
		}
		// Initializers
		final protected function retrieveData(): void {
			global $_REQUEST, $_FILES;
			// Recieve
			$_REQUEST ??= [];
			self::$action	= $_REQUEST["act"] ?? null;
			self::$command	= $_REQUEST["cmd"] ?? null;
			self::$attr		= $_REQUEST["param"] ?? (json_decode(file_get_contents('php://input'), true) ?? null);
			self::$file		= $_FILES ?? null;
		}
		final protected function setDefault(bool $useNormalParameters=true, bool $useResponseTemplate=true): void {
			// Review
			self::$return = $useResponseTemplate ? array(
				"success" => false,
				"messages" => [],
				"jsaction" => []
			) : [];
			if ($useNormalParameters && (!strlen(self::$action) || !strlen(self::$command)))
				self::sendOutput();
		}

		// Prototypes
		final public static function successState(mixed $output=null, bool $clearMsg=true, bool $clearJsaction=true): void {
			if (!self::$is["initialized"]) self::initialize();
			self::$return["success"] = true;
			if ($clearMsg) unset(self::$return["messages"]);
			if ($clearJsaction) unset(self::$return["jsaction"]);
			if ($output <> null) self::$return["info"] = $output;
		}
		final public static function errorMessage(int|string $type, string|null $text=null, int|null $display_dur=null): void {
			if (!self::$is["initialized"]) self::initialize();
			array_push(
				self::$return["messages"],
				$text == null ? $type : ($display_dur ? [$type, $text, $display_dur] : [$type, $text])
			);
		}
		final public static function infoMessage(int|string $type, string|null $text=null, int|null $display_dur=null): void {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["messages"])) self::$return["messages"] = [];
			array_push(
				self::$return["messages"],
				$text == null ? $type : ($display_dur ? [$type, $text, $display_dur] : [$type, $text])
			);
		}
		final public static function addJSAction(array|string $commands): void {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["jsaction"])) self::$return["jsaction"] = [];
			gettype($commands) == "string" ?
				array_push(self::$return["jsaction"], $commands) : array_push(self::$return["jsaction"], ...$commands);
		}
		final public static function devInfo(string|int|float $key, mixed $data, string $mode="replace"): void {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["_dev"])) self::$return["_dev"] = [];
			if ($mode == "replace" || !isset(self::$return["_dev"][$key])) self::$return["_dev"][$key] = $data;
			else if ($mode == "append") {
				if (!isset(self::$return["_dev"][$key])) self::$return["_dev"][$key] = [];
				if (is_array(self::$return["_dev"][$key]) && is_array($data)) self::$return["_dev"][$key] = array_merge(self::$return["_dev"][$key], $data);
			} else if ($mode == "append") self::$return["_dev"][$key] .= $data;
		}
		final public static function sendOutput(bool|int $resp=false, bool $readable=false): never {
			if (!self::$is["initialized"]) self::initialize();
			global $APP_CONST, $APP_DB;
			// Handle configuration
			if (gettype($resp) == "int") http_response_code($resp);
			else $readable = $resp;
			// Process data
			if (ob_get_level() && $error = ob_get_clean()) {
				if ($APP_CONST["environment"] == "DEV") API::errorMessage(3, $error);
				else self::devInfo("error", $error);
			} $outputData = json_encode(self::$return, !$readable ? 0 : JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
			header("Content-Type: application/json; charset=UTF-8");
			header("Content-Length: ".strlen($outputData));
			echo $outputData;
			$APP_DB[0] -> close();
			exit(0);
		}

		// Utilities
		final public static function requirePermission(string|array|null $scopes=null, bool $useAnd=true, bool $mods=true): bool {
			global $APP_USER;
			if (hasPermission($scopes, $useAnd, mods: $mods)) return true;
			self::$return["messages"] = [[2, "You don't have permission to perform this action."]];
			if (empty($APP_USER)) self::addJSAction("sys?.auth?.request();");
			self::sendOutput();
			return false;
		}
	}
?>