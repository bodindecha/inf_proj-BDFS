<?php
	$APP_PAGE = arrayToObject(array(
		"print" => array(
			"head" => function() {
				global $APP_RootDir, $APP_CONST, $APP_USER, $heading, $header, $googleFonts, $requiredSSO; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php");
			},
			"nav" => function($panel = "structure") {
				global $APP_RootDir, $APP_CONST, $signinURL, $APP_USER, $isSignedIn, $isAdministrator, $isDeveloper; ?>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/$panel.php");
			},
			"materials" => function($set = "main", $side_panel = "default") {
				global $APP_RootDir, $APP_CONST, $isSignedIn, $isAdministrator, $isDeveloper; ?>
			<aside class="navigator-tab slider">
				<?php include($APP_RootDir."private/block/core/side-panel/$side_panel.php"); ?>
			</aside>
			<?php require($APP_RootDir."private/block/core/material/$set.php");
			},
			"footer" => function($footer = "default") {
				global $APP_RootDir, $APP_CONST; ?>
			<footer>
				<?php require($APP_RootDir."private/block/core/footer/$footer.php"); ?>
			</footer>
		</app>
	</body>
</html>
			<?php }
		)
	));
?>