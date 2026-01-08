<style type="text/css">
	<?php
		$googleFonts = array(
			"Akaya+Telivigala",
			"Balsamiq+Sans",
			"Bitter",
			"Caladea:wght@700",
			"Charm",
			"Charmonman",
			"Cormorant+Upright:wght@700",
			"Dancing+Script:wght@400;600",
			"Dosis:wght@500",
			"Google+Sans",
			"IBM+Plex+Sans+Thai",
			"IBM+Plex+Mono",
			"Itim",
			"Kanit:wght@200",
			"Kodchasan:wght@200;300",
			"Krub:wght@300",
			"Libre+Barcode+128+Text",
			"Mali:wght@300",
			"Modak",
			"Open+Sans",
			"Oswald:wght@700",
			"Permanent+Marker",
			"Prompt",
			"Quicksand:wght@600",
			"Ranchers",
			"Roboto+Mono",
			"Roboto:wght@300",
			"Sarabun:wght@300",
			"Srisakdi"
		); $googleIcons = array(
			"Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200",
			"Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
		);
		echo '@import url("https://fonts.googleapis.com/css2?family='.implode("&family=", array_merge($googleFonts, $googleIcons)).'&display=swap");';
	?>
	@import url("<?=$APP_CONST["baseURL"]?>_resx/static/style/core/appfont.css");
	@import url("<?=$APP_CONST["cdnURL"]?>static/style/core/tclfont.css");
	@import url("https://fonts.googleapis.com/icon?family=Material+Icons");
	@import url("https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css");
	:root { --CDN: <?=$APP_CONST["cdnURL"]?>; }
</style>
<style type="text/css" class="init"> app[name=main] * { transition: none !important; } </style>
<script type="text/javascript">
	// INITIALIZE
	window.CURRENT_APP_USER = <?=(strlen($APP_USER) ? "\"$APP_USER\"" : "null")?>;
	window.CURRENT_APP_USER_IS_DEVELOPER = <?=intval(has_perm("dev"))?>;
	$(document).ready(function() {
		app.initialize(document);

		// Resizing
		$(window).on("resize", function() {
			$("app[name=main]").css("--window-height", $(window).height().toString()+"px");
		}).trigger("resize");

		// Embedding
		if (self == top) {
			app._oneTime.handleDeveloper();
		} else {
			$("app[name=main]").addClass("embedded");
			$("app[name=main] > header, app[name=main] > .navigator-tab, app[name=main] > footer").remove();
		}

		// App internal
		<?php if ($requiredSSO) { ?>
		(function () {
			var continueParam = "<?=$_GET["return_url"]??""?>",
				continueArg = decodeURIComponent(function() {
					if (location.hash.length <= 1) return "";
					else if (!/(#|&)next=[^&]+($|&)/.test(location.hash)) return "";
					else return location.hash.match(/(#|&)next=[^&]+/)[0].substring(6).trimEnd("&");
				}());
			sys.auth.sso(continueArg.length ? continueArg : continueParam);
		}());
		<?php } ?>

		/* --- 3rd parties --- */
		<?php if (!in_array($APP_USER, $APP_CONST["USER_NO_SHADOW"])) { ?>
		// Google Analytics
		<?php if (strlen($heading["3rdParty"]["Google-Tag_ID"])) { ?>
			window.dataLayer = window.dataLayer || [];
			function gtag() { dataLayer.push(arguments); }
			gtag("js", new Date());	gtag("config", "<?=$heading["3rdParty"]["Google-Tag_ID"]?>"
				<?php if (strlen($APP_USER)) echo ', {user_id: "'.$APP_USER.'"}'; ?>
			);
		<?php } ?>
		// Google Tag Manager
		<?php if (strlen($heading["3rdParty"]["Google-Tag-Manager_ID"])) { ?>
			(function(w,d,s,l,i) {
				w[l]=w[l]||[]; w[l].push({"gtm.start": new Date().getTime(), event: "gtm.js"});
				var f=d.getElementsByTagName(s)[0], j=d.createElement(s), dl=l!="dataLayer"?"&l="+l:"";
				j.async=true; j.src="https://www.googletagmanager.com/gtm.js?id="+i+dl;
				f.parentNode.insertBefore(j,f);
			})(window, document, "script", "dataLayer", "<?=$heading["3rdParty"]["Google-Tag-Manager_ID"]?>");
			<?php if (strlen($APP_USER)) echo 'dataLayer.push({"user_id": "'.$APP_USER.'"});'; ?>
		<?php } ?>
		// Microsoft Clarity
		<?php if (strlen($heading["3rdParty"]["Microsoft-Clarity_ID"])) { ?>
			(function(c,l,a,r,i,t,y) {
				c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
				t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
				y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
			})(window, document, "clarity", "script", "<?=$heading["3rdParty"]["Microsoft-Clarity_ID"]?>");
			<?php if (strlen($APP_USER)) echo 'clarity("set", "user-id", "'.$APP_USER.'");'; ?>
			$("app[name=main] > * :where(form, .form)").attr("data-clarity-unmask", true);
		<?php } ?>
		<?php } ?>

		// When done
		if (typeof after_init === "function") after_init();
	});

	// In-app browsers
	<?php if ($forceExternalBrowser ?? false) echo 'app.IO.openInBrowser();'; ?>
</script>