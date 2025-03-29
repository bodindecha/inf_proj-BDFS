<?php
	if (!isset($_SESSION)) session_start();

	if (!function_exists("strtorandom")) {
		if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
		require_once($APP_RootDir."private/script/function/utility.php");
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	}

	class VirtualToken {
		// เริ่มระบบ virtual-token (โดย TianTcl)
		private $hashSalt;

		private function newHash_salt(): void {
			$this -> hashSalt = rand(1001, 9999);
			TianTcl::sessVar("hash_salt", $this -> hashSalt);
		}
		private function encryptNID(int|string $ID): string {
			return strtorandom(base_convert((intval($ID) + $this -> hashSalt) * $this -> hashSalt, 10, 36));
		}
		private function decryptNID(string $ID): int {
			if (!preg_match("/^[0-9A-Za-z]{4,}$/", $ID)) return "";
			return base_convert(strtolower($ID), 36, 10) / $this -> hashSalt - $this -> hashSalt;
		}
		
		function __construct(bool $forceNewSalt=false) {
			if (empty(TianTcl::sessVar("hash_salt")) || $forceNewSalt) $this -> newHash_salt();
			else $this -> hashSalt = TianTcl::sessVar("hash_salt");
		}

		public function create(int|string $number): string { return $this -> encryptNID($number); }
		public function read(string $str): int { return $this -> decryptNID($str); }
		public function reset(): void { $this -> newHash_salt(); }

	} $vToken = new VirtualToken();
?>