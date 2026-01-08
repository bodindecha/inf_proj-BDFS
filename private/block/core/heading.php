		<?php require_once($APP_RootDir."private/config/heading.php"); ?>
		<!-- Settings -->
		<meta charset="UTF-8" />
		<title><?=$heading["title"]?></title>
		<meta name="description" content="<?=$heading["desc"]?>" />
		<meta name="author" content="Tecillium (UFDT)" />
		<link rel="icon" href="<?=$heading["favicon"]?>" />
		<link rel="shortcut icon" href="<?=$heading["favicon"] ?>" />
		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, viewport-fit=cover" />
		<!-- <base href="<?=$APP_CONST["baseURL"]?>" /> -->
		<?php if ($_SERVER["REQUEST_URI"] != $APP_CONST["baseURL"]."error/904") echo '<noscript><meta http-equiv="refresh" content="0; '.$APP_CONST["baseURL"].'error/904"></noscript>'; ?>
		<!-- Twitter card sharing prepare -->
		<meta name="twitter:card" content="summary_large_image" />
		<!meta name="twitter:site" content="@TianTcl" />
		<meta name="twitter:creator" content="@TianTcl" />
		<meta name="twitter:title" content="<?=$heading["title"]?>" />
		<meta name="twitter:description" content="<?=$heading["desc"]?>" />
		<meta name="twitter:image" content="<?=$heading["cover"]?>" />
		<meta name="twitter:app:country" content="th" />
		<meta name="twitter:app:name:ipad" content="<?=$APP_CONST["name"]?>" />
		<meta name="twitter:app:name:iphone" content="<?=$APP_CONST["name"]?>" />
		<meta name="twitter:app:name:googleplay" content="<?=$APP_CONST["name"]?>" />
		<!-- Link sharing prepare -->
		<meta property="og:title" content="<?=$heading["title"]?>" />
		<meta property="og:description" content="<?=$heading["desc"]?>" />
		<meta property="og:image" content="<?=$heading["cover"]?>" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:locale:alternate" content="th_th" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="<?=$APP_CONST["domain"].$APP_CONST["baseURL"]?>" />
		<meta property="og:site_name" content="<?=$APP_CONST["name"]?>" />
		<meta property="article:publisher" content="<?=$APP_CONST["domain"].$APP_CONST["baseURL"]?>" />
		<meta property="article:modified_time" content="2023-04-10T00:00:00+07:00" />
		<!-- Third parties app setup --><?php
		if (strlen($heading["3rdParty"]["Facebook-App_ID"])) echo '<meta property="fb:app_id" content="'.$heading["3rdParty"]["Facebook-App_ID"].'" />';
		if (strlen($heading["3rdParty"]["Google-Site_Verification"])) echo '<meta name="google-site-verification" content="'.$heading["3rdParty"]["Google-Site_Verification"].'" />';
		if (strlen($heading["3rdParty"]["Google-Tag_ID"])) echo '<script async src="https://www.googletagmanager.com/gtag/js?id='.$heading["3rdParty"]["Google-Tag_ID"].'"></script>'.
			'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$heading["3rdParty"]["Google-Tag_ID"].'" style="display: none;"></iframe></noscript>';
		?>
		<!-- Android standalone A2HS webapp prepare -->
		<link rel="manifest" href="<?=$APP_CONST["baseURL"]?>_resx/static/config/application.webmanifest" crossorigin="use-credentials" />
		<!link rel="manifest" href="<?=$APP_CONST["baseURL"]?>_resx/static/config/as-extension.json" />
		<meta name="application-name" content="<?=$APP_CONST["name"]?>" />
		<meta name="theme-color" content="<?=$heading["themeColor"]?>" />
		<!-- iOS standalone A2HS webapp prepare -->
		<meta name="apple-mobile-web-app-title" content="<?=$APP_CONST["name"]?>" />
		<link rel="apple-touch-startup-image" href="<?=$heading["favicon"]?>" />
		<meta name="apple-mobile-web-app-status-bar-style" content="<?=$heading["themeColor"]?>" />
		<link rel="apple-touch-icon" href="<?=$heading["cover"]?>" />
		<meta name="mobile-web-app-capable" content="yes" />
		<link rel="canonical" href="<?=$APP_CONST["domain"].$APP_CONST["baseURL"]?>" />
		<!-- Resources loading -->
		<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/core/appstyle.min.css" crossorigin="anonymous" />
		<link rel="stylesheet" href="<?=$APP_CONST["baseURL"]?>_resx/static/style/core/stylevar.css" />
		<link rel="stylesheet" href="<?=$APP_CONST["baseURL"]?>_resx/static/style/core/customize.css" />
		<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/core/appobj.min.css" crossorigin="anonymous" />
		<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/core/forms.min.css" crossorigin="anonymous" />
		<link rel="stylesheet" href="<?=$APP_CONST["baseURL"]?>_resx/static/style/core/template.css" />
		<link rel="stylesheet" href="<?=$APP_CONST["baseURL"]?>_resx/static/style/core/components.css" />
		<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/core/styling.min.css" crossorigin="anonymous" />
		<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/lib/prism.min.css" crossorigin="anonymous" />
		<link rel="stylesheet" href="<?=$APP_CONST["baseURL"]?>_resx/static/style/core/components.css" />
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/jQuery/main.min.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/core/Utility.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/disable-devtool.min.js" crossorigin="anonymous"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/prism.min.js" crossorigin="anonymous"></script>
		<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>_resx/static/script/core/customize.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/core/appscript.min.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>resource/js/core/sysscript.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/core/seasonal-effect.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/smooth-scroll.min.js" async></script>
		<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>_resx/static/script/ext/scroll-control.js"></script>
		<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/darkreader.min.js" defer crossorigin="anonymous"></script>