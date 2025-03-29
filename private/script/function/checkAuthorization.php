<?php
	// Included in /private/script/start/PHP.php
	
	$isSignedIn = isset($_SESSION["auth"]["type"]);
	$isAdministrator = ($_SESSION["auth"]["level"] ?? 0) >= 75; # hasPermission($APP_CONST["PERM_MOD_GROUP"], true, 0, false);
	$isDeveloper = has_perm("dev", false); # hasPermission("dev", true, 0, false);
	$requiredSSO = (!isset($_SESSION["auth"]) && isset($_COOKIE["bdSSOv1a"]) && strlen($_COOKIE["bdSSOv1a"]));
	
	function stopAndLeave(string $toPage): never {
		header("Location: $toPage"); exit(0);
	}
	function isAllowedToViewPage(): void {
		global $APP_CONST, $signinURL, $isSignedIn, $isAdministrator, $isDeveloper, $requiredSSO;

		$completeURL = $APP_CONST["baseURL"] . "account/complete";
		if (!preg_match("/^(".str_replace("/", "\\/", $APP_CONST["baseURL"])."|".str_replace("/", "\\/", $signinURL).")$/", $_SERVER["REQUEST_URI"]))
			$completeURL .= "?return_url=".urlencode(preg_replace("/^".str_replace("/", "\\/", $APP_CONST["baseURL"])."/", "", $_SERVER["REQUEST_URI"]));
		# (v\d+\/)?
		$pageURL = explode("?", preg_replace("/^".str_replace("/", "\\/", $APP_CONST["baseURL"])."/", "", $_SERVER["REQUEST_URI"]))[0];
		$guestPage = preg_match("/^(account\/sign-in(-v\d+)?)?$/", $pageURL); # |_resx\/static\/.+
		$publicPage = preg_match("/^d\/(sandbox\/.*|css|font)$/", $pageURL);
		$reservedPage = preg_match("/^((s|t|m|d|v\d+|project)\/.*|(v\d+\/)?service\/(app\/file-share\/|(4\/TianTcl\/)?dark-reg\/.*)|account\/(complete|my)|p\/manual\/.+|e\/enroll\/(M4|report)\/.*)$/", $pageURL);
		$anyPage = preg_match("/^(project\/.+|e\/.*|(|p|account|resource|(v\d+\/)?service|_resx)\/.+|archive(d\/\d{10})?|user\/(\d{5}|[A-Z0-9a-z._]{3,30})(\/(edit|avatar))?|go)$/", $pageURL);

		if ($requiredSSO) return;
		if (!$isSignedIn && $reservedPage && !$publicPage) stopAndLeave($signinURL);
		else if ($_SESSION["auth"]["req_CP"] ?? false && !preg_match("/^account\/complete(\?return_url=.+)?$/", $pageURL)) {
			if (!preg_match("/^e\/enroll\/.*$/", $pageURL)) stopAndLeave($completeURL);
		} else if ($isSignedIn) {
			$user_type = $_SESSION["auth"]["type"];
			$redirect = $guestPage;
			if (!$redirect && !$anyPage && !$publicPage) switch ($user_type) {
				case "s": {
					if (preg_match("/^\/e\/enroll\//", $pageURL) && !preg_match("/^\/e\/enroll\/(M4\/.*|statistics\/\d{4}|\d{4}|resource\/upload\/view)?$/", $pageURL))
						{ $redirect = true; $redirectURL = "e/enroll/M4/"; }
					else if (!preg_match("/^(v\d+\/(s\/.+)?|(s)\/.*)$/", $pageURL)) $redirect = true;
				break; }
				case "t": {
					if ((!$isAdministrator && !$isDeveloper &&	!preg_match("/^(v\d+\/|(t)\/.*)$/", $pageURL)) ||
						(!$isAdministrator && $isDeveloper &&	!preg_match("/^(v\d+\/|(t|d)\/.*)$/", $pageURL)) ||
						($isAdministrator && !$isDeveloper &&	!preg_match("/^(v\d+\/|(t|m)\/.*)$/", $pageURL)) ||
						($isAdministrator && $isDeveloper &&	!preg_match("/^(v\d+\/|(t|m|d)\/.*)$/", $pageURL))) $redirect = true;
					else if (preg_match("/^\/e\/enroll\/.*$/", $pageURL) && !preg_match("/^\/e\/enroll\/(report\/.*|statistics\/\d{4}|\d{4})?$/", $pageURL))
						{ $redirect = true; $redirectURL = "e/enroll/report/"; }
				break; }
			} if ($redirect) {
				if (isset($_GET["return_url"]))	stopAndLeave($APP_CONST["baseURL"].$_GET["return_url"]);
				else if (isset($_GET["next"]))	stopAndLeave($APP_CONST["baseURL"].$_GET["next"]);
				else if (isset($redirectURL))	stopAndLeave($APP_CONST["baseURL"].$redirectURL);
				stopAndLeave($APP_CONST["baseURL"]."v2/");
			}
		}
	}
?>