<?php
	if (!isset($_SESSION)) session_start();
	$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/lib/TianTcl/PHP-functions.php");
	require_once($APP_RootDir."private/config/security-key.php");

	/**
	 * TianTcl is a class that provides various utility functions to aid in the development of web applications.
	 * 
	 * @author TianTcl
	 */
	class TianTcl {
		private static $default, $is = array(
			"initialized" => false
		);
		function __construct() {
			# if (!$this -> is["initialized"]) self::initialize();
		}
		/**
		 * Initializes the TianTcl class by setting up default values and marking the class as initialized. This method is called to prepare the class for use, ensuring that any necessary setup is performed before other methods are called.
		 */
		final public static function initialize(): void {
			if (self::$is["initialized"]) return;
			global $APP_CONST;
			self::$default = $APP_CONST["SECURITY_KEY"];
			self::$is["initialized"] = true;
		}

		// Cryptos
		// bin2hex = hex2bin | dechex = hexdec | decoct = octdec | decbin = bindec | base_convert
		/**
		 * Generates a unique salt string that can be used for cryptographic purposes. The method uses the uniqid function combined with a random number to create a unique identifier, which can be further enhanced with additional entropy if the $entropy parameter is set to true. This generated salt can be used in various cryptographic operations.
		 * 
		 * @param bool $entropy A boolean flag indicating whether to add additional entropy to the generated salt. If set to true, the uniqid function will generate a more unique identifier by adding additional entropy, making it less likely to produce duplicate values.
		 * @return string A unique salt string generated using the uniqid function and a random number, optionally enhanced with additional entropy for increased uniqueness.
		 */
		public static function generateSalt(bool $entropy=false): string {
			return uniqid(rand(), $entropy);
		}
		// Encypt-Decrypt v1
		/**
		 * Encrypts a string using a custom algorithm that involves multiple layers of encoding and transformation. The method takes a string input and applies a series of operations, including base64 encoding, string reversal, ROT13 transformation, and hexadecimal encoding, to produce an encrypted version of the input string. The number of times these operations are applied can be controlled by the $nest parameter, allowing for increased complexity in the encryption process.
		 * 
		 * @param string|int|float $str The input string (or number) to be encrypted. If the input is not a string, it will be converted to a string before encryption.
		 * @param int $nest The number of times to apply the encryption operations. This controls the complexity of the encryption, with a higher value resulting in a more complex encrypted string. The value is bounded between 1 and 5.
		 * @return string The encrypted version of the input string, produced by applying the specified encryption operations the specified number of times.
		 */
		final public static function encrypt_v1(string|int|float $str, int $nest=1): string {
			$nest = self::boundNumber($nest, 1, 5);
			if (gettype($str) <> "string") $str = strval($str);
			for ($_ = 0; $_ < $nest; $_++) $str = strrev(bin2hex(str_rot13(strrev(base64_encode(strrev($str))))));
			return $str;
		}
		/**
		 * Decrypts a string that was encrypted using the encrypt_v1 method. This method reverses the operations applied during encryption, including hexadecimal decoding, ROT13 transformation, base64 decoding, and string reversal, to retrieve the original input string. The number of times these operations are reversed is controlled by the $nest parameter, which should match the value used during encryption to successfully decrypt the string.
		 * 
		 * @param string $str The encrypted string to be decrypted. This should be a string that was produced by the encrypt_v1 method.
		 * @param int $nest The number of times to reverse the encryption operations. This should match the value used during encryption to ensure successful decryption. The value is bounded between 1 and 5.
		 * @return string|int|float The original input string that was encrypted, retrieved by reversing the encryption operations. The return type can be a string, integer, or float, depending on the original input before encryption.
		 */
		final public static function decrypt_v1(string $str, int $nest=1): string|int|float {
			$nest = self::boundNumber($nest, 1, 5);
			for ($_ = 0; $_ < $nest; $_++) $str = strrev(base64_decode(strrev(str_rot13(hex2bin(strrev($str))))));
			return is_int($str) ? (int)$str : (is_float($str) ? (float)$str : $str);
		}
		// Encypt-Decrypt v2
		/**
		 * Encrypts a string using a custom algorithm that combines character manipulation with base64 encoding. The method takes a string input and applies a series of operations, including character code transformations based on a key and salt, to produce an encrypted version of the input string.
		 * 
		 * @param string|int|float $str The input string (or number) to be encrypted. If the input is not a string, it will be converted to a string before encryption.
		 * @param string $key An optional key used in the encryption process. If not provided, a default key from the class's configuration will be used.
		 * @param string $salt An optional salt used in the encryption process. If not provided, a salt will be generated and stored in the session for use in encryption and decryption.
		 * @param int $nest The number of times to apply the encryption operations. This controls the complexity of the encryption, with a higher value resulting in a more complex encrypted string. The value is bounded between 1 and 10.
		 * @param bool $URLsafe A boolean flag indicating whether the output should be URL-safe.
		 * @return string The encrypted version of the input string, produced by applying the specified encryption operations the specified number of times.
		 */
		final public static function encrypt(string|int|float $str, string $key="", string $salt="", int $nest=1, bool $URLsafe=false): string {
			if (gettype($str) <> "string") $str = strval($str);
			// Key and salt handling
			$key = $key ?: self::$default["crypto_key"];
			if (!strlen($salt) && self::sessVar("crypto_salt") == null) self::sessVar("crypto_salt", self::generateSalt(true));
			else if (strlen($salt)) self::sessVar("crypto_salt", $salt);
			$salt = self::sessVar("crypto_salt");
			// Algorithm begins
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
				} return $URLsafe ? base64url_encode($encrypted) : rtrim(base64_encode($encrypted), "=");
			} for ($rounds = 0; $rounds < $nest; $rounds++) $str = ($URLsafe ? "base64url_decode" : "base64_decode")(self::encrypt($str, $key, $salt));
			return $URLsafe ? base64url_encode($str) : rtrim(base64_encode($str), "=");
		}
		/**
		 * Decrypts a string that was encrypted using the encrypt method. This method reverses the operations applied during encryption, including character code transformations based on the key and salt, to retrieve the original input string.
		 * 
		 * @param string $str The encrypted string to be decrypted. This should be a string that was produced by the encrypt method.
		 * @param string $key An optional key used in the decryption process. If not provided, the default key from the class's configuration will be used.
		 * @param string $salt An optional salt used in the decryption process. If not provided, the salt stored in the session will be used.
		 * @param int $nest The number of times to reverse the encryption operations. This should match the value used during encryption to ensure successful decryption. The value is bounded between 1 and 10.
		 * @param bool $URLsafe A boolean flag indicating whether the input is URL-safe encoded.
		 * @return string|int|float|false The original input string that was encrypted, retrieved by reversing the encryption operations.
		 */
		final public static function decrypt(string $str, string $key="", string $salt="", int $nest=1, bool $URLsafe=false): string|int|float|false {
			# if (gettype($str) <> "string") $str = (string)$str;
			$str = ($URLsafe ? "base64url_decode" : "base64_decode")($str);
			// Key and salt handling
			$key = $key ?: self::$default["crypto_key"];
			if (!strlen($salt)) {
				$salt = self::sessVar("crypto_salt");
				if ($salt == null) return false;
			} // Algorithm begins
			$strLength = strlen($str);
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

		// Generators
		/**
		 * Generates one or more UUIDs of a specified version. This method serves as a helper function to generate multiple UUIDs at once by calling the appropriate UUID generation method based on the specified version.
		 * 
		 * @param int $version The version of UUID to generate.
		 * @param int $amount The number of UUIDs to generate.
		 * @return string|array<string>|false A single UUID string if the amount is 1 or less, an array of UUID strings if the amount is greater than 1, or false if an error occurs.
		 */
		final public static function UUID(int $version=7, int $amount=1): string|array|false {
			if (!function_exists("self::UUIDv$version")) return false;
			if ($amount == 1) return self::{"UUIDv$version"}();
			$UUIDs = [];
			do {
				$populated = count($UUIDs);
				array_fill($populated, $amount - $populated, fn() => self::{"UUIDv$version"}());
				array_unique($UUIDs);
			} while (count($UUIDs) < $amount);
			return $UUIDs;
		}
		/**
		 * Generates one or more UUID version 1 (UUIDv1) identifiers. (Time-based)
		 * Standard: RFC 4122
		 * @link https://www.ietf.org/rfc/rfc4122.txt UUIDv1 specification
		 * 
		 * @param int $amount The number of UUIDv1 identifiers to generate.
		 * @return string|array<string> A single UUIDv1 string if the amount is 1 or less, or an array of UUIDv1 strings if the amount is greater than 1.
		 */
		final public static function UUIDv1(int $amount=1): string|array {
			if ($amount > 1) return self::UUID(1, $amount);
			// Number of 100-ns intervals between UUID epoch (1582-10-15) and Unix epoch (1970-01-01)
			$time = (int)(microtime(true) * 1e7) + 0x01B21DD213814000; // UUID epoch offset
			$clock_seq = random_int(0, 0x3FFF); // 14 bits for clock sequence
			// Begin generating the node (MAC address or random)
			$node = random_bytes(6);
			$node[0] = chr(ord($node[0]) | 0x01); // multicast bit (no real MAC)
			return sprintf(
				"%08x-%04x-%04x-%02x%02x-%012s",
				$time & 0xFFFFFFFF, // time_low
				($time >> 32) & 0xFFFF, // time_mid
				(($time >> 48) & 0x0FFF) | (1 << 12), // time_hi
				($clock_seq >> 8) | 0x80, // clock_seq_hi
				$clock_seq & 0xFF, // clock_seq_low
				bin2hex($node)
			);
		}
		/**
		 * Generates one or more UUID version 4 (UUIDv4) identifiers. (Random-based)
		 * Standard: RFC 4122
		 * @link https://www.ietf.org/rfc/rfc4122.txt UUIDv4 specification
		 * 
		 * @link https://uuidgenerator.net/api/version4/<amount> External UUIDv4 generator API
		 * 
		 * @param int $amount The number of UUIDv4 identifiers to generate.
		 * @return string|array<string> A single UUIDv4 string if the amount is 1 or less, or an array of UUIDv4 strings if the amount is greater than 1.
		 */
		final public static function UUIDv4(int $amount=1): string|array {
			if ($amount > 1) return self::UUID(4, $amount);
			$data = random_bytes(16);
			# assert(strlen($data) == 16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
			return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
		}
		/**
		 * Generates one or more UUID version 6 (UUIDv6) identifiers. (Time-ordered)
		 * Standard: RFC 9562
		 * @link https://www.ietf.org/rfc/rfc9562.txt#section-4.1.1 UUIDv6 specification
		 * 
		 * @param int $amount The number of UUIDv6 identifiers to generate.
		 * @return string|array<string> A single UUIDv6 string if the amount is 1 or less, or an array of UUIDv6 strings if the amount is greater than 1.
		 */
		final public static function UUIDv6(int $amount=1): string|array {
			if ($amount > 1) return self::UUID(6, $amount);
			// Number of 100-ns intervals between UUID epoch (1582-10-15) and Unix epoch (1970-01-01)
			$time = (int)(microtime(true) * 1e7) + 0x01B21DD213814000; // UUID epoch offset
			$clock_seq = random_int(0, 0x3fff);
			$node = random_bytes(6);
			// Reordered timestamp (big-endian)
			return sprintf(
				"%08x-%04x-%04x-%02x%02x-%012s",
				($time >> 28) & 0xFFFFFFFF, // time_high
				($time >> 12) & 0xFFFF, // time_mid
				($time & 0x0FFF) | (6 << 12), // time_low
				($clock_seq >> 8) | 0x80, // clock_seq_hi
				$clock_seq & 0xFF, // clock_seq_low
				bin2hex($node)
			);
		}
		/**
		 * Generates one or more UUID version 7 (UUIDv7) identifiers. (Unix timestamp-based + random)
		 * Standard: RFC 9562
		 * @link https://www.ietf.org/rfc/rfc9562.txt#section-4.1.2 UUIDv7 specification
		 * 
		 * @param int $amount The number of UUIDv7 identifiers to generate.
		 * @return string|array<string> A single UUIDv7 string if the amount is 1 or less, or an array of UUIDv7 strings if the amount is greater than 1.
		 */
		final public static function UUIDv7(int $amount=1): string|array {
			if ($amount > 1) return self::UUID(7, $amount);
			$timestamp = (int)floor(microtime(true) * 1e3);
			$rand = random_bytes(10); // 80 bits
			$rand[0] = chr((ord($rand[0]) & 0x0F) | (7 << 4)); // 0x70
			$rand[2] = chr((ord($rand[2]) & 0x3F) | 0x80); // 0x80-0xBF
			return sprintf(
				'%08x-%04x-%04x-%04x-%012s',
				($timestamp >> 16) & 0xFFFFFFFF, // time_high
				($timestamp >> 4) & 0xFFFF, // time_low
				(ord($rand[0]) << 8) | ord($rand[1]), // clock_seq_hi
				(ord($rand[2]) << 8) | ord($rand[3]), // clock_seq_low
				bin2hex(substr($rand, 4))
			);
		}

		// IO
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
		/**
		 * Determines the MIME type of a file based on its content and extension. The method first attempts to use the built-in mime_content_type function to get the MIME type. If that fails, it falls back to using the finfo class for a more reliable detection. If both methods fail, it checks a predefined list of MIME types based on the file extension. If all methods fail to determine the MIME type, it returns an empty string.
		 * 
		 * @param string $path The file path for which to determine the MIME type.
		 * @return string The determined MIME type of the file, or an empty string if the MIME type cannot be determined.
		 */
		final public static function mime_file_type(string $path): string {
			global $APP_CONST;
			$name = basename($path);
			$type = pathinfo($name, PATHINFO_EXTENSION);
			// Standard PHP identification
			$mime = mime_content_type($path);
			if (strlen($mime)) return $mime;
			// finfo identification
			$getInfo = new finfo();
			if (is_resource($getInfo)) {
				$mime = $getInfo -> file($path, FILEINFO_MIME_TYPE);
				if (strlen($mime)) return $mime;
			} // Fallback to extension-based identification
			if (!count(self::$MIME_TYPES)) self::$MIME_TYPES = json_decode(file_get_contents($APP_CONST["cdnURL"]."static/data/mime-types.json"), true);
			$mime = self::$MIME_TYPES[$type] ?? "";
			return $mime;
		}
		/**
		 * Groups file extensions based on their types. This method takes an array of type categories (e.g., "image", "video", "audio") and returns a merged array of all file extensions that belong to those categories. It checks the predefined $file_extensions array for each specified type and combines the extensions into a single array, which is then returned.
		 * 
		 * @param array $types An array of type categories for which to group file extensions. Each category corresponds to a key in the $file_extensions array.
		 * @return array An array of file extensions that belong to the specified type categories. If a specified type does not exist in the $file_extensions array, it is skipped, and only valid extensions are included in the returned array.
		 */
		private static function groupExtensions(array $types): array {
			$buffer = array();
			foreach ($types as $type) {
				if (!isset(self::$file_extensions[$type])) continue;
				$buffer = array_merge($buffer, self::$file_extensions[$type]);
			} return $buffer;
		}
		/**
		 * Calculates the appropriate cache duration for a file based on its type. The method checks the file type against predefined groups of extensions (e.g., "image", "video", "audio", "documents") and returns a corresponding cache duration in seconds. If the file type does not match any predefined groups, a default cache duration of 1 week is returned.
		 * 
		 * @param string $type The file extension type for which to calculate the cache duration.
		 * @return int The calculated cache duration in seconds based on the file type. A default duration is 1 week (604800 seconds).
		 */
		final public static function cacheDurCalculator(string $type): int {
			if (in_array($type, self::groupExtensions(["image", "video", "audio"]))) return 31536000; // 1 year
			if (in_array($type, self::groupExtensions(["documents", "msAccess", "msExcel", "msPowerpnt", "msProject", "msPublisher", "msVisio", "msWord", "adobe"]))) return 2592000; // 1 month
			if (in_array($type, self::groupExtensions(["code", "font"]))) return 31536000; // 1 year
			if (in_array($type, self::$file_extensions["text"])) return 86400; // 1 day
			if (in_array($type, self::$file_extensions["manifest"])) return 604800; // 1 week
			if (in_array($type, self::$file_extensions["archive"])) return 2592000; // 1 month
			return 604800; // 1 week
		}
		/**
		 * Sends a file to the client with appropriate headers for content type, caching, and content disposition. The method checks if the file exists and retrieves its MIME type. It then sets the necessary headers for content type, content length, caching, and ETag. If the file is a media type (e.g., video or audio), it supports byte-range requests for efficient streaming. Finally, it reads the file and sends it to the client, allowing for inline display or forced download based on the specified parameters.
		 * 
		 * @param string $path The file path of the file to be sent to the client.
		 * @param ?int $cacheDuration An optional cache duration in seconds. If not provided or if set to null, the cache duration will be calculated using the cacheDurCalculator method. If set to a negative value, caching will be disabled.
		 * @param ?string $export_name An optional name for the file when it is sent to the client. If not provided or if set to null, the original file name will be used.
		 * @param bool $download A boolean flag indicating whether the file should be sent as an attachment for download (true) or displayed inline (false). The default value is false, meaning the file will be displayed inline if possible.
		 * @return never This method does not return a value as it sends the file directly to the client and terminates the script execution.
		 */
		final public static function sendFile(string $path, int|null $cacheDuration=null, string|null $export_name=null, bool $download=false): never {
			if (!file_exists($path)) self::exitCode(404);
			// Manifestation
			$name = basename($path);
			$type = pathinfo($name, PATHINFO_EXTENSION);
			$mime = self::mime_file_type($path);
			// Header setup
			header("Content-Type: $mime");
			header("Content-Length: ".filesize($path));
			# header("Access-Control-Allow-Origin: *"); // Already set in .htaccess
			// Caching
			if (is_int($cacheDuration) && $cacheDuration < 0) {
				# $cacheDuration = null;
				// Disable caching
				header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
				header("Pragma: no-cache");
				header("Expires: 0");
			} else {
				$cacheDur = $cacheDuration ?: self::cacheDurCalculator($type);
				$cacheExp = gmdate("D, d M Y H:i:s", time() + $cacheDur)." GMT";
				$etag = md5_file($path);
				$last_modified = gmdate("D, d M Y H:i:s", filemtime($path))." GMT";
				// Check if the browser sent an If-None-Match header (304 handling)
				if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] === $etag) {
					# header("HTTP/1.1 304 Not Modified"); exit(0);
					self::exitCode(304);
				}
				header("Cache-Control: public, max-age=$cacheDur");
				header("Pragma: public");
				header("Expires: $cacheExp");
				header("ETag: \"$etag\"");
				header("Last-Modified: $last_modified");
			} // Content disposition
			header("Content-Disposition: ".($download ? "attachment" : "inline")."; filename=\"".($export_name ?: $name)."\"");
			if (in_array($type, ["mp4", "mp3", "avi", "mov"])) {
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
		/**
		 * Sends an HTTP response with a specified status code and terminates the script execution.
		 * 
		 * @param int $status The HTTP status code to be sent in the response.
		 * @return never This method does not return a value as it sends the HTTP response and terminates the script execution.
		 */
		final public static function exitCode(int $status): never {
			http_response_code($status);
			exit(0);
		}

		// Conditioning & Logistics
		/**
		 * Checks a given value against a specified condition using a custom evaluation method. The method evaluates the condition by replacing a placeholder in the condition string with the actual value and executing it as PHP code. It also takes into account whether the value is required and whether the condition is expected to be met or not, allowing for flexible validation of the input value based on the provided parameters.
		 * Basically a shorthand for multiple non-function logical checks to a single variable
		 * 
		 * @param int|float|string $value The value to be checked against the condition. This can be an integer, float, or string.
		 * @param string $condition The condition to be evaluated, which should contain a placeholder (default is "#") that will be replaced with the actual value during evaluation. The condition should be a valid PHP expression that can be evaluated to true or false.
		 * @param bool $required A boolean flag indicating whether the value is required (true) or optional (false). If set to true, an empty value will cause the method to return false unless the condition is not expected to be met.
		 * @param bool $asserts A boolean flag indicating whether the condition is expected to be met (true) or not (false).
		 * @param string $replace The placeholder in the condition string that will be replaced with the actual value during evaluation. The default placeholder is "#".
		 * @return bool The result of the condition check
		 */
		final public static function checkCond(int|float|string $value, string $condition, bool $required=true, bool $asserts=true, string $replace="#"): bool {
			if (!strlen((string)$value)) return !$required && $asserts;
			if (!str_ends_with($condition, ";")) $condition .= ";";
			if (!str_starts_with($condition, "return ")) $condition = "return $condition";
			return eval(str_replace($replace, var_export($value, true), $condition)) xor !$asserts;
		}
		/**
		 * Check if the given date is within the range (accross year allowed)
		 * 
		 * @param array $start An array containing the start month and day in the format [month, day].
		 * @param array $end An array containing the end month and day in the format [month, day].
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
		/**
		 * Checks if a given timestamp falls within a specified time range. The method takes start and stop times, which can be provided as either integers (Unix timestamps) or strings (date/time formats), and an optional timestamp to check against. If the timestamp is not provided, the current time will be used.
		 * 
		 * @param int|string $start The start time of the range, which can be an integer (Unix timestamp) or a string (date/time format).
		 * @param int|string $stop The end time of the range, which can be an integer (Unix timestamp) or a string (date/time format).
		 * @param int|string|null $timestamp An optional timestamp to check against the range. If not provided or set to null, the current time will be used. The timestamp can be an integer (Unix timestamp) or a string (date/time format).
		 * @return bool True if the timestamp falls within the specified time range, false otherwise.
		 */
		final public static function inTimeRange(int|string $start, int|string $stop, int|string|null $timestamp=null): bool {
			if (empty($timestamp)) $timestamp = time();
			else if (gettype($timestamp) <> "integer") $timestamp = strtotime($timestamp);
			if (gettype($start) <> "integer") $start = strtotime($start);
			if (gettype($stop) <> "integer") $stop = strtotime($stop);
			return self::inRange($timestamp, $start, $stop);
		}
		/**
		 * Checks if a given value falls within a specified range. The method takes a value and minimum and maximum bounds, which can be provided as integers, floats, or numeric strings. It validates the inputs to ensure they are numeric and that the minimum is not greater than the maximum before checking if the value falls within the range.
		 * 
		 * @param int|float|string $value The value to be checked against the specified range. This can be an integer, float, or numeric string.
		 * @param int|float|string $min The minimum bound of the range. This can be an integer, float, or numeric string. The method will validate that this value is numeric and will be used as the lower limit of the range.
		 * @param int|float|string $max The maximum bound of the range. This can be an integer, float, or numeric string. The method will validate that this value is numeric and will be used as the upper limit of the range.
		 * @param bool $inclusive A boolean flag indicating whether the range should be inclusive (true) or exclusive (false).
		 * @return bool True if the value falls within the specified range based on the provided bounds, false otherwise.
		 */
		public static function inRange(int|float|string $value, int|float|string $min=0, int|float|string $max=100, bool $inclusive=true): bool {
			if (!is_numeric($value) || !is_numeric($min) || !is_numeric($max) || (float)$min > (float)$max) return false;
			return $inclusive ?
				((float)$min <= (float)$value && (float)$value <= (float)$max) :
				((float)$min < (float)$value && (float)$value < (float)$max);
		}
		/**
		 * Alias: {@see inRange}
		 */
		public static function inBetween(int|float|string $value, int|float|string $min=0, int|float|string $max=100, bool $inclusive=true): bool { return self::inRange($value, $min, $max, $inclusive); }
		/**
		 * Bounds a given number within a specified minimum and maximum range. The method takes a number and minimum and maximum bounds, which can be provided as integers, floats, or numeric strings. It validates the inputs to ensure they are numeric and return the number constrained within the specified range.
		 * 
		 * @param int|float|string $number The number to be bounded within the specified range. This can be an integer, float, or numeric string.
		 * @param int|float|string $min The minimum bound of the range. This can be an integer, float, or numeric string. This will be used as the lower limit of the range.
		 * @param int|float|string $max The maximum bound of the range. This can be an integer, float, or numeric string. This will be used as the upper limit of the range.
		 * @return int|float|null The number constrained within the specified range, or null if the inputs are invalid.
		 */
		public static function boundNumber(string|int|float $number, string|int|float $min=0, string|int|float $max=100): int|float|null {
			if (!is_numeric($number) || !is_numeric($min) || !is_numeric($max) || (float)$min > (float)$max) return null;
			return min((float)$max, max((float)$min, (float)$number));
		}
		/**
		 * Converts a string representation of a value into its appropriate data type.
		 * 
		 * @param string $value The string value to be converted into its appropriate data type.
		 * @return mixed The converted value in its appropriate data type, which can be a boolean, float, integer, null, or the original string if it does not match any specific type patterns.
		 */
		public static function beautifyAnswer(string $value): mixed {
			if (RegExTest("/^([Tt]rue|TRUE|[Ff]alse|FALSE)$/", $value)) return strtolower($value) == "true";
			# if (RegExTest("/^\d*\.\d+$/", $value)) return (float)$value;
			if (is_float($value)) return (float)$value;
			# if (RegExTest("/^\d+$/", $value)) return (int)$value;
			if (is_int($value)) return (int)$value;
			if ($value == "null") return null;
			return $value;
		}

		// Various
		/**
		 * Manages session variables in a structured way. This method allows you to set, get, or remove session variables using a consistent interface. It checks if the session variable array exists and initializes it if necessary. Depending on the provided parameters, it can return the value of a session variable, set a new value, or remove an existing variable from the session.
		 * 
		 * @param string|int $key The key for the session variable to be accessed or modified. This can be a string or an integer, and it will be converted to a string if it is not already.
		 * @param mixed $value The value to be set for the session variable. If this parameter is set to "__GET", the method will return the current value of the session variable associated with the specified key. If it is set to "__REMOVE", the method will remove the session variable associated with the specified key. For any other value, the method will set the session variable to that value.
		 * @param mixed $coalesce An optional value to return if the session variable does not exist when trying to get its value. This parameter is only used when the $value parameter is set to "__GET". If the session variable does not exist, the method will return this coalesce value instead of null.
		 * @return mixed The result of the session variable operation, which can be the value of the session variable when getting, a boolean indicating success when removing, or the new value when setting. If getting a non-existent variable, it will return the coalesce value or null if coalesce is not provided.
		 */
		final public static function sessVar(string|int $key, mixed $value="__GET", mixed $coalesce=null): mixed {
			if (!isset($_SESSION["var"])) $_SESSION["var"] = array();
			if (gettype($key) <> "string") $key = (string)$key;
			if ($value == "__GET") return $_SESSION["var"][$key] ?? $coalesce;
			else if ($value == "__REMOVE") {
				if (!isset($_SESSION["var"][$key])) return false;
				unset($_SESSION["var"][$key]);
				return true;
			} return ($_SESSION["var"][$key] = $value);
		}
		/**
		 * Redirects the client to a custom error page. The method constructs a URL for the error page based on the provided status code and the original request URI. Finally, it terminates the script execution.
		 * 
		 * @param string|int $code The HTTP status code to be included in the error page URL. This can be a string or an integer, and it will be converted to a string if it is not already.
		 * @return never This method does not return a value as it sends a redirect header to the client and terminates the script execution.
		 */
		public static function http_response_code(string|int $code=900): never {
			global $APP_CONST;
			if (gettype($code) <> "string") $code = (string)$code;
			$originURL = urlencode(preg_replace("/^".str_replace("/", "\\/", $APP_CONST["baseURL"])."/", "", $_SERVER["REQUEST_URI"]));
			header("Location: ".$APP_CONST["baseURL"]."error/$code#ref=$originURL");
			exit(0);
		}
	} TianTcl::initialize();

	/* Shorten up functions (Alias/Junction) */
	// Crypto AES
	if (!function_exists("AES_encrypt")) { function AES_encrypt(string|int|float $str, string $key="", string $salt="", $method="aes-256-cbc", $URLsafe=false): string {
		if (gettype($str) <> "string") $str = strval($str);
		$ivSize = openssl_cipher_iv_length($method);
		$iv = openssl_random_pseudo_bytes($ivSize);
		$encrypted = openssl_encrypt($str, $method, sha256($key.$salt), OPENSSL_RAW_DATA, $iv);
		$encrypted = ($URLsafe ? "base64url_encode" : "base64_encode")($iv.$encrypted);
		return $encrypted;
	} }
	if (!function_exists("AES_decrypt")) { function AES_decrypt(string $str, string $key="", string $salt="", $method="aes-256-cbc", $URLsafe=false): string {
		if (gettype($str) <> "string") $str = strval($str);
		$str = ($URLsafe ? "base64url_decode" : "base64_decode")($str);
		$ivSize = openssl_cipher_iv_length($method);
		$iv = substr($str, 0, $ivSize);
		$encrypted = substr($str, $ivSize);
		$decrypted = openssl_decrypt($encrypted, $method, sha256($key.$salt), OPENSSL_RAW_DATA, $iv);
		return $decrypted;
	} }
	// Base64 URL-safe
	if (!function_exists("base64url_encode")) { function base64url_encode(string $data): string {
		return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
	} }
	if (!function_exists("base64url_decode")) { function base64url_decode(string $data): string {
		return base64_decode(strtr($data, "-_", "+/"));
	} }
?>