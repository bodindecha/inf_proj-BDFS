<?php
	if (!isset($_SESSION)) session_start();

	$signinURL = $APP_CONST["baseURL"] . "account/sign-in-v3";
	if (
		$_SERVER["REQUEST_URI"] <> $APP_CONST["baseURL"]."v2/" &&
		!RegExTest("/^".str_replace("/", "\\/", $APP_CONST["baseURL"])."((v2\/)?[st]\/?||account\/sign-in(-v\d+)?(\?return_url=(s|t)(%2F)?)?)?$/", $_SERVER["REQUEST_URI"])
	) $signinURL .= "#next=".urlencode(preg_replace("/^".str_replace("/", "\\/", $APP_CONST["baseURL"])."/", "", $_SERVER["REQUEST_URI"]));

	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	/**
	 * Permissions:
	 * 0: No permission (NULL)
	 * 1: Read
	 * 2: Write
	 * 4: Edit
	 * 8: Overwrite (Bypass all)
	 * 
	 * Stored as sum-char (Min: 0, Max: 15)
	 * Save-as: dechex($lvl)
	 * Read-as: hexdec($lvl)
	 **/
	/**
	 * Check wether user has the required permission
	 * 
	 * @param scopes {string} The only tag required
	 * @param scopes {array} Tags required
	 * @param scopes {array associative} Permissions required at each level
	 * @param useAnd {boolean} Wether and or or when handling multiple scopes (default to true)
	 * @param mods {boolean} If user is op then allow (default to true)
	 * @param denyTo {int} Error page code to response when user has no permission (0 = No redirect) (default to 0)
	 * @return {boolean} Access grant / deny
	 **/
	function hasPermission($scopes=null, bool $useAnd=true, bool $mods=true, $denyTo=null): bool {
		global $APP_CONST, $APP_DB, $APP_USER, $APP_RootDir;

		// Data clean up
		$onlySignedInSearch = empty($APP_USER);
		if (gettype($scopes) == "NULL") $onlySignedInSearch = true;
		else if (gettype($scopes) == "string") {
			if (!strlen($scopes)) $onlySignedInSearch = true;
			else $scopes = array($scopes);
		} else if (!count($scopes)) $onlySignedInSearch = true;

		// Only check if user is signed in
		$pass = $onlySignedInSearch ? isset($_SESSION["auth"]) : $useAnd;
		if ($onlySignedInSearch) goto sendSolution;

		// Connect to database
		if (!isset($APP_DB)) require($APP_RootDir."private/script/function/database.php");
		$permission_table = "user_permission";
		$user_field = "idcode";

		if ($mods && hasPermission($APP_CONST["PERM_MOD_GROUP"], false, false)) return true;

		// Cache check
		$cached = TianTcl::sessVar("user_perm");
		if (!$cached || time() - $cached["loadedAt"] > $APP_CONST["PERM_CACHE_DUR"]) {
			$search = $APP_DB[0] -> query("SELECT * FROM $permission_table WHERE $user_field=$APP_USER");
			$result = $search -> fetch_array(MYSQLI_ASSOC);
			TianTcl::sessVar("user_perm", array(
				"permissions" => $result,
				"loadedAt" => time()
			));
		} else $result = $cached["permissions"];
		// Tag Permission
		if (!isAssocArr($scopes)) {
			$userGroups = explode(",", $result["user_group"]);
			foreach ($scopes as $scope) {
				if (($hasThisPerm = in_array($scope, $userGroups)) && !$useAnd) {
					$pass = true;
					break;
				} else if (!$hasThisPerm && $useAnd) {
					$pass = false;
					break;
				}
			}
		} // lvl Permission
		else {
			if (!function_exists("shortPermissionTag2Level")) { function shortPermissionTag2Level(string $shorthand): int {
				global $APP_CONST;
				$lvl = 0;
				$shorthand = strtolower(trim($shorthand));
				for ($pos = 0; $pos < count($APP_CONST["PERM_TYPES"]); $pos++)
					if (str_contains($shorthand, $APP_CONST["PERM_TYPES"][$pos])) $lvl += pow(2, $pos);
				return $lvl;
			} }
			foreach ($scopes as $scope => $level) {
				if (gettype($level) == "string") $level = shortPermissionTag2Level($level);
				if (($hasThisPerm = ((hexdec($result[$scope] ?? 0) & $level) == $level)) && !$useAnd) {
					$pass = true;
					break;
				} else if (!$hasThisPerm && $useAnd) {
					$pass = false;
					break;
				}
			}
		}

		sendSolution:
		if (!$pass && $denyTo <> null) TianTcl::http_response_code($denyTo);
		return $pass;
	}
	/**
	 * Get all permissions user has for that scope
	 * 
	 * @param scope {string} The only scope to focus
	 * @param mods {boolean} If user is op then allow (default to true)
	 * @return {boolean|string} Is logged-in / list of permissions
	 **/
	function getPermission($scope=null, bool $mods=true) {
		global $APP_CONST, $APP_DB, $APP_USER, $APP_RootDir;

		// Data clean up
		$onlySignedInSearch = empty($APP_USER);
		if (gettype($scope) == "NULL" || (gettype($scope) == "string" && !strlen($scope))) $onlySignedInSearch = true;

		// Only check if user is signed in
		if ($onlySignedInSearch) return isset($_SESSION["auth"]);

		// Connect to database
		if (!isset($APP_DB)) require($APP_RootDir."private/script/function/database.php");
		$permission_table = "user_perm";
		$user_field = "refID";

		# if (!defined("APP_PERM_BYPASS_LVL")) define("APP_PERM_BYPASS_LVL", shotPermissionTag2Level("o"));
		if ($mods && hasPermission($APP_CONST["PERM_MOD_GROUP"], false, false)) return implode("", $APP_CONST["PERM_TYPES"]);

		// Cache check
		$cached = TianTcl::sessVar("user_perm");
		if (!$cached || time() - $cached["loadedAt"] > $APP_CONST["PERM_CACHE_DUR"]) {
			$search = $APP_DB[0] -> query("SELECT * FROM $permission_table WHERE $user_field=$APP_USER");
			$result = $search -> fetch_array(MYSQLI_ASSOC);
			TianTcl::sessVar("user_perm", array(
				"permissions" => $result,
				"loadedAt" => time()
			));
		} else $result = $cached["permissions"];
		// Convert level to short tag
		$level = hexdec($result[$scope] ?? 0);
		$shorthand = "";
		for ($pos = 0; $pos < count($APP_CONST["PERM_TYPES"]); $pos++)
			if (($level & pow(2, $pos)) == pow(2, $pos)) $shorthand .= $APP_CONST["PERM_TYPES"][$pos];
		return strlen($shorthand) ? $shorthand : false;
	}
	// Permission checks
	function has_perm($what, $mods = true) {
		if (!(isset($_SESSION["auth"]) && $_SESSION["auth"]["type"]=="t")) return false;
		$mods = ($mods && $_SESSION["auth"]["level"]>=75); $perm = (in_array("*", $_SESSION["auth"]["perm"]) || in_array($what, $_SESSION["auth"]["perm"]));
		return ($perm || $mods);
	}
?>