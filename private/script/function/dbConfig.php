<?php
	if (!isset($_SESSION)) session_start();
	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/function/database.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	
	class DBConfig {
		const ERR_RESULT = "$~ERR!";
		private static $table = "config_sys",
			$DBidx = 0;

		function __construct(int $DB_index = 0) {
			if ($DB_index > 0) self::$DBidx = $DB_index;
		}
		public static function switchDB(int $DB_index): bool {
			global $APP_CONST;
			if ($DB_index < 0 || $DB_index >= count($APP_CONST["DB_INFO"])) return false;
			self::$DBidx = $DB_index;
			return true;
		}

		final public static function get(string $key): mixed {
			global $APP_DB;
			$key = escapeSQL($key);
			if (!strlen($key)) return self::ERR_RESULT;
			$get = $APP_DB[self::$DBidx] -> query("SELECT value FROM ".self::$table." WHERE name='$key'");
			if (!$get || $get -> num_rows <> 1) return self::ERR_RESULT;
			return TianTcl::beautifyAnswer(($get -> fetch_array(MYSQLI_ASSOC))["value"]);
		}
		final public static function set(string $key, string|int|float|bool $value): bool|string {
			global $APP_DB;
			$key = escapeSQL($key);
			if (!strlen($key)) return self::ERR_RESULT;
			$value = escapeSQL($value);
			$success = $APP_DB[self::$DBidx] -> query("UPDATE ".self::$table." SET value='$value' WHERE name='$key'");
			return (bool)$success;
		}
		final public static function add(string $key, int|float $amount=1): bool|string {
			global $APP_DB;
			$key = escapeSQL($key);
			if (!strlen($key)) return self::ERR_RESULT;
			if (!RegExTest("/^(\d*\.\d+|\d+)$/", $amount)) return self::ERR_RESULT;
			$amount = escapeSQL($amount);
			$success = $APP_DB[self::$DBidx] -> query("UPDATE ".self::$table." SET value=value+$amount WHERE name='$key'");
			return (bool)$success;
		}
	}
?>