<?php
	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	class mlsn {
		private static $default, $is = array(
			"initialized" => false
		), $MAILERSEND = array(
			"API_URL" => "https://api.mailersend.com/v1/email",
			"API_KEY" => "mlsn._____",
			"sender" => array("email" => "noreply@___", "name" => "___"),
			"return" => array("email" => "TianTcl@___", "name" => "TianTcl"),
			"template" => array(
				"notificiation" => "___",
				"action" => "___",
				"alert" => "___"
			)
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
		public const NAME = "mailersend";
		// chinissai@hotmail.com

		final public static function email(array $data, string $subj, string $type): bool|null {
			global $APP_CONST;
			$is_in_API = class_exists("API");
			// Convert data
			$recp = isAssocArr($data) ? array_keys($data) : $data;
			// Check
			if (!self::$is["initialized"]) self::initialize();
			if (!in_array($type, array_keys(self::$MAILERSEND["template"]))) {
				if ($is_in_API) API::errorMessage(3, "Invalid email type");
				# syslog_a(null, "email", "send", self::NAME, $recp, false, "", "Wrong type");
				return false;
			} foreach ($recp as $recipient) {
				if (RegExTest("email", $recipient)) continue;
				if ($is_in_API) API::errorMessage(3, "Invalid email address");
				# syslog_a(null, "email", "send", self::NAME, $recp, false, "", "Wrong email");
				return false;
			} // Prepare
			$mail = array(
				"recipients" => [],
				"settings" => [],
				"head" => array(
					"Content-Type: application/json",
					"X-Requested-With: XMLHttpRequest",
					"Authorization: Bearer ".self::$MAILERSEND["API_KEY"]
				)
			); foreach ($recp as $recipient) {
				array_push($mail["recipients"], array("email" => $recipient));
				array_push($mail["settings"], array("email" => $recipient, "data" => $data[$recipient] ?? []));
			} $mail["body"] = array(
				"from" => self::$MAILERSEND["sender"],
				"to" => $mail["recipients"], // Can be ["to", "cc", "bcc"]
				"reply_to" => self::$MAILERSEND["return"],
				"subject" => $subj,
				"personalization" => $mail["settings"],
				"template_id" => self::$MAILERSEND["template"][$type],
			); // Setting
			$email = curl_init(self::$MAILERSEND["API_URL"]);
			curl_setopt_array($email, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_HTTPHEADER => $mail["head"],
				CURLOPT_POSTFIELDS => json_encode($mail["body"])
			)); if (($is_dev_env = $APP_CONST["environment"] == "DEV")) curl_setopt_array($email, array(
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true
			)); // Launch
			$result = curl_exec($email);
			if (!$is_in_API && $is_dev_env) { echo "<pre>"; print_r($mail); echo "</pre>"; }
			$recp = implode(", ", $recp);
			if (curl_errno($email)) {
				$error = json_encode(curl_error($email), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
				if ($is_in_API) API::infoMessage(3, $error);
				syslog_a(null, "email", "send", self::NAME, "$recp: $error", false, "", "cURL");
				return false;
			} else curl_close($email);
			if (!$result) {
				# if ($is_in_API) API::successState();
				syslog_a(null, "email", "send", self::NAME, $recp);
				return true;
			} else {
				if ($is_in_API) API::infoMessage(3, "Unable to send an email");
				syslog_a(null, "email", "send", self::NAME, "$recp: $result", false, "", "API mailersend");
				return false;
			} return null;
		}
	} mlsn::initialize();
?>