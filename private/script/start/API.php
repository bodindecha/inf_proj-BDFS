<?php
	session_start();
	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	// Connect
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/script/function/utility.php");
	require($APP_RootDir."private/script/function/database.php");
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
		public final static function initialize(bool $useNormalParameters=true): void {
			if (self::$is["initialized"]) return;
			global $APP_RootDir, $_SERVER;
			if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
			self::$default["APP_RootDir"] = $APP_RootDir;
			$buffer = new self();
			$buffer -> retrieveData();
			$buffer -> setDefault($useNormalParameters);
			self::$is["initialized"] = true;
		}
		// Initializers
		final protected function retrieveData(): void {
			global $_REQUEST, $_FILES;
			// Recieve
			self::$action	= $_REQUEST["act"] ?? null;
			self::$command	= $_REQUEST["cmd"] ?? null;
			self::$attr		= $_REQUEST["param"] ?? (json_decode(file_get_contents('php://input'), true) ?? null);
			self::$file		= $_FILES ?? null;
		}
		final protected function setDefault(bool $useNormalParameters) {
			// Review
			self::$return = $useNormalParameters ? array(
				"success" => false,
				"messages" => array()
			) : array();
			if ($useNormalParameters && (!strlen(self::$action) || !strlen(self::$command)))
				self::sendOutput();
		}

		// Prototypes
		final public static function successState(mixed $output=null, bool $clearMsg=true): void {
			if (!self::$is["initialized"]) self::initialize();
			self::$return["success"] = true;
			if ($clearMsg) unset(self::$return["messages"]);
			if ($output <> null) self::$return["info"] = $output;
		}
		final public static function errorMessage($type, $text=null): void {
			if (!self::$is["initialized"]) self::initialize();
			array_push(
				self::$return["messages"],
				$text == null ? $type : array($type, $text)
			);
		}
		final public static function infoMessage($type, $text=null): void {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["messages"])) self::$return["messages"] = array();
			array_push(
				self::$return["messages"],
				$text == null ? $type : array($type, $text)
			);
		}
		final public static function sendOutput(bool $readable=false): never {
			if (!self::$is["initialized"]) self::initialize();
			global $APP_DB;
			$outputData = json_encode(self::$return, !$readable ? 0 : JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
			header("Content-Type: application/json; charset=UTF-8");
			header("Content-Length: ".strlen($outputData));
			echo $outputData;
			$APP_DB[0] -> close();
			exit(0);
		}

		// Utilities
		final public static function requirePermission($scopes=null, bool $useAnd=true, bool $mods=true): bool {
			if (hasPermission($scopes, $useAnd, mods: $mods)) return true;
			self::$return["messages"] = array(
				array(2, "You don't have permission to perform this action.")
			);
			self::sendOutput();
			return false;
		}
	}
?>