<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	$useRedirectRule = false;
	require($APP_RootDir."private/script/start/PHP.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	
	if (!isset($_REQUEST["furl"]) || empty(trim($_REQUEST["furl"]))) TianTcl::http_response_code(902);
	$path = $_REQUEST["furl"];
	if (preg_match("/^\//", $path)) $path = ltrim($path, "/");
	if (!preg_match("/^(static|upload)\/.+\.(png|jpg|jpeg|heic|heif|gif|pdf)(\?.+)?/", $path)) TianTcl::http_response_code(914);
	$path = preg_replace("/(((^|\/)?\.\.?\/)+|\.$)/", "/", $path); // prevent rooting
	$path = $APP_RootDir.$APP_CONST["publicDir"].$APP_CONST["baseURL"]."_resx/$path";

	$extension = pathinfo($path, PATHINFO_EXTENSION);
	if (isset($_REQUEST["name"])) {
		$name = trim($_REQUEST["name"]);
		if (!str_ends_with($name, ".$extension")) $name .= ".$extension";
	} else $name = basename($path);
	if (!file_exists($path)) TianTcl::http_response_code(905);

	$mime = TianTcl::mime_file_type($path);
	// — Start Force Download —
	if (ob_get_contents()) die("Some data has already been output, can't download file");
	header("Content-Description: File Transfer");
	if (headers_sent()) die("Some data has already been output to browser, can't download file");
	header("Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1");
	# header("Cache-Control: public, must-revalidate, max-age=0"); // HTTP/1.1
	header("Pragma: public");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	// force download dialog
	if (strpos(php_sapi_name(), "cgi") === false) {
		# header("Content-Type: $mime", true);
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream", false);
		header("Content-Type: application/download", false);
		header("Content-Type: $mime", false);
		header("Content-Length: ".filesize($path));
	} else header("Content-Type: $mime");
	// use the Content-Disposition header to supply a recommended filename
	header("Content-Disposition: attachment; filename=\"$name\"");
	header("Content-Transfer-Encoding: binary");
	# echo file_get_contents($path);
	fpassthru(fopen($path, "rb")); # readfile($path);
	// — End Force Download —
?>