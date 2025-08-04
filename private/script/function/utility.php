<?php
	use Google\Service\Dataproc\RegexValidation;

	// App global variables
	if (!isset($_SESSION)) session_start();
	if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	if (!isset($APP_CONST)) require_once($APP_RootDir."private/config/constant.php");
	$APP_USER = $_SESSION["auth"]["user"] ?? ($_SESSION["auth"]["override"] ?? $APP_CONST["USER_TYPE"][3]);

	if (!isset($USER_IP)) {
		if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) $USER_IP = $_SERVER["HTTP_CF_CONNECTING_IP"];
		else if (!empty($_SERVER["HTTP_CLIENT_IP"])) $USER_IP = $_SERVER["HTTP_CLIENT_IP"];
		else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $USER_IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else $USER_IP = $_SERVER["REMOTE_ADDR"] ?? ""; // null
	}

	// Predefined constants
	define("DATE_MYSQL", "Y-m-d H:i:s");
	
	// Prototypes
	function strtoproper(string $str): string {
		return ucwords($str);
	}
	function strtosentence(string $str): string {
		$str = explode(". ", $str);
		foreach ($str as $idx => $es) $str[$idx] = ucfirst($es);
		return implode(". ", $str);
	}
	function strtocamel(string $str): string {
		return strtolower($str[0]).preg_replace("/\W/", "", substr(strtoproper($str), 1));
	}
	function strtosnail(string $str): string {
		return trim(preg_replace("/\W+/", "_", strtolower($str)), "_");
	}
	function strtorandom(string $str): string {
		for ($_ = 0; $_ < strlen($str); $_++)
			$str[$_] = rand(0, 1) ? strtoupper($str[$_]) : strtolower($str[$_]);
		return $str;
	}
	function strswapcase(string $str): string {
		# return strtr($str, array_merge(array_combine(range("a", "z"), range("A", "Z")), array_combine(range("A", "Z"), range("a", "z"))));
		return strtr($str, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
		/* for ($_ = 0; $_ < strlen($str); $_++) {
			$c = ord($str[$_]);
			# if ($c >= 65 && $c <= 90) $s[$_] = chr($c + 32);
			# else if ($c >= 97 && $c <= 122) $s[$_] = chr($c - 32);
			if ($c >= 65 && $c <= 90) $str[$_] = strtolower($str[$_]);
			else if ($c >= 97 && $c <= 122) $str[$_] = strtoupper($str[$_]);
		} return $str; */
	}
	function array_shallow_copy(array $arr): array {
		return clone $arr;
	}
	function array_deep_copy(array $arr): array {
		return unserialize(serialize($arr));
	}
	class CallableClass extends stdClass {
		private $methods = [];
		public function __set($name, $value) {
			if (is_callable($value) && !is_string($value)) $this -> methods[$name] = $value;
			else $this -> {$name} = $value;
		}
		public function __call($name, $arguments) {
			if (isset($this -> methods[$name])) return call_user_func_array($this -> methods[$name], $arguments);
			throw new Exception("Call to undefined method $name()");
		}
	}
	function arrayToObject(array $arr): object {
		$obj = new CallableClass();
		foreach ($arr as $k => $v) {
			if (!strlen($k)) continue;
			if (is_callable($v) && !is_string($v)) $obj -> {$k} = $v; # instanceof Closure ? $v : Closure::fromCallable($v);
			# else if (is_object($v) || is_array($v)) $obj -> {$k} = $v;
			else if (isAssocArr($v)) $obj -> {$k} = arrayToObject($v);
			else $obj -> {$k} = $v;
		} return $obj;
	}

	// Checkers
	function isAssocArr(array $arr): bool {
		if ($arr == array() || gettype($arr) <> "array") return false;
		return array_keys($arr) <> range(0, count($arr) - 1);
	}

	// Miscellanous functions
	function RegExTest($pattern, $subject, &$into=null): bool {
		global $APP_CONST;
		$subject = mb_convert_encoding((string)$subject, "UTF-8", "auto");
		if (@preg_match($pattern, "", $into) === false) {
			if (!isset($APP_CONST["REGEX"][$pattern])) return false;
			$pattern = $APP_CONST["REGEX"][$pattern];
		} if (preg_match("/\/[imsxADU]*$/", $pattern)) $pattern .= "u";
		return (bool)preg_match($pattern, $subject, $into);
	}

	// Conversions
	function date2TH(string $date="", bool $short = false): string {
		global $APP_CONST;
		if (!strlen($date)) $date = date("Y-m-d");
		if (!RegExTest("date", $date)) return $date;
		$BE_year = strval((int)substr($date, 0, 4) + 543);
		$month = (int)substr($date, 5, 2) - 1;
		$TH_month = ($short ? $APP_CONST["TH"]["month_abbr"] : $APP_CONST["TH"]["month"])[$month];
		$day = ltrim(substr($date, 8, 2), "0");
		return "$day $TH_month $BE_year";
	}
	function date2StdTHNum(string $date="", string $delimeter="/"): string {
		$format = explode("/", strlen($date) ? date("d/m/Y", strtotime($date)) : date("d/m/Y"));
		$format[2] = strval((int)$format[2] + 543); 
		return implode($delimeter, $format);
	}
?>