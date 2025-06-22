<?php
	if (!isset($_SESSION)) session_start();
	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/lib/TianTcl/PHP-functions.php");
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/config/security-key.php");

	class TianTcl {
		private static $default, $is = array(
			"initialized" => false
		);
		function __construct() {
			# if (!$this -> is["initialized"]) self::initialize();
		}
		final public static function initialize(): void {
			if (self::$is["initialized"]) return;
			global $APP_CONST;
			self::$default = $APP_CONST["SECURITY_KEY"];
			self::$is["initialized"] = true;
		}

		/***
		 * Cryptos
		 * 
		 * bin2hex = hex2bin | dechex = hexdec | decoct = octdec | decbin = bindec | base_convert
		 ***/
		// v1
		final public static function encrypt_v1(string|int|float $str, int $nest=1): string {
			$nest = self::boundNumber($nest, 1, 5);
			if (gettype($str) <> "string") $str = strval($str);
			for ($_ = 0; $_ < $nest; $_++) $str = strrev(bin2hex(str_rot13(strrev(base64_encode(strrev($str))))));
			return $str;
		}
		final public static function decrypt_v1(string $str, int $nest=1): string|int|float {
			if ($nest < 1) $nest = 1; else if ($nest > 5) $nest = 5;
			for ($_ = 0; $_ < $nest; $_++) $str = strrev(base64_decode(strrev(str_rot13(hex2bin(strrev($str))))));
			return is_int($str) ? intval($str) : (is_float($str) ? floatval($str) : $str);
		}
		// v2
		final public static function encrypt(string|int|float $str, string $key="", string $salt="", int $nest=1): string {
			if (gettype($str) <> "string") $str = strval($str);
			$key = $key ?: self::$default["crypto_key"];
			if (!strlen($salt) && self::sessVar("crypto_salt") == null) self::sessVar("crypto_salt", self::generateSalt(true));
			else if (strlen($salt)) self::sessVar("crypto_salt", $salt);
			$salt = self::sessVar("crypto_salt");
			$strLength = strlen($str);
			$keyLength = strlen($key);
			$saltLength = strlen($salt);
			if ($nest <= 1) {
				$encrypted = "";
				for ($pos = 0; $pos < $strLength; $pos++) {
					$noOriginal = ord($str[$pos]);
					$noSalt = ord($salt[$pos % $saltLength]);
					$noKey = ord($key[$pos % $keyLength]);
					$noFactor = ($noSalt * $noKey) % ($noSalt + $noKey);
					$noChar = $noOriginal + $noFactor;
					$encrypted .= chr($noChar);
				} return rtrim(base64_encode($encrypted), "=");
			} for ($rounds = 0; $rounds < $nest; $rounds++) $str = base64_decode(self::encrypt($str, $key));
			return rtrim(base64_encode($str), "=");
		}
		final public static function decrypt(string $str, string $key="", string $salt="", int $nest=1): string|int|float|bool {
			if (gettype($str) <> "string") $str = strval($str);
			$str = base64_decode($str);
			$key = $key ?: self::$default["crypto_key"];
			if (!strlen($salt)) {
				$salt = self::sessVar("crypto_salt");
				if ($salt == null) return false;
			} $strLength = strlen($str);
			$keyLength = strlen($key);
			$saltLength = strlen($salt);
			if ($nest <= 1) {
				$decrypted = "";
				for ($pos = 0; $pos < $strLength; $pos++) {
					$noOriginal = ord($str[$pos]);
					$noSalt = ord($salt[$pos % $saltLength]);
					$noKey = ord($key[$pos % $keyLength]);
					$noFactor = ($noSalt * $noKey) % ($noSalt + $noKey);
					$noChar = $noOriginal - $noFactor;
					$decrypted .= chr($noChar);
				} return $decrypted;
			} for ($rounds = 0; $rounds < $nest; $rounds++) $str = self::decrypt(base64_encode($str), $key, $salt);
			return $str;
		}

		/***
		 * Generators
		 ***/
		public static function UUIDv4(int $amount=1): string|array {
			if ($amount <= 1) {
				// External: https://uuidgenerator.net/api/version4/<amount>
				$data = random_bytes(16);
				# assert(strlen($data) == 16);
				$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
				$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
				return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
			} $UUIDs = array();
			do {
				$initial_amount = count($UUIDs);
				for ($count = $initial_amount; $count < $amount; $count++) array_push($UUIDs, self::UUIDv4());
				array_unique($UUIDs);
			} while (count($UUIDs) < $amount);
			return $UUIDs;
		}
		public static function generateSalt(bool $entropy=false): string {
			return uniqid(rand(), $entropy);
		}

		/***
		 * IO
		 ***/
		private static $MIME_TYPES = array(), $file_extensions = array(
			"image" => ["png", "jpg", "jpeg", "heic", "heif", "gif", "webp", "svg", "ico", "tiff"],
			"video" => ["webm", "mp4", "3gpp", "mov", "avi"],
			"audio" => ["mp3", "wav", "flac", "ogg", "m4a", "wma", "aac", "midi"],
			"text" => ["txt", "csv", "tsv", "json", "md", "log", "ini", "conf", "cfg"],
			"code" => ["css", "html", "htm", "php", "sql", "js", "c", "cpp", "py", "ps1", "r", "rb", "vb", "java", "kt", "swift", "scala", "pl", "sh", "bat", "cmd", "ps1", "asm", "go", "dart", "lua", "ts", "tsx", "jsx", "cs", "htm", "mhtml", "phtml", "shtml", "asp", "aspx", "jsp", "jspx", "do", "pl"],
			"manifest" => ["manifest", "webmanifest", "lock", "package", "composer", "xml", "yaml", "yml", "toml"],
			"documents" => ["pdf", "pages", "epub", "ibooks"],
			"font" => ["ttf", "otf", "woff", "woff2"],
			"archive" => ["zip", "rar", "7z", "tar", "gz", "bz2", "xz", "phar"],
			"executable" => ["exe", "msi", "apk", "dmg", "pkg", "jar"],
			"generated" => ["pdb", "sol", "dll", "sys"],

			"msAccess" => ["accdb", "mdb"],
			"msExcel" => ["xls", "xlsx"],
			"msPowerpnt" => ["ppt", "pptx", "ppsx", "ppsm"],
			"msProject" => ["mpp"],
			"msPublisher" => ["pub"],
			"msVisio" => ["vsdx", "vsd"],
			"msWord" => ["doc", "docx"],
			"adobe" => ["ai", "psd", "prproj"],

			"etc" => ["dxf", "eps", "ps", "xps", "sib"],
		);
		public static function mime_file_type(string $path): string {
			global $APP_CONST;

			$name = basename($path);
			$type = pathinfo($name, PATHINFO_EXTENSION);
			$mime = mime_content_type($path);
			if (strlen($mime)) return $mime;

			$getInfo = new finfo();
			if (is_resource($getInfo)) {
				$mime = $getInfo -> file($path, FILEINFO_MIME_TYPE);
				if (strlen($mime)) return $mime;
			}

			if (!count(self::$MIME_TYPES)) self::$MIME_TYPES = json_decode(file_get_contents($APP_CONST["cdnURL"]."static/data/mime-types.json"), true);
			$mime = self::$MIME_TYPES[$type] ?? "";
			if (strlen($mime)) return $mime;

			return "";
		}
		private static function groupExtensions(array $types): array {
			$buffer = array();
			foreach ($types as $type) {
				if (!isset(self::$file_extensions[$type])) continue;
				$buffer = array_merge($buffer, self::$file_extensions[$type]);
			} return $buffer;
		}
		private static function cacheDurCalculator(string $type): int {
			if (in_array($type, self::groupExtensions(["image", "video", "audio"]))) return 31536000; // 1 year
			if (in_array($type, self::groupExtensions(["documents", "msAccess", "msExcel", "msPowerpnt", "msProject", "msPublisher", "msVisio", "msWord", "adobe"]))) return 2592000; // 1 month
			if (in_array($type, self::groupExtensions(["code", "font"]))) return 31536000; // 1 year
			if (in_array($type, self::$file_extensions["text"])) return 86400; // 1 day
			if (in_array($type, self::$file_extensions["manifest"])) return 604800; // 1 week
			if (in_array($type, self::$file_extensions["archive"])) return 2592000; // 1 month
			return 604800; // 1 week
		}
		public static function sendFile(string $path, int|null $cacheDuration=null): never {
			function leave(int $status): never {
				http_response_code($status);
				exit(0);
			} if (!file_exists($path)) leave(404);
		
			// Manifestation
			$name = basename($path);
			$type = pathinfo($name, PATHINFO_EXTENSION);
			$mime = self::mime_file_type($path);
			// Header setup
			header("Content-Type: $mime");
			header("Content-Length: ".filesize($path));
			# header("Access-Control-Allow-Origin: *"); // Already set in .htaccess
			// Caching
			if ($cacheDuration && $cacheDuration < 0) $cacheDuration = null;
			$cacheDur = $cacheDuration ?: self::cacheDurCalculator($type);
			$cacheExp = gmdate("D, d M Y H:i:s", time() + $cacheDur)." GMT";
			$etag = md5_file($path);
			$last_modified = gmdate("D, d M Y H:i:s", filemtime($path))." GMT";
			// Check if the browser sent an If-None-Match header
			if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] === $etag) {
				# header("HTTP/1.1 304 Not Modified"); exit(0);
				leave(304);
			}
			header("Cache-Control: public, max-age=$cacheDur");
			header("Pragma: public");
			header("Expires: $cacheExp");
			header("ETag: \"$etag\"");
			header("Last-Modified: $last_modified");
			// Content disposition
			if (in_array($type, ["mp4", "mp3", "avi", "mov"])) {
				header("Content-Disposition: inline");
				header("Accept-Ranges: bytes");
				$chunkSize = 1024 * 1024;
				$buffer = fopen($path, "rb");
				while (!feof($buffer)) {
					echo fread($buffer, $chunkSize);
					ob_flush(); flush();
				} fclose($buffer);
			} else {
				# fpassthru(fopen($path, "rb"));
				readfile($path);
			} exit(0);
		}

		/***
		 * Conditioning
		 ***/
		final public static function checkCond(int|float|string $value, string $condition, bool $required=true, bool $isMet=true, string $replace="#"): bool {
			if (strlen((string)$value) == 0) return !$required && $isMet;
			return eval("return ".str_replace($replace, var_export($value, true), $condition).";") xor !$isMet;
		}
		/**
		 * Check if the given date is within the range (accross year allowed)
		 * @param array $start [month, date]
		 * @param array $end [month, date]
		 * @return bool If the current date is within the range
		 */
		final public static function inDateRange(array $start, array $end): bool {
			$chrono = [
				"M" => (int)date("m"),
				"D" => (int)date("d")
			]; $within_year = (
				($start[0] < $end[0]) ||
				($start[0] == $end[0] && $start[1] <= $end[1])
			); $crossOverlap_year = (
				($start[0] > $end[0]) ||
				($start[0] == $end[0] && $start[1] > $end[1])
			); return (
				( // Start time check
					($chrono["M"] == $start[0] && $chrono["D"] >= $start[1]) ||
					($within_year && $chrono["M"] > $start[0]) ||
					($crossOverlap_year && $chrono["M"] != $start[0])
				) && ( // End time check
					($chrono["M"] < $end[0]) ||
					($chrono["M"] == $end[0] && $chrono["D"] <= $end[1]) ||
					($chrono["M"] > $end[0] && $crossOverlap_year && (
						($chrono["M"] == $start[0] && $chrono["D"] >= $start[1]) ||
						($chrono["M"] > $start[0])
					))
				)
			);
		}
		/***
		 * Vairous
		 ***/
		final public static function sessVar(string|int $key, mixed $value="__getValue", mixed $coalesce=null): mixed {
			if (!isset($_SESSION["var"])) $_SESSION["var"] = array();
			if (gettype($key) <> "string") $key = strval($key);
			if ($value == "__getValue") return $_SESSION["var"][$key] ?? $coalesce;
			else if ($value == "__delete") {
				if (!isset($_SESSION["var"][$key])) return false;
				unset($_SESSION["var"][$key]);
				return true;
			} return ($_SESSION["var"][$key] = $value);
		}
		public static function http_response_code(string|int $code=900): never {
			global $APP_CONST;
			if (gettype($code) <> "string") $code = strval($code);
			$originURL = urlencode(preg_replace("/^".str_replace("/", "\\/", $APP_CONST["baseURL"])."/", "", $_SERVER["REQUEST_URI"]));
			header("Location: ".$APP_CONST["baseURL"]."error/$code#ref=$originURL");
			exit(0);
		}
		public static function boundNumber(string|int|float $number, string|int|float $min=0, string|int|float $max=100): int|float|null {
			if (!is_numeric($number) || !is_numeric($min) || !is_numeric($max) || (float)$min > (float)$max) return null;
			return min((float)$max, max((float)$min, (float)$number));
		}
	} TianTcl::initialize();

	/* Shorten up functions */
	// Crypto AES
	if (!function_exists("AES_encrypt")) { function AES_encrypt(string|int|float $str, string $key="", string $salt="", $method="aes-256-cbc", $URLsafe=false): string {
		if (gettype($str) <> "string") $str = strval($str);
		$ivSize = openssl_cipher_iv_length($method);
		$iv = openssl_random_pseudo_bytes($ivSize);
		$encrypted = openssl_encrypt($str, $method, sha256($key.$salt), OPENSSL_RAW_DATA, $iv);
		$encrypted = base64_encode($iv.$encrypted);
		if ($URLsafe) $encrypted = str_replace(["+", "/", "="], ["-", "_", ""], $encrypted);
		return $encrypted;
	} }
	if (!function_exists("AES_decrypt")) { function AES_decrypt(string $str, string $key="", string $salt="", $method="aes-256-cbc", $URLsafe=false): string {
		if (gettype($str) <> "string") $str = strval($str);
		if ($URLsafe) $str = str_replace(["-", "_"], ["+", "/"], $str);
		$str = base64_decode($str);
		$ivSize = openssl_cipher_iv_length($method);
		$iv = substr($str, 0, $ivSize);
		$encrypted = substr($str, $ivSize);
		$decrypted = openssl_decrypt($encrypted, $method, sha256($key.$salt), OPENSSL_RAW_DATA, $iv);
		return $decrypted;
	} }
?>