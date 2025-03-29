<?php
	$heading = array(
		"themeColor" => "#15499A",
		"3rdParty" => array(
			"Facebook-App_ID" => "",
			"Google-Site_Verification" => "",
			"Google-Tag_ID" => "",
			"Google-Tag-Manager_ID" => "",
			"Microsoft-Clarity_ID" => ""
		)
	);
	$heading["title"] = ((isset($header["title"]) && strlen($header["title"])) ? $header["title"]." | " : "").$APP_CONST["name"];
	$heading["desc"] = (isset($header["desc"]) && strlen($header["desc"])) ? str_replace("\"", "'", $header["desc"]) : "B.D.F.S - INF Sandbox";
	$heading["cover"] = (isset($header["cover"]) && strlen($header["cover"])) ? $header["cover"] : $APP_CONST["baseURL"]."_resx/upload/img/brand/logo/avatar.png";
	$heading["favicon"] = $APP_CONST["baseURL"]."_resx/upload/img/brand/favicon.ico";
?>