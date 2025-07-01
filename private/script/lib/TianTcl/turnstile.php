<?php
	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	class Turnstile {
		private static $default, $is = array(
			"initialized" => false
		), $TURNSTILE = array(
			"API_URL" => "https://challenges.cloudflare.com/turnstile/v0/siteverify",
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

		final public static function verify(string $token): bool {
			global $APP_CONST, $USER_IP;
			$data = array(
				"secret" => self::$default["TURNSTILE_SECRET"],
				"response" => $token,
			); if (strlen($USER_IP)) $data["remoteip"] = $USER_IP;

			$ch = curl_init(self::$TURNSTILE["API_URL"]);
			curl_setopt_array($ch, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($data)
			)); if ($show_debug = class_exists("API") && $APP_CONST["environment"] == "DEV") curl_setopt_array($ch, array(
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true
			)); $result = curl_exec($ch);
			if (curl_errno($ch) && $show_debug) API::errorMessage(3, curl_error($ch));
			curl_close($ch);

			$result = json_decode($result, true);
			return isset($result["success"]) && $result["success"] == true;
		}
	} Turnstile::initialize();
?>