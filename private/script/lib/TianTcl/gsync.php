<?php
	$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	class gsync {
		private static $default, $is = array(
			"initialized" => false,
			"usable" => false
		), $GOOGLE = array(
			"API_URL" => [
				"https://oauth2.googleapis.com/token",
				"https://admin.googleapis.com/admin/directory/v1/users",
				"https://admin.googleapis.com/admin/directory/v1/groups"
			]
		); CONST OU_root = "/";
		protected static $OU = array(
			"root"		=> "/",
			"student"	=> self::OU_root."school/student",
			"teacher"	=> self::OU_root."school/teacher",
			"employee"	=> self::OU_root."employee",
			"admin"		=> self::OU_root."employee/admin",
			"audit"		=> self::OU_root."employee/audit",
			"founder"	=> self::OU_root."employee/founder",
			"HR"		=> self::OU_root."employee/hr",
			"QA"		=> self::OU_root."employee/qa",
			"merch"		=> self::OU_root."employee/sales",
			"supply"	=> self::OU_root."employee/supply",
			"IT"		=> self::OU_root."employee/tech",
			"labour"	=> self::OU_root."employee/worker",
			"guest"		=> self::OU_root."guest",
			"intern"	=> self::OU_root."intern",
			"system"	=> self::OU_root."system"
		);
		function __construct(string $user) {
			# if (!$this -> is["initialized"]) self::initialize();
		}
		final public static function initialize(): void {
			if (self::$is["initialized"]) return;
			global $APP_CONST, $APP_USER, $APP_RootDir;
			self::$default = $APP_CONST["SECURITY_KEY"];
			self::$GOOGLE["superAdminEmail"] = $APP_CONST["gsync_email"];
			// get service account configuration
			$svcacc_file = $APP_RootDir."private/config/$APP_CONST[gsync_svcacc].secret.json";
			if (file_exists($svcacc_file)) {
				self::$GOOGLE["serviceAccount"] = json_decode(file_get_contents($APP_RootDir."private/config/$APP_CONST[gsync_svcacc].secret.json"), true);
				self::$is["usable"] = true;
			} self::$is["initialized"] = true;
		}

		// Helper functions
		final protected static function buildResponse(bool $success, string|null $message=null, ...$data): array {
			$response = array("success" => $success);
			if ($message) $response["message"] = $message;
			if (count($data)) $response["data"] = $data;
			return $response;
		}
		final protected static function getJWT(int $lifetime=60): array|string {
			if (!self::$is["usable"]) return self::buildResponse(false, "❌ Google Sync is not configured");
			$header = array("alg" => "RS256", "typ" => "JWT");
			$now = time(); $claim = [
				"iss"	=> self::$GOOGLE["serviceAccount"]["client_email"],
				"scope"	=> "https://www.googleapis.com/auth/admin.directory.user",
				"aud"	=> self::$GOOGLE["API_URL"][0],
				"exp"	=> $now + $lifetime,
				"iat"	=> $now,
				"sub"	=> self::$GOOGLE["superAdminEmail"]
			]; // Build JWT
			$jwtToSign = base64url_encode(json_encode($header)).".".base64url_encode(json_encode($claim));
			// Sign JWT with private key
			$privateKey = openssl_pkey_get_private(self::$GOOGLE["serviceAccount"]["private_key"]);
			openssl_sign($jwtToSign, $signature, $privateKey, "sha256WithRSAEncryption");
			$jwt = "$jwtToSign.".base64url_encode($signature);
			// ====== EXCHANGE JWT FOR ACCESS TOKEN ======
			$tokenResponse = file_get_contents(self::$GOOGLE["API_URL"][0], false, stream_context_create([
				"http" => [
					"method"	=> "POST",
					"header"	=> "Content-Type: application/x-www-form-urlencoded\r\n",
					"content"	=> http_build_query([
						"grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
						"assertion" => $jwt
					])
				]
			])); $tokenData = json_decode($tokenResponse, true);
			$accessToken = $tokenData["access_token"] ?? null;
			if (!$accessToken) return self::buildResponse(false, "❌ Failed to get access token");
			return $accessToken;
		}
		final public static function updateUser(string $user, array|null $data=null, string $path="/{user}", $method="PUT"): array {
			if (!self::$is["usable"]) return self::buildResponse(false, "❌ Google Sync is not configured");
			$path = str_replace("{user}", $user, $path);
			$ch = curl_init(self::$GOOGLE["API_URL"][1].$path);
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_HTTPHEADER => [
					"Authorization: Bearer ".self::getJWT(),
					"Content-Type: application/json"
				]
			); if ($data) $options[CURLOPT_POSTFIELDS] = json_encode($data);
			curl_setopt_array($ch, $options);
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($httpCode == 200) return self::buildResponse(true, "✅ User updated successfully", json_decode($response, true));
			return self::buildResponse(false, "❌ Failed to update user", $httpCode, json_decode($response, true));
		}
		final public static function updateGroup(string $group, array|null $data=null, string $path="/{group}", string $method=""): array {
			if (!self::$is["usable"]) return self::buildResponse(false, "❌ Google Sync is not configured");
			$path = str_replace("{group}", $group, $path);
			$ch = curl_init(self::$GOOGLE["API_URL"][2].$path);
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_HTTPHEADER => [
					"Authorization: Bearer ".self::getJWT(),
					"Content-Type: application/json"
				]
			); if ($data) $options[CURLOPT_POSTFIELDS] = json_encode($data);
			curl_setopt_array($ch, $options);
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($httpCode == 200) return self::buildResponse(true, "✅ Group updated successfully", json_decode($response, true));
			return self::buildResponse(false, "❌ Failed to update group", $httpCode, json_decode($response, true));
		}

		// User functions
		final public static function createUser(string $email, string $name_first, string $name_last, string $password, string $department, array|null $other=null): array {
			if (!array_key_exists($department, self::$OU)) return self::buildResponse(false, "❌ Invalid department: $department");
			$data = array(
				"primaryEmail" => $email,
				"name" => array(
					"givenName" => $name_first,
					"familyName" => $name_last
				),
				"password" => md5($password),
				"hashFunction" => "MD5",
				"orgUnitPath" => self::$OU[$department],
			); if ($other) {
				if (isset($other["phone"])) $data["phones"] = [array(
					"type" => "mobile",
					"value" => $other["phone"]
				)]; if (isset($other["employeeID"])) $data["organizations"] = [array(
					"employeeNumber" => $other["employeeID"]
				)]; 
			} return self::updateUser("", $data, "", "POST");
		}
		final public static function deleteUserPhoto(string $user): array {
			return self::updateUser($user, null, "/{user}/photos/thumbnail", "DELETE");
		}
		final public static function getUser(string $user): array {
			return self::updateUser($user, null, "/{user}", "GET");
		}
		final public static function getUserPhoto(string $user): array {
			return self::updateUser($user, null, "/{user}/photos/thumbnail", "GET");
		}
		final public static function moveUserOU(string $user, string $department): array {
			if (!array_key_exists($department, self::$OU)) return self::buildResponse(false, "❌ Invalid department: $department");
			return self::updateUser($user, array("orgUnitPath" => self::$OU[$department]));
		}
		final public static function setUserEmail(string $user, string $email): array {
			return self::updateUser($user, array("primaryEmail" => $email));
		}
		final public static function setUserName(string $user, string|array $names, string|null $name2=null): array {
			$changes = array();
			if (gettype($names) == "string") {
				if (substr_count($names, " ")) $names = explode(" ", $names);
				else {
					$changes = array("givenName" => $names);
					if (gettype($name2) == "string") $changes["familyName"] = $name2;
				}
			} if (gettype($names) == "array") {
				if (!isAssocArr($names)) {
					$changes = array("givenName" => $names[0]);
					if (isset($names[1])) {
						$changes["familyName"] = $names[1];
						if (!$names[0]) unset($changes["givenName"]);
					}
				} else $changes = $names;
			} return self::updateUser($user, array("name" => $changes));
		}
		final public static function setUserPassword(string $user, string $password): array {
			return self::updateUser($user, array(
				"password" => md5($password),
				"hashFunction" => "MD5"
			));
		}
		final public static function setUserPhone(string $user, string $phone): array {
			return self::updateUser($user, array("phones" => [array(
				"type" => "mobile",
				"value" => $phone
			)]));
		}
		final public static function setUserPhoto(string $user, string $photo_path): array {
			global $APP_RootDir;
			$photo = $APP_RootDir."private/bucket/user/$photo_path";
			if (!file_exists($photo)) return self::buildResponse(false, "❌ Photo file not found: $photo_path");
			return self::updateUser($user, array(
				"photoData" => base64url_encode(file_get_contents($photo))
			), "/photos/thumbnail");
		}
		final public static function suspendUser(string $user): array {
			return self::updateUser($user, array("suspended" => true));
		}
		final public static function unsuspendUser(string $user): array {
			return self::updateUser($user, array("suspended" => false));
		}

		// Groups functions
		final public static function createGroup(string $name, string $email, string|null $description=null): array {
			$data = array(
				"name" => $name,
				"email" => $email
			); if ($description) $data["description"] = $description;
			return self::updateGroup("", $data, "", "POST");
		}
		final public static function addGroupMember(string $group, string $user, string $role="MEMBER"): array {
			if (!in_array($role, ["MEMBER", "MANAGER", "OWNER"])) return self::buildResponse(false, "❌ Invalid role: $role");
			return self::updateGroup($group, array("email" => $user, "role" => $role), "/{group}/members", "POST");
		}
		final public static function deleteGroupMember(string $group, string $user): array {
			return self::updateGroup($group, null, "/{group}/members/$user", "DELETE");
		}
		final public static function setGroupDescription(string $group, string $desc): array {
			return self::updateGroup($group, array("description" => $desc));
		}
		final public static function setGroupEmail(string $group, string $email): array {
			return self::updateGroup($group, array("email" => $email));
		}
		final public static function setGroupName(string $group, string $name): array {
			return self::updateGroup($group, array("name" => $name));
		}
		final public static function setGroupMemberRole(string $group, string $user, string $role): array {
			if (!in_array($role, ["MEMBER", "MANAGER", "OWNER"])) return self::buildResponse(false, "❌ Invalid role: $role");
			return self::updateGroup($group, array("role" => $role), "/{group}/members/$user");
		}
	} gsync::initialize();

	class gsyncUser extends gsync {
		private $user;
		function __construct(string|null $user=null) {
			$this -> user = $user;
		}

		// Helper functions
		final public function update(array $data): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::updateUser($this -> user, $data);
		}

		// Functional functions
		final public function addToGroup(string $group, string $role="MEMBER"): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::addGroupMember($group, $this -> user, $role);
		}
		final public function create(string $email, string $name_first, string $name_last, string $password, string $department, array|null $other=null): array {
			if ($this -> user) return parent::buildResponse(false, "❌ User has already been specified");
			$resp = parent::createUser($email, $name_first, $name_last, $password, $department, $other);
			if ($resp["success"]) $this -> user = $email;
			return $resp;
		}
		final public function deleteFromGroup(string $group): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::deleteGroupMember($group, $this -> user);
		}
		final public function deletePhoto(): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::deleteUserPhoto($this -> user);
		}
		final public function get(): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::getUser($this -> user);
		}
		final public function getPhoto(): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::getUserPhoto($this -> user);
		}
		final public function moveOU(string $department): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::moveUserOU($this -> user, $department);
		}
		final public function setEmail(string $email): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			$resp = parent::setUserEmail($this -> user, $email);
			if ($resp["success"]) $this -> user = $email;
			return $resp;
		}
		final public function setName(string|array $names, string|null $name2=null): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::setUserName($this -> user, $names, $name2);
		}
		final public function setPassword(string $password): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::setUserPassword($this -> user, $password);
		}
		final public function setPhone(string $phone): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::setUserPhone($this -> user, $phone);
		}
		final public function setPhoto(string $photo_path): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::setUserPhoto($this -> user, $photo_path);
		}
		final public function suspend(): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::suspendUser($this -> user);
		}
		final public function unsuspend(): array {
			if (!$this -> user) return parent::buildResponse(false, "❌ User not specified");
			return parent::unsuspendUser($this -> user);
		}
	}

	class gsyncGroup extends gsync {
		private $group;
		function __construct(string|null $group=null) {
			$this -> group = $group;
		}

		// Helper functions
		final public function update(array $data): array {
			if (!$this -> group) return parent::buildResponse(false, "❌ Group not specified");
			return parent::updateGroup($this -> group, $data);
		}

		// Functional functions
		final public function create(string $name, string $email, string|null $description=null): array {
			if ($this -> user) return parent::buildResponse(false, "❌ Group has already been specified");
			$resp = parent::createGroup($name, $email, $description);
			if ($resp["success"]) $this -> group = $email;
			return $resp;
		}
		final public function addMember(string $user, string $role="MEMBER"): array {
			if (!$this -> group) return parent::buildResponse(false, "❌ Group not specified");
			return parent::addGroupMember($this -> group, $user, $role);
		}
		final public function deleteMember(string $user): array {
			if (!$this -> group) return parent::buildResponse(false, "❌ Group not specified");
			return parent::deleteGroupMember($this -> group, $user);
		}
		final public function setDescription(string $desc): array {
			return parent::setGroupDescription($this -> group, $desc);
		}
		final public function setEmail(string $email): array {
			return parent::setGroupEmail($this -> group, $email);
		}
		final public function setName(string $name): array {
			return parent::setGroupName($this -> group, $name);
		}
		final public function setMemberRole(string $user, string $role): array {
			if (!$this -> group) return parent::buildResponse(false, "❌ Group not specified");
			return parent::setGroupMemberRole($this -> group, $user, $role);
		}
	}
?>