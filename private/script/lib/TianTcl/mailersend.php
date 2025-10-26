<?php
	$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	class mlsn {
		private static $default, $is = array(
			"initialized" => false
		), $MAILERSEND = array(
			"API_URL" => "https://api.mailersend.com/v1/email",
			"API_KEY" => "mlsn._____",
			"sender" => array("email" => "noreply@___", "name" => "___"),
			"return" => array("email" => "TianTcl@___", "name" => "TianTcl"),
			"template" => array(
				"notify" => "___",
				"action" => "___",
				"alert" => "___"
			)
		), $no_main_recp = "/^_{1,2}[A-Z0-9\\-_\\.]*$/i";
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

		final public static function email(array $data, string $subj, string $type, bool $append_log=true): bool|null {
			global $APP_CONST;
			$is_in_API = class_exists("API");
			// Convert data
			$recp = isAssocArr($data) ? array_keys($data) : $data;
			// Check
			if (!self::$is["initialized"]) self::initialize();
			if (!in_array($type, array_keys(self::$MAILERSEND["template"]))) {
				if ($is_in_API) API::errorMessage(3, "Invalid email type");
				# if ($append_log) syslog_a(null, "email", "send", self::NAME, $recp, false, "", "Wrong type");
				return false;
			} foreach ($recp as $recipient) {
				if (RegExTest("email", $recipient) || RegExTest(self::$no_main_recp, $recipient)) continue;
				if ($is_in_API) API::errorMessage(3, "Invalid email address");
				# if ($append_log) syslog_a(null, "email", "send", self::NAME, $recp, false, "", "Wrong email");
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
				if (RegExTest("email", $recipient)) {
					array_push($mail["recipients"], array("email" => $recipient));
					array_push($mail["settings"], array("email" => $recipient, "data" => $data[$recipient] ?? []));
				} else if (RegExTest(self::$no_main_recp, $recipient) && isset($data[$recipient])) {
					$recp_real ??= array();
					foreach (["cc", "bcc"] as $tag_type) if (isset($data[$recipient][$tag_type])) {
						$recp_real[$tag_type] ??= [];
						$recp_tmp = $data[$recipient][$tag_type];
						unset($data[$recipient][$tag_type]);
						foreach ($recp_tmp as $recp_eml) {
							if (!RegExTest("email", $recp_eml)) continue;
							array_push($recp_real[$tag_type], array("email" => $recp_eml));
							array_push($mail["settings"], array("email" => $recp_eml, "data" => $data[$recipient]));
						}
					}
				} $content ??= json_encode($data[$recipient], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
			} $mail["body"] = array(
				"from" => self::$MAILERSEND["sender"],
				"to" => $mail["recipients"],
				"reply_to" => self::$MAILERSEND["return"],
				"subject" => $subj,
				"personalization" => $mail["settings"],
				"template_id" => self::$MAILERSEND["template"][$type],
			); if (isset($recp_real)) {
				$mail["body"] = array_merge($mail["body"], $recp_real);
				$recp = array_merge($recp, ...array_values($recp_real));
			} // Setting
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
				if ($append_log) syslog_a(null, "email", "send", self::NAME, "$recp: $error", false, "", "cURL");
				self::log($subj, $recp, $content, false);
				return false;
			} else curl_close($email);
			$http_status_code = curl_getinfo($email, CURLINFO_HTTP_CODE);
			if ($http_status_code == 202) {
				# if ($is_in_API) API::successState();
				if ($append_log) syslog_a(null, "email", "send", self::NAME, $recp);
				self::log($subj, $recp, $content);
				return true;
			} else {
				if ($is_in_API) API::infoMessage(3, "Unable to send an email<hr>$http_status_code: $result");
				if ($append_log) syslog_a(null, "email", "send", self::NAME, "$recp: [$http_status_code] $result", false, "", "API mailersend");
				self::log($subj, $recp, $content, false);
				return false;
			} return null;
		}
		private static function log(string $subj, string $recp, string $content, bool $success=true): void {
			global $APP_DB;
			$APP_DB[0] -> query("INSERT INTO log_email (subject,recp,message,status) VALUE ('".escapeSQL($subj)."','".escapeSQL($recp)."','".escapeSQL($content)."','".($success ? "Y" : "N")."')");
		}
	} mlsn::initialize();
?>