<?php
	$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	class CustomClass {
		private static $default, $is = array(
			"initialized" => false
		), $CONFIGURATIONS = array(
			"___" => "___",
		);
		function __construct() {
			# if (!$this -> is["initialized"]) self::initialize();
		}
		final public static function initialize(): void {
			if (self::$is["initialized"]) return;
			global $APP_CONST, $APP_USER;
			self::$default = $APP_CONST["SECURITY_KEY"];
			self::$is["initialized"] = true;
		}

		final public static function myFunction() {
			
		}
	} CustomClass::initialize();
?>