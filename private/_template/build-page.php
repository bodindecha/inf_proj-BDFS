<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
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