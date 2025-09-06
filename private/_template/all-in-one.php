<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_REQUEST)) {
		require_once($APP_RootDir."private/script/start/API.php");
		API::initialize();
		// Execute
		switch (API::$action) {
			case "": {
				switch (API::$command) {
					case "": {

					break; }
					default: API::errorMessage(1, "Invalid command"); break;
				}
			break; }
			default: API::errorMessage(1, "Invalid type"); break;
		} API::sendOutput();
	}

	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "";
	$header["desc"] = "";
	$header["cover"] = "";
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	
</style>
<script type="text/javascript">
	// const TRANSLATION = location.pathname.substring(AppConfig.baseURL.length).replace(/\/$/, "").replaceAll("/", "+");
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = { API_URL: AppConfig.APIbase + "" };
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;

			sv.inited = true;
		};
		var myFunction = function() {

		};
		return {
			init: initialize,
			myFunction
		};
	}(document));
</script>
<?php $APP_PAGE -> print -> nav(); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?></h2>

	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>